<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

$this->breadcrumbs=array(
		'Driver Zhaopins'=>array(
				'index'
		),
		$model->name
);

$this->menu=array(
		array(
				'label'=>'List DriverZhaopin',
				'url'=>array(
						'index'
				)
		),
		array(
				'label'=>'Create DriverZhaopin',
				'url'=>array(
						'create'
				)
		),
		array(
				'label'=>'Update DriverZhaopin',
				'url'=>array(
						'update',
						'id'=>$model->id
				)
		),
		array(
				'label'=>'Delete DriverZhaopin',
				'url'=>'#',
				'linkOptions'=>array(
						'submit'=>array(
								'delete',
								'id'=>$model->id
						),
						'confirm'=>'Are you sure you want to delete this item?'
				)
		),
		array(
				'label'=>'Manage DriverZhaopin',
				'url'=>array(
						'admin'
				)
		)
);

// $gender = array( '女', '男');
$gender=Dict::items('gender');
unset($gender[0]);
// $marry = array( '已婚', '未婚');
$marry=Dict::items('marry');
// $political_status = array('群众', '无党派人士', '民主党派 ', '团员', '中共党员(含预备党员)');
$political_status=Dict::items('political');
//$edu = array('大专','本科','硕士','博士','MBA','EMBA','中专','中技','高中','初中','其他');
$edu=Dict::items('edu');
//$driver_type = array('A1','A2','A3','B1','B2','C1');
$driver_type=Dict::items('driver_type');
//$status = array('全部','已报名', '已通知培训', '已培训考核', '已签约');
$status=Dict::items('driver_status');
// $arrCars = array(
// '1'=>'微/小型车',
// '2'=>'普通轿车',
// '3'=>'高档轿车',
// '4'=>'商务/大型车辆',
// '5'=>'MPV/SUV等'
// );
$arrCars=Dict::items('car_type');
$dataZhaopin=$model->attributes;

//查询状态流水
$recruitment_log=Yii::app()->db->createCommand()->select('*')->from('t_recruitment_log')->where('id_card=:id_card', array(
		':id_card'=>$dataZhaopin['id_card']
))->order('time ASC')->queryAll();

$citys=Dict::items('city');
$districts=District::model()->findAll('city_id=:city_id', array(
		':city_id'=>$dataZhaopin['city_id']
));
$districts=CHtml::listData($districts, 'id', 'name');
$work_type=Dict::items('work_type');

$serialNumber=$this->getRecruitmentQueueNumber($dataZhaopin['id'], $dataZhaopin['city_id']);

$DriverCars=array();
$arrDriverCars=array();
if (!empty($dataZhaopin['driver_cars']))
	$arrDriverCars=explode(',', $dataZhaopin['driver_cars']);
if (!empty($arrDriverCars)) {
	foreach($arrDriverCars as $car) {
		array_push($DriverCars, $arrCars[$car]);
	}
}
$strDriverCars=implode(',', $DriverCars);

?>

<h1>查看 <?php echo $model->name; ?> 的报名信息</h1>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-queue-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>
<input type="button" onclick="pr()" value="打印" />

<table border=1 style="line-height: 25px;">
	<tr>
		<td>报名流水号:</td>
		<td><?php echo $serialNumber?></td>
		<td>所在城市:</td>
		<td><?php echo $citys[$dataZhaopin['city_id']]?></td>
		<td>工作方式:</td>
		<td><?php echo $work_type[$dataZhaopin['work_type']]?></td>
	</tr>
	<tr>
		<td>姓名：</td>
		<td><?php echo $dataZhaopin['name']?></td>
		<td>性别：</td>
		<td><?php echo $gender[$dataZhaopin['gender']]?></td>
		<td>年龄：</td>
		<td><?php echo $dataZhaopin['age']?></td>
	</tr>
	<tr>
		<td>手机号码：</td>
		<td><input type="button" id="phone_one" value="查看电话" onclick="javascript:showPhone(<?php echo $dataZhaopin['mobile']?>);" /> </td>
		<td>户口所在地:</td>
		<td><?php echo $dataZhaopin['domicile']?></td>
		<td>居住地区：</td>
		<td><?php echo isset($districts[$dataZhaopin['district_id']])?$districts[$dataZhaopin['district_id']]:'未知'?></td>
	</tr>
	<tr>
		<td>担保情况:</td>
		<td><?php 
$radioList = array(5=>'无需担保',6=>'担保金',7=>'担保人',8=>'未担保');
if($dataZhaopin['assure']!=''&&$dataZhaopin['assure']!=0&&$dataZhaopin['assure']!=1){
echo $radioList[$dataZhaopin['assure']];
}?></td>
		<td>婚姻状况：</td>
		<td><?php echo $marry[$dataZhaopin['marry']]; ?></td>
		<td>身份证号码：</td>
		<td><?php echo $dataZhaopin['id_card']?></td>
	</tr>
	<tr>
		<td>学历：</td>
		<td><?php echo $edu[$dataZhaopin['edu']]; ?></td>

		<td>专业：</td>
		<td><?php echo $dataZhaopin['pro']; ?></td>
		<td>政治面貌:</td>
		<td><?php echo $political_status[$dataZhaopin['political_status']]; ?></td>
	</tr>
	<tr>
		<td>准驾车型：</td>
		<td><?php echo $driver_type[$dataZhaopin['driver_type']]; ?></td>
		<td>驾驶证号：</td>
		<td><?php echo $dataZhaopin['driver_card']; ?></td>
		<td>驾照申领日期：</td>
		<td><?php echo date("Y-m-d", $dataZhaopin['driver_year']); ?></td>
	</tr>
	<tr>
		<td>IMEI号:</td>
		<td><?php echo $dataZhaopin['imei']?></td>
		<td>推荐人</td>
		<td><?php echo $dataZhaopin['recommender']?></td>
		<td>驾照档案编号：</td>
                <td><?php echo CHtml::encode($dataZhaopin['id_driver_card']); ?></td>
	</tr>
	<tr>
		<td>紧急联系人姓名:</td>
		<td><?php echo $dataZhaopin['contact']?></td>
		<td>电话号码:</td>
		<td><input type="button" id="phone_one" value="查看电话" onclick="javascript:showPhone(<?php echo $dataZhaopin['contact_phone']?>);" /></td>
		<td>关系</td>
		<td><?php echo $dataZhaopin['contact_relate']?></td>
	</tr>
    <tr>
        <td>联系人</td>
        <td><?php echo $dataZhaopin['company_contact']?></td>
        <td>联系方式:</td>
        <td><input type="button" id="phone_one" value="查看电话" onclick="javascript:showPhone(<?php echo $dataZhaopin['company_mobile']?>);" /></td>
        <td>现(前)单位:</td>
        <td><?php echo $dataZhaopin['company']?></td>
    </tr>

	<tr>
		<td>紧急联系人单位：</td>
		<td colspan="5"><?php echo $dataZhaopin['join_company'];?></td>
	</tr><tr>
		<td>居住详细地址：</td>
		<td colspan="5"><?php echo $dataZhaopin['address'];?></td>
	</tr>
	<tr>
		<td>来源渠道：</td>
		<td colspan="5">
<?php 
$src = Dict::items('recruitment_src');
echo $src[$dataZhaopin['src']];
if($dataZhaopin['src']=='8'){ echo ' ['.$dataZhaopin['other_src'].']';}
?></td>
	</tr>
	<tr>
		<td>熟练驾驶车型：</td>
		<td colspan="5"><?php echo $strDriverCars;?></td>
	</tr>
	<tr>
		<td><?php echo $form->labelEx($model, 'experience'); ?></td>
		<td colspan="5"><pre><?php echo $dataZhaopin['experience']; ?></pre></td>
	</tr>
	<tr>
		<td>状态变化日志</td>
		<td colspan="5"><pre>
<?php 
if($recruitment_log){
foreach($recruitment_log as $item){
	echo '<pre>'.date('Y-m-d H:i',$item['time']).'&nbsp;&nbsp;'.$item['message'].'</pre>';
}
}else{ echo '暂无日志';}
?>
</pre></td>
	</tr>
</table>
<?php $this->endWidget(); ?>

<script>
function pr(){
	var css = $("head").html();
	var headstr = "<html><head><title></title></head><body style='margin-top:35px;'><center>";
	var footstr = "</center>";
	var newstr = $("#mydialog").html();
	newstr = newstr.replace(/<h1>.*<\/h1>/,'');
	newstr = newstr.replace(/\<input .*\">/,'');
	
	
	w=window.open("","_blank","k");
	w.document.write(headstr+css+newstr+footstr);
	w.print();
}

function showPhone(phone){
    var _phone = (phone == undefined) ? '未填写联系方式':phone;
	alert(_phone);
}
</script>