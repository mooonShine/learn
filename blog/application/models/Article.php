<?php
/**
 * User: lidc
 */
class Model_Article extends Smodel{
    protected $table = 'article';

    /**
     * 文章分页列表
     * */
    public function artListAll($where=array(),$p=1,$size=10)
    {
        $page = ($p - 1) * $size;
        $condition_new = $this->getCondition($where);
        $condition_new["LIMIT"] = array($page, $size);
        $condition_new["ORDER"] = "hits desc";
        $list = $this->select("*",$condition_new);
        $count = $this->count($this->getCondition($where));
        return array("list" => $list, "count" => $count);
    }


    /**
     * 条件处理
     * */
    private function getCondition($where)
    {
        $condition =array();
        if(isset($where['title']) && !empty($where['title']))
        {
            $condition['AMD']['title'] =$where['title'];
        }
        if(isset($where['create_time'])  && !empty($where['create_time']))
        {
            $condition['AND']['create_time'] = $where['create_time'];
        }
        return $condition;
    }

}