<?php
/**
 * Created by 村长
 * Date: 2020-08-31
 * Time: 15:12
 */
include_once('lexer.php');
include_once('parse.php');
$file = $argv[1] ?? 'select.sql';
$sql  = file_get_contents($file);
$lexer = new Lexer($sql);

//while(($token = $lexer->nextToken())['type'] !='eof')
//{
//    var_dump($token);
//}
$parser = new Parser($lexer);
$ast = $parser->buildAst();
var_dump($ast);
//echo json_encode($ast[1]);
