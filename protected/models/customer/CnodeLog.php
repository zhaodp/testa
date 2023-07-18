<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2014/11/13
 * Time: 16:06
 */

class CnodeLog extends CActiveRecord{
    const CONTACT_DRIVER = 1;    //联系司机
    const NO_CONTACT_CUSTOMER = 2;    //联系司机
    const CONTACT_CUSTOMER = 3;    //已联系上客人
    const VLAUE_LOSS = 4;    //估损
    const DIFF_CASE = 5;    //疑难案件
    const LAWSUIT = 6;    //诉讼
    const DEAL = 7;    //处理
    const FINISH = 8;    //完结
    const SEND_SMS = 90;    //发送短信
    const CONFIRM_COMPLAIN = 91;    //确认投诉
    const CLOSE_COMPLAIN = 92;    //关闭投诉
    const CLASS_SET = 93;    //分类设置
    const TRAFFIC_ACCIDENT = 94;    //交通事故案件信息
    const CAR_INSURE = 95;    //客户车辆保险信息
    const ACCIDENT_COST = 96;    //交通事故涉及费用
    const REVERT_COMPLAIN = 97;    //撤销投诉
    const REJECT_COMPLAIN = 98;    //驳回投诉

    public static $pnodeArr=array(
        '0'=>'全部',
        '1'=>'联系司机',
        '2'=>'未联系上客人',
        '3'=>'已联系上客人',
        '4'=>'估损',
        '7'=>'处理',
        '5'=>'疑难案件',
        '6'=>'诉讼',
        '8'=>'完结',
        '90'=>'发送短信',
        '91'=>'确认投诉',
        '92'=>'关闭投诉',
        '93'=>'分类设置',
        '94'=>'交通事故案件信息',
        '95'=>'客户车辆保险信息',
        '96'=>'交通事故涉及费用',
        '97'=>'撤销投诉',
        '98'=>'驳回投诉',
    );

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{cnode_log}}';
    }
    /*
     * 记录操作日志到t_cnode_log
     */
    public function pushCnodeLog($cid,$node,$content){
        $operator_time = date('Y-m-d H:i:s', time());//操作时间
        $operator = Yii::app()->user->id;//操作人

        $cNodeLog = new CnodeLog();
        $cNodeLog->customer_id = $cid;
        $cNodeLog->node = $node;
        $cNodeLog->ptime = $operator_time;
        $cNodeLog->operator = $operator;
        $cNodeLog->pdetail = $content;
        $flag = $cNodeLog->save();
        if($flag){
            return true;
        }else{
            return false;
        }
    }
    public function dealDetails($cid){
        $sql = "SELECT * FROM {{cnode_log}} where customer_id =:cid ORDER BY ptime DESC ";
        $logmodes = CnodeLog::model()->findAllBySql($sql,array(":cid"=>$cid));//根据条件得到符合条件的所有数据(arry里面嵌套对象)
        return $logmodes;
    }

    /**
     * 查询日志，去掉增加分类的日志
     * @param $cid
     * @return mixed
     */
    public function getLogByCid($cid)
    {
        //查询处理日志
        $sql = 'select id, ptime, node from '.self::tableName().' where customer_id='.$cid.' and node!=93';//过滤掉增加分类>的节点
        $log = Yii::app()->db_readonly->createCommand($sql)->queryAll();
        return $log;
    }

    /**
     * 投诉是否结案
     * @param $cid
     * @return bool
     */
    public function isClosed($cid)
    {
        $conditions = 'customer_id=:cid and node=:node';
        $params = array(':cid'=>$cid,':node'=>self::FINISH);
        $log = Yii::app()->db_readonly->createCommand()->select('id')->from(self::tableName())->where($conditions,$params)->queryRow();
        if ($log) {
            return true;
        }
        return false;
    }
} 