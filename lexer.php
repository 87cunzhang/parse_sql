<?php
/**
 * Created by 村长
 * Date: 2020-08-31
 * Time: 15:12
 */

class Lexer
{
    private $input; // 要解析的sql

    private $pos = -1;  // point to c char

    private $readPos = 0; // point to next char

    private $c; // current char

    const EOF = -1;

    const KEYWORDS = ['select', 'from', 'where', 'or', 'and', 'order', 'by', 'act', 'desc', 'limit'];

    public function __construct(string $input)
    {
        $this->input = $input;
        $this->readChar();
    }

    public function nextToken(): array
    {
        $this->skipBlank();

        switch ($this->c) {
            case '=':
            case '+':
            case '-':
            case '*':
            case '/':
            case '(':
            case ')':
            case '<':
            case '>':
            case ',':
                $token = $this->makeToken($this->c, $this->c);
                $this->readChar();
                break;
            case "'":
                //匹配单引号字符串
                $token = $this->makeToken('str', $this->matchSingleQuotationStr());
                break;
            case '"':
                //匹配双引号字符串
                $token = $this->makeToken('str', $this->matchDoubleQuotationStr());
                break;
            case '`':
                //匹配双引号字符串
                $token = $this->makeToken('id', $this->matchbackQuotationStr());
                break;
            case self::EOF:
                $token = $this->makeToken('eof', $this->c);
                $this->readChar();
                break;
            default:
                //匹配关键字和id
                if ($this->isLiteral($this->c)) {
                    $literal = $this->matchLiteral();
                    $type    = 'id';
                    if (in_array($literal, self::KEYWORDS)) {
                        $type = $literal;
                    }
                    $token = $this->makeToken($type, $literal);
                    break;
                } elseif ($this->isNum($this->c)) {
                    //匹配数字
                    $token = $this->makeToken('num', $this->matchNumber());
                    break;
                }
                throw new Exception('格式错误,now the char is ' . +$this->c);
        }

        return $token;
    }

    private function isLiteral($char)
    {
        return preg_match('#[a-zA-Z_][a-zA-Z_0-9]*#', $char);
    }

    private function isNum($char)
    {
        return preg_match('#^\d$#', $char);
    }

    //匹配单引号字符串
    private function matchSingleQuotationStr()
    {
        //指针后移,跳过起始的单引号
        $this->readChar();
        $str = '';

        while ($this->c != "'" && $this->c != self::EOF) {
            $str .= $this->c;
            $this->readChar();
        }
        $this->expectChar("'");
        return $str;
    }

    //匹配双引号字符串
    private function matchDoubleQuotationStr()
    {
        //指针后移,跳过起始的双引号
        $this->readChar();
        $str = '';

        while ($this->c != '"' && $this->c != self::EOF) {
            $str .= $this->c;
            $this->readChar();
        }
        $this->expectChar('"');
        return $str;
    }

    //匹配`
    private function matchbackQuotationStr()
    {
        //指针后移,跳过起始的`
        $this->readChar();
        $str = '';

        while ($this->c != "`" && $this->c != self::EOF) {
            $str .= $this->c;
            $this->readChar();
        }
        $this->expectChar("`");
        return $str;
    }

    // 期望拿到什么字符
    private function expectChar($char)
    {
        if ($this->c != $char) {
            throw new Exception("expect $char but $this->c give");
        }
        $this->readChar();
    }

    private function matchNumber()
    {
        $start = $this->pos;
        $len   = 0;

        while ($this->isNum($this->c)) {
            $this->readChar();
            $len++;
        }

        return (int)(substr($this->input, $start, $len));
    }

    private function matchLiteral()
    {
        $start = $this->pos;
        $len   = 0;

        while ($this->isLiteral($this->c)) {
            $this->readChar();
            $len++;
        }

        return substr($this->input, $start, $len);
    }

    //指针往后移
    private function readChar()
    {
        $this->pos++;
        $this->readPos++;
        $this->c = $this->input[$this->pos] ?? self::EOF;

    }

    private function skipBlank()
    {
        while (($this->c == ' ' || $this->c == "\n" || $this->c == "\r" || $this->c == "\t") && $this->c != self::EOF) {
            $this->readChar();
        }
    }

    private function makeToken($type, $literal): array
    {
        return ['type' => $type, 'literal' => $literal];
    }
}