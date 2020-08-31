<?php
/**
 * Created by 村长
 * Date: 2020-08-31
 * Time: 15:12
 */
$file = $argv[1] ?? 'select.sql';
$sql  = file_get_contents($file);
echo $sql;