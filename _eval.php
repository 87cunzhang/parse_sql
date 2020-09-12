<?php

/**
 * Created by 村长
 * Date: 2020-09-12
 * Time: 21:57
 */
class _Eval
{
    private $ast;

    private $result;

    private $data = [
        ['num' => 1, 'name' => '张三', 'code' => 30],
        ['num' => 2, 'name' => '李四', 'code' => 30],
        ['num' => 3, 'name' => '王二', 'code' => 10],
    ];

    public function __construct($ast)
    {
        $this->ast = $ast;
    }

    public function _eval()
    {
        $this->filterData();
        $this->evalSelect();
        $this->evalOrder();
        $this->evalLimit();
        return $this->result;
    }

    private function evalOrder()
    {
        $orderAst = $this->ast[2];
        if (!$orderAst) {
            return;
        }
        $result = $this->result;
        foreach ($orderAst['child'] as $subOrder) {
            foreach ($subOrder['child'] as $id) {
                $column_id = array_column($result,$id['child']);
                $sort_flag_arr = self::SORT_FLAG;
                $sort_flag = $sort_flag_arr[$subOrder['attr']];
                array_multisort($column_id,$sort_flag,$result);
            }
        }
        $this->result = $result;
    }

    const SORT_FLAG = [
        'ast'  => SORT_ASC,
        'desc' => SORT_DESC
    ];

    private function evalLimit()
    {
        $limitAst = $this->ast[3];
        if (!$limitAst) {
            return;
        }

        $result      = $this->result;
        $limitResult = [];
        if (count($limitAst['child']) == 1) {
            $offset   = 0;
            $pageSize = $limitAst['child'][0]['child'];
        } else {
            $offset   = $limitAst['child'][0]['child'];
            $pageSize = $limitAst['child'][1]['child'];
        }

        foreach ($result as $key => $item) {
            if ($key >= $offset && $key < $offset + $pageSize) {
                $limitResult[] = $item;
            }
        }

        $this->result = $limitResult;

    }

    private function evalSelect()
    {
        $result    = $this->result;
        $selectAst = $this->ast[0];
        //要筛选的key
        $selectKeys = array_column($selectAst['child'], 'child');
        if (!$result || in_array('*', $selectKeys)) {
            return;
        }
        //所有key
        $allKeys = array_keys($result[0]);
        //要消除的key
        $unsetKeys = array_diff($allKeys, $selectKeys);
        foreach ($result as &$item) {
            foreach ($unsetKeys as $key) {
                unset($item[$key]);
            }
        }
        $this->result = $result;
    }

    private function orderBy()
    {
        $filterData = $this->filterData();
        var_dump($filterData);
        die;
    }

    /**
     * 获取符合要求的记录
     * @return array
     */
    private function filterData()
    {
        $result   = [];
        $data     = $this->data;
        $whereAst = $this->ast[1];
        if (!$whereAst) {
            $result = $data;
        } else {
            foreach ($data as $item) {
                if ((bool)$this->filterItem($item, $whereAst['child'])) {
                    $result[] = $item;
                }
            }
        }
        $this->result = $result;
    }


    private function filterItem($item, $expr)
    {
        switch ($expr['kind']) {
            case 'num':
            case 'str':
                return $expr['child'];
            case 'id':
                return $item[$expr['child']];
            case 'infixExpr':
                $left  = $this->filterItem($item, $expr['child'][0]);
                $right = $this->filterItem($item, $expr['child'][1]);
                $op    = $expr['attr'];
                return $this->calc($left, $op, $right);
            default:
                $this->throwError('invalid attr , now the kind is ' . $expr['kind']);
        }
    }

    private function calc($left, $op, $right)
    {
        switch ($op) {
            case '+':
                return intval($left) + intval($right);
            case '-':
                return intval($left) - intval($right);
            case '*':
                return intval($left) * intval($right);
            case '/':
                return intval($left) / intval($right);
            case '=':
                return $left == $right;
            case '>':
                return intval($left) > intval($right);
            case '<':
                return intval($left) < intval($right);
            case 'and':
                return boolval($left) and boolval($right);
            case 'or':
                return boolval($left) or boolval($right);
            default :
                $this->throwError('invalid op ,now the op is ' . $op);
        }
    }

    private function throwError($msg)
    {
        throw new Exception($msg);
    }

}
