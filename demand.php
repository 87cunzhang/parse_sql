<?php
/**
 * 需求
 *  redis k-v
 *  10000 [['price' => 100, 'valid_etime' => '2020-10-01']]
 *  uid_10000 => json_encode([['price' => 100, 'valid_etime' => '2020-10-01']])
 *  app要根据条件筛选
 */

$userCouponList = [
    ['id' => 1, 'price' => 100, 'valid_etime' => '2020-10-01'],
    ['id' => 10, 'price' => 200, 'valid_etime' => '2020-10-02']
];

$sql = 'select valid_etime,id from t 
	where id > 10 or (price+2 = 100) and (valid_etime/2 > "2020-10-01" or id = 10 and price < 10)
	order by id,price desc 
	limit 0,10';
function query(array $userCouponList, string $sql) : array {}
// 1. 词法分析
// 2. 语法分析
// 3. 语法树
// 4. eval

// 1. token: T_SELECT T_FROM T_WHERE T_OR T_AND > < = + - * / ( ) T_ORDER T_BY , T_ASC T_DESC T_LIMIT
// T_ID => [a-zA-Z0-9_]+  `[a-zA-Z0-9_]+`
// T_STR => ".+" '.+'
// T_NUMBER => \d+

// 2. 文法:
// top : T_SELECT fileds T_FROM T_ID where orderby limit
// fileds : T_*
//        | T_ID (, T_ID)*

// where : /* empty */
//       | T_WHERE expr

// orderby : /* empty */
//         | T_ORDER T_BY orderByGroup(,orderByGroup)*

// orderByGroup : T_ID
//              | T_ID T_ASC
//              | T_ID T_DESC

// limit : /* empty */
// 		 | T_LIMIT T_NUMBER (, T_NUMBER)?

// expr : T_NUMBER
//      | T_STR
//      | T_ID
//      | ( expr )
//      | expr + expr
//      | expr - expr
//      | expr * expr
//      | expr / expr
//      | expr > expr
//      | expr < expr
//      | expr = expr
//      | expr T_AND expr
//      | expr T_OR expr