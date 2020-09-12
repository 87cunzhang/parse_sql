<?php
/**
 * @Author zhanghaomin@100tal.com
 * @Time 2020/5/9 3:15 下午
 */

class Parser
{
    /**
     * @var Parser
     */
    private $l;

    private $curToken;

    private $peekToken;


    const KEYWORDS = ['select', 'from', 'where', 'order', 'limit', 'eof'];

    public function __construct(Lexer $l)
    {
        $this->l = $l;
        $this->nextToken();
        $this->nextToken();
    }

    //生成ast
    public function parseSql()
    {
        $ast   = [];
        $ast[] = $this->parseSelect();
        $this->parseFrom();
        $ast[] = $this->parseWhere();
        $ast[] = $this->parseOrder();
        $ast[] = $this->parseLimit();
        return $ast;
    }


    private function parseSelect()
    {
        $selectAst = [
            'kind'  => 'select',
            'child' => []
        ];
        $this->expectToken('select');
        $this->nextToken();//skip select

        if ($this->curTokenType() == '*') {
            $selectAst['child'][] = $this->parseStar();
            $this->nextToken();
        }

        while ($this->curTokenType() != 'from') {

            if ($this->curTokenType() == ',') {
                $this->nextToken();
            } elseif ($this->curTokenType() == 'id') {
                $selectAst['child'][] = $this->parseId();
                $this->nextToken();
            } else {
                $this->throwError('select fields error');
            }

        }

        return $selectAst;
    }

    private function parseFrom()
    {
        $this->expectTokenType('from');
        $this->nextToken();
        $this->expectTokenType('id');
        $this->nextToken();
    }

    private function parseWhere()
    {
        if ($this->curTokenLiteral() != 'where') {
            return [];
        }

        $whereAst = [
            'kind'  => 'where',
            'child' => []
        ];

        $this->nextToken();//skip where
        $whereAst['child'] = $this->parseExpr(0);
        return $whereAst;
    }


    private function parseOrder()
    {
        if ($this->curTokenLiteral() != 'order') {
            return [];
        }

        $this->expectToken('order');
        $this->nextToken();//skip order;
        $this->expectToken('by');
        $this->nextToken();//skip  by
        $orderAst = [
            'kind'  => 'order',
            'child' => []
        ];

        while (!in_array($this->curTokenType(), self::KEYWORDS)) {
            $orderAst['child'][] = $this->parseSubOrder();
        }

        return $orderAst;
    }

    private function parseSubOrder()
    {
        $subOrderAst = [
            'kind'  => 'subOrder',
            'attr'  => '',
            'child' => []
        ];

        while (!in_array($this->curTokenType(), ['asc', 'desc']) && !in_array($this->curTokenType(), self::KEYWORDS)) {

            if ($this->curTokenType() == 'id') {
                $subOrderAst['child'][] = $this->parseId();
                $this->nextToken();
            } elseif ($this->curTokenType() == ',') {
                $this->nextToken();
            }

        }

        if (in_array($this->curTokenLiteral(), ['asc', 'desc'])) {
            $subOrderAst['attr'] = $this->curTokenLiteral();
            $this->nextToken(); //skip ast or desc
        } else {
            $subOrderAst['attr'] = 'ast';
        }

        if ($this->curTokenLiteral() == ',') {
            $this->nextToken();
        }

        return $subOrderAst;

    }


    private function parseLimit()
    {
        if ($this->curTokenLiteral() != 'limit') {
            return [];
        }

        $limitAst = [
            'kind'  => 'limit',
            'child' => []
        ];
        $this->nextToken();//skip limit
        $this->expectTokenType('num');
        $limitAst['child'][] = $this->parseNumber();
        $this->nextToken();

        if ($this->curTokenLiteral() == ',') {
            $this->nextToken(); //skip ,
            $this->expectTokenType('num');
            $limitAst['child'][] = $this->parseNumber();
            $this->nextToken();
        }

        return $limitAst;
    }


    private function parseExpr($precedence)
    {
        $left = '';

        if ($this->curTokenType() == 'num') {
            $left = $this->parseNumber();
        } elseif ($this->curTokenType() == 'str') {
            $left = $this->parseString();
        } elseif ($this->curTokenType() == 'id') {
            $left = $this->parseId();
        } elseif ($this->curTokenType() == '(') {
            $left = $this->parseBrackets();
        } else {
            $this->throwError('token type error ,now the type is' . $this->curTokenType());
        }

        $this->nextToken();

        //遇到下一个关键字停止
        while (!in_array($this->curTokenLiteral(), self::KEYWORDS) && $precedence < $this->curPrecedence()) {
            $left = $this->parseInfixExpr($left);
        }

        return $left;
    }


    private function parseInfixExpr($left)
    {
        $op         = $this->curTokenLiteral();
        $precedence = $this->curPrecedence();
        $this->nextToken();
        return ['kind' => 'infixExpr', 'attr' => $op, 'child' => [$left, $this->parseExpr($precedence)]];
    }


    private function curPrecedence()
    {
        $precedences = [
            'or'  => 100,
            'and' => 200,
            '<'   => 300,
            '>'   => 300,
            '='   => 300,
            '+'   => 400,
            '-'   => 400,
            '*'   => 500,
            '/'   => 500
        ];
        return $precedences[$this->curTokenLiteral()] ?? 0;
    }

    private function curTokenType()
    {
        return $this->curToken['type'];
    }

    private function curTokenLiteral()
    {
        return $this->curToken['literal'];
    }

    private function nextToken($skipNum = 1)
    {
        for ($i = 0; $i < $skipNum; $i++) {
            $this->curToken  = $this->peekToken;
            $this->peekToken = $this->l->nextToken();
        }
    }

    private function expectToken($expectToken)
    {
        $curToken = $this->curTokenLiteral();

        if ($curToken != $expectToken) {
            $this->throwError('expect token ' . $expectToken . ',but ' . $curToken . ' given');
        }
    }

    private function expectTokenType($expectTokenType)
    {
        $curTokenType = $this->curTokenType();

        if ($curTokenType != $expectTokenType) {
            $this->throwError('expect token ' . $expectTokenType . ',but ' . $curTokenType . ' given');
        }
    }

    private function throwError($msg)
    {
        throw new Exception($msg);
    }

    private function parseId()
    {
        $this->expectTokenType('id');
        return ['kind' => 'id', 'child' => $this->curTokenLiteral()];
    }

    private function parseStar()
    {
        $this->expectTokenType('*');
        return ['kind' => '*', 'child' => $this->curTokenLiteral()];
    }

    private function parseNumber()
    {
        $this->expectTokenType('num');
        return ['kind' => 'num', 'child' => $this->curTokenLiteral()];
    }

    private function parseString()
    {
        $this->expectTokenType('str');
        return ['kind' => 'str', 'child' => $this->curTokenLiteral()];
    }

    //解析括号
    private function parseBrackets()
    {
        $this->nextToken();//跳过左括号
        $expr = $this->parseExpr(0);
        $this->nextToken();//跳过右括号
        return $expr;
    }
}