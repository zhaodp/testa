<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-6
 * Time: 下午2:38
 * auther mengtianxue
 */

class RZList extends CRedis
{
    public $host = 'redis02n.edaijia.cn'; //10.161.174.78 redis02n.edaijia.cn
    public $port = 6379;
    public $password = 'k74FkBwb7252FsbNk2M7';


    public static function model($className = __CLASS__)
    {
        $model = null;
        if (isset(self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }


    /**
     * 在列表中添加
     * @param $key
     * @param $score
     * @param $value
     * @return int 返回1
     * @auther mengtianxue
     */
    protected function addList($key, $score, $value)
    {
        $add = $this->redis->zAdd($key, $score, $value);
        return $add;
    }

    /**
     * delKey
     * @param $key
     * @auther mengtianxue
     */
    protected function del($key){
        $del = $this->redis->del($key);
        return $del;
    }

    /**
     * 删除列表中的一项
     * @param $key
     * @param $value int  成功返回1，不成功返回0
     * @auther mengtianxue
     */
    protected function delItem($key, $value)
    {
        $del = $this->redis->zRem($key, $value);
        return $del;
    }

    /**
     * 获取订单ID 列表
     * Range  RevRange 排序有问题，暂时不要用 默认Sort
     * @param $key
     * @param int $pageNo
     * @param int $pageSize
     * @param string $sortType
     * @return array
     * @author bidong 2014-1-16
     */
    protected function getListByPage($key, $pageNo = 0, $pageSize = 10)
    {
        $start = empty($pageNo) ? 0 : ($pageNo * $pageSize);
        $list = array();
        $list = $this->redis->sort($key, array('sort' => 'desc', 'limit' => array($start, $pageSize)));

        return $list;
    }

    /**
     * 获取订单ID 列表，根据Score 排序
     * @param $key
     * @param $startScore  开始序号
     * @param $endScore  结束序号
     * @param string $sortType 排序类型
     * @return array
     * @author bidong 2014-1-19
     */
    protected function getListByScore($key,$startScore,$endScore,$sortType = 'RevRange'){

        $list = array();
        switch ($sortType) {
            case 'Range':
                //按score从小到大排序
                $list = $this->redis->zRange($key, $startScore, $endScore);
                break;
            case 'RevRange':
                //按score从大到小排序
                $list = $this->redis->zRevRange($key, $startScore, $endScore);
                break;
            default:
                break;
        }
        return $list;

    }


    /**
     * 获取列表总数
     * @param $key
     * @return int 如果为空返回0
     * @auther mengtianxue
     */
    protected function getListCount($key)
    {
        $count = $this->redis->zCard($key);
        return $count;
    }

    /**
     * 返回当前值的排序
     * @param $key
     * @param $value
     * @return int
     * @auther mengtianxue
     */
    protected function getRevIndex($key, $value)
    {
        $index = $this->redis->zRevRank($key, $value);
        if ($index != 'nil') {
            return $index;
        } else {
            return 0;
        }
    }

    /**
     * 获取List 最大的Score
     * @param $key
     * @return int
     * @author bidong  2014-1-20
     */
    protected function getMaxScore($key){
        $score= $this->getListCount($key);
        $score=$score+1;
        return intval($score);
    }

}