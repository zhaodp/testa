<?php
/**
 * 查看投诉处理流水
 * User: Bidong
 * Date: 13-6-20
 * Time: 上午10:48
 * To change this template use File | Settings | File Templates.
 */

class ComplainViewStatusAction  extends CAction{
    public function run(){

        if(isset($_GET['cid'])){
            $complain_id=intval(trim($_GET['cid']));

            $criteria=new CDbCriteria;
            $criteria->condition='customer_id=:cid';
            $criteria->params=array(':cid'=>$complain_id);
            $criteria->order='ptime desc';
            $logData = CnodeLog::model()->findAll($criteria);
            $retData=array();
            foreach($logData as $_k=>$_v){
                $temp=array();
                $temp['seq']=$_k+1;   //处理序号
                $temp['node']=CnodeLog::$pnodeArr[$_v->node];   //处理点
                $temp['pdetail']=$_v->pdetail;   //处理详情
                $temp['operator']=$_v->operator;   //处理人
                $temp['ptime']=$_v->ptime;   //处理时间
                $retData[]=$temp;
            }
            $dataProvider=new CArrayDataProvider($retData, array(
                'id'=>'id',
                'sort'=>array(),
                'pagination'=>array(
                    'pageSize'=>20,
                ),
            ));
            $dataProvider->keyField=false;

            //前日志展示
            $criteria=new CDbCriteria;
            $criteria->condition='complain_id=:cid';
            $criteria->params=array(':cid'=>$complain_id);
            $criteria->order='create_time desc';
            $logData2 = CustomerComplainLog::model()->findAll($criteria);

            $retData2=array();
            foreach($logData2 as $log){
                $temp=array();
                $temp['process_type']=CustomerComplainLog::$process_type[$log->process_type];   //处理分类
                $temp['result']=CustomerComplainLog::$process_result[$log->result];;   //处理结果

                $temp['mark']=$log->mark;   //处理备注
                $temp['operator']=$log->operator;   //处理人
                $temp['create_time']=$log->create_time;   //处理时间
                $retData2[]=$temp;
            }
            $dataProvider2=new CArrayDataProvider($retData2, array(
                'id'=>'id',
                'sort'=>array(),
                'pagination'=>array(
                    'pageSize'=>20,
                ),
            ));
            $dataProvider2->keyField=false;
            $this->controller->render('view_status',array('data'=>$dataProvider,'data2'=>$dataProvider2));
        }
    }
}