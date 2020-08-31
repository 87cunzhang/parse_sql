<?php
/**
 * Created by 村长
 * Date: 2020-08-31
 * Time: 15:12
 */

class Lexer
{
    private $input; // 输入的字符串

    private $pos = -1;  // point to c char

    private $readPos = 0; // point to next char

    private $c; // current char

    const EOF = -1;

    const KEYWORDS = ['p','set','function'];

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
            case '{':
            case '}':
            case '@':
                $token = $this->makeToken($this->c, $this->c);
                $this->readChar();
                break;
            case self::EOF:
                $token = $this->makeToken('eof', $this->c);
                $this->readChar();
                break;
            case '"':
                $token= $this->makeToken('str', $this->matchStr());
                break;
            default:
                //匹配变量名和关键字
                if ($this->isLiteral($this->c)) {
                    $literal  = $this->matchLiteral();
                    $type = 'name';
                    if(in_array($literal,self::KEYWORDS)){
                        $type = $literal;
                    }
                    $token = $this->makeToken($type, $literal);
                    break;
                } elseif ($this->isNum($this->c)) {
                    $token = $this->makeToken('num', $this->matchNumber());
                    break;
                }
                throw new Exception('格式错误');
        }

        return $token;
    }

    private function isLiteral($char)
    {
        return preg_match('#^[a-zA-Z_]$#', $char);
    }

    private function isNum($char)
    {
        return preg_match('#^\d$#', $char);
    }

    private function matchStr()
    {
        //指针后移,跳过起始的双引号
        $this->readChar();
        $str = '';

        while ($this->c != '"' && $this->c != self::EOF) {
            $str .= $this->c;
            $this->readChar();
        }
        $this->expectChar();
        return $str;
    }

    // 期望拿到什么字符
    private function expectChar()
    {
        if ($this->c != '"') {
            throw new Exception('双引号未闭合');
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