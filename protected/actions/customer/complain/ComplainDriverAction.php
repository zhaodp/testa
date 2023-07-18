<?php
/**
 * 司机处理页面
 * User: Bidong
 * Date: 13-6-16
 * Time: 下午3:49
 * To change this template use File | Settings | File Templates.
 */

class ComplainDriverAction extends CAction {

    public function run(){
	$status=0;
	$complainType=CustomerComplainType::model()->getComplainTypeByID(0);
	$typeArr=array('-1'=>'全部');
	foreach($complainType as $item){
	    $typeArr[$item->id]=$item->name;
	}

	$criteria=new CDbCriteria;
	$city_id=0;
	$model=new CustomerComplain();
	$params=array();
	if(isset($_GET['search'])){
	    $type=0;
	    if(isset($_GET['sub_type']) && intval($_GET['sub_type'])>0){
		$type=intval($_GET['sub_type']);
	    }elseif(isset($_GET['main_type']) && intval($_GET['main_type'])>0){
		$type=intval($_GET['main_type']);
	    }

	    if($type){
		$criteria->addCondition('complain_type=:type');
		$params[':type']=$type;

		$data=CustomerComplainType::model()->getComplainTypeByID((int)$_GET['complain_maintype']);
		$data=CHtml::listData($data,'id','name');
	    }
	    if(!empty($_GET['driver_id'])){
		$criteria->addCondition('driver_id=:did');
		$params[':did']=trim($_GET['driver_id']);
	    }
	    if(!empty($_GET['source'])){
		$criteria->addCondition('source=:source');
		$params[':source']=trim($_GET['source']);
	    }

	    if(!empty($_GET['city_id'])){
		$city_id=$params[':city_id']=$_GET['city_id'];
		$criteria->addCondition('city_id=:city_id');
		$params[':city_id']=$city_id;
	    }
	}else{
                if (Yii::app()->user->city!=0){
                        $criteria->addCondition('city_id=:city_id');
                        $params[':city_id'] = Yii::app()->user->city;
                }

        if(empty($_GET['dm_process'])){ //默认品监处理
            $status = CustomerComplain::STATUS_CS;
        }
        }

	if(!empty($_GET['dm_process'])){
	    $status = $_GET['dm_process'];
	}

	if(!empty($_GET['dm_process']) && $_GET['dm_process'] == CustomerComplain::STATUS_EFFECT){
	    $criteria->addInCondition('status', array(2,3,4,8));
	    // $v = array(2,3,4,8);
	    $params[':ycp0']=2;
	    $params[':ycp1']=3;
	    $params[':ycp2']=4;
	    $params[':ycp3']=8;
	}else{
	    if($status != 0){
		$criteria->addCondition('status=:s');
		$params[':s']=$status;
	    }
	}
	$criteria->order='id desc';
	$criteria->params=$params;
	
	$dataProvider=new CActiveDataProvider(
	    'CustomerComplain', array(
	            'criteria'=>$criteria,
		    'id'=>'id',
		    'sort'=>array(),
		    'pagination'=>array(
			'pageSize'=>10,
			),
		    ));

	$this->controller->render('driver',array(
		    'model'=>$model,
		    'data'=>$dataProvider,
		    'typelist'=>$typeArr,
		    'city_id'=>$city_id,
		    'status' => $status,
		    'sub_type' => isset($_GET['sub_type']) ? $_GET['sub_type'] : '-1',
		    'complain_maintype' => isset($_GET['complain_maintype']) ? $_GET['complain_maintype'] : '-1',
		    'childTypeList' => isset($data) ? $data : array('-1'=> '全部'),
		    'driver_id' => isset($_GET['driver_id']) ? $_GET['driver_id'] : '',
		    'source' => isset($_GET['source']) ? $_GET['source'] : 0,
		    ));
    }

}
