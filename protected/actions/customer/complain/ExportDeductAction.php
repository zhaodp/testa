<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zengzhihai
 * Date: 13-8-22
 * Time: 下午2:45
 * To change this template use File | Settings | File Templates.
 */
class ExportDeductAction extends CAction{
    public function run(){
        $model = new CustomerComplainRecoup();
        $model::$db = Yii::app()->db_readonly;
        $start_time = $end_time = '';
        $criteria = new CDbCriteria();
        $params = array();
        if(!empty($_GET['driver_id'])){
            $criteria->addCondition('driver_id=:driver_id');
            $params[':driver_id'] =$_GET['driver_id'];
        }

        if(!empty($_GET['process_type'])){
            $criteria->addCondition('process_type=:process_type');
            $params[':process_type'] =$_GET['process_type'];
        }

        if(!empty($_GET['created'])){
            $criteria->addCondition('created=:created');
            $params[':created'] =$_GET['created'];
        }

        if(!empty($_GET['operator'])){
            $criteria->addCondition('operator=:operator');
            $params[':operator'] =$_GET['operator'];
        }

        if(!empty($_GET['start_time'])&&!empty($_GET['end_time'])){
            $start_time = $_GET['start_time'];
            $end_time = $_GET['end_time'];
            $cstr = '';
            if ($start_time) {
                $cstr = 'create_time>=:s_time';
                $params[':s_time'] = $start_time;
            }
            if ($end_time) {
                if ($cstr)
                    $cstr .= ' and create_time<=:e_time';
                $params[':e_time'] = $end_time;
            }
            $criteria->addCondition($cstr);
        }

        if(!empty($_GET['status'])){
            $criteria->addCondition('status=:status');
            $params[':status'] =$_GET['status'];
        }else{
            $criteria->addCondition('status=:status');
            $params[':status'] = 0;
        }

        if(!empty($_GET['recoup_type'])){
            $criteria->addCondition('recoup_type=:recoup_type');
            $params[':recoup_type'] =$_GET['recoup_type'];
        }else{
            $criteria->addCondition('recoup_type=:recoup_type');
            $params[':recoup_type'] = 1;
        }

        $criteria->order='create_time desc';
        $criteria->params = $params;
        $findSearchRecoup=$model->findAll($criteria);

        $filename = 'driver' . time() . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        Header('Accept-Ranges: bytes');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        $RecoupArr = array();
        $RecoupArr['id'] = mb_convert_encoding('订单流水号', 'gbk', 'UTF-8');
        $RecoupArr['recoup_customer'] = mb_convert_encoding('补偿客户', 'gbk', 'UTF-8');
        $RecoupArr['amount_customer'] = mb_convert_encoding('金额(用户)', 'gbk', 'UTF-8');
        $RecoupArr['driver_id'] = mb_convert_encoding('补扣司机工号', 'gbk', 'UTF-8');
        $RecoupArr['amount_driver'] = mb_convert_encoding('金额(司机)', 'gbk', 'UTF-8');
        $RecoupArr['recoup_type'] = mb_convert_encoding('补偿方式', 'gbk', 'UTF-8');
        $RecoupArr['process_type'] = mb_convert_encoding('处理类型', 'gbk', 'UTF-8');
        $RecoupArr['status'] = mb_convert_encoding('分类', 'gbk', 'UTF-8');
        $RecoupArr['operator'] = mb_convert_encoding('操作人', 'gbk', 'UTF-8');
        $RecoupArr['created'] = mb_convert_encoding('操作时间', 'gbk', 'UTF-8');
        $RecoupArr['city'] = mb_convert_encoding('城市', 'gbk', 'UTF-8');
        $RecoupArr['category'] = mb_convert_encoding('分类名称', 'gbk', 'UTF-8');
        $header =  implode(',', $RecoupArr) . "\n";
        echo $header;
        if($findSearchRecoup){
            $complain=new CustomerComplain();
            $complain::$db = Yii::app()->db_readonly;
            $complainType=new CustomerComplainType();
            $complainType::$db = Yii::app()->db_readonly;
            foreach($findSearchRecoup as $key=>$value){
                $ComplainModel=$complain->findByAttributes(array('id'=>$value['complain_id']));
                $complainTypeName=$complainType->findByAttributes(array('id'=>$ComplainModel['complain_type']));
                $temp=$ExportSearchRecoup='';
                $ExportSearchRecoup.=$ComplainModel['order_id'].',';
                $ExportSearchRecoup.=$value['recoup_customer']?$value['recoup_customer'].',':',';
                $ExportSearchRecoup.=mb_convert_encoding("{$value['amount_customer']}", 'gbk', 'UTF-8') . ',';
                $ExportSearchRecoup.=$value['driver_id']?$value['driver_id'].',':',';
                $ExportSearchRecoup.=mb_convert_encoding("{$value['amount_driver']}", 'gbk', 'UTF-8') . ',';
                $ExportSearchRecoup.=$value['recoup_type']==CustomerComplainRecoup::RECOUP_TYPE1?mb_convert_encoding('现金', 'gbk', 'UTF-8') . ',':mb_convert_encoding('优惠券', 'gbk', 'UTF-8').',';
                $temp=CustomerComplainRecoup::$process_type[$value['process_type']];
                $ExportSearchRecoup.=mb_convert_encoding("$temp", 'gbk', 'UTF-8') . ',';
                // $ExportSearchRecoup.=mb_convert_encoding("{$complainTypeName['name']}", 'gbk', 'UTF-8').',';
                $ExportSearchRecoup.=mb_convert_encoding("{$complainTypeName['name']}", 'gbk', 'UTF-8').',';
                $ExportSearchRecoup.=mb_convert_encoding("{$value['created']}", 'gbk', 'UTF-8') . ',';
                $ExportSearchRecoup.=$value['create_time'].',';
                //添加城市和分类名称
                $cate_name = $city_name = '';
                if(!empty($ComplainModel)){
                    $city_name = Dict::item("city",$ComplainModel->city_id);
                }
                $ExportSearchRecoup .= mb_convert_encoding($city_name, 'gbk', 'UTF-8') . ',';
                if(!empty($complainTypeName)){
                    $parentType = $complainType->findByPk($complainTypeName->parent_id);
                    if(!empty($parentType))
                        $cate_name = $parentType->name;
                }
                $ExportSearchRecoup .=  mb_convert_encoding($cate_name, 'gbk', 'UTF-8') . ',';

                $ExportSearchRecoup.= '' . "\n";
                echo $ExportSearchRecoup;
            }
        }
    }
}