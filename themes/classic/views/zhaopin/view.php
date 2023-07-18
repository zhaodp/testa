<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

$this->breadcrumbs=array(
	'Driver Zhaopins'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List DriverZhaopin', 'url'=>array('index')),
	array('label'=>'Create DriverZhaopin', 'url'=>array('create')),
	array('label'=>'Update DriverZhaopin', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DriverZhaopin', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DriverZhaopin', 'url'=>array('admin')),
);

$gender = array( '女', '男');
$marry = array( '已婚', '未婚');
$political_status = array('群众', '无党派人士', '民主党派 ', '团员', '中共党员(含预备党员)');
$edu = array('大专','本科','硕士','博士','MBA','EMBA','中专','中技','高中','初中','其他');
$driver_type = array('A1','A2','A3','B1','B2','C1');
$status = array('全部','已报名', '已通知培训', '已培训考核', '已签约');
$arrCars = array(
'1'=>'微/小型车',
'2'=>'普通轿车',
'3'=>'高档轿车',
'4'=>'商务/大型车辆',
'5'=>'MPV/SUV等'
);

$dataZhaopin = $model->attributes;

$citys = Dict::items('city');
$districts = District::model()->findAll('city_id=:city_id', array(':city_id' => $dataZhaopin['city_id']));
$districts = CHtml::listData($districts,'id','name');
$work_type = Dict::items('work_type');

$serialNumber = $this->getZhaopinQueueNumber($dataZhaopin['id'], $dataZhaopin['city_id']);

$DriverCars = array();
$arrDriverCars = array();
if (!empty($dataZhaopin['driver_cars']))
	$arrDriverCars = explode(',', $dataZhaopin['driver_cars']);
if (!empty($arrDriverCars))
{
	foreach ($arrDriverCars as $car)
	{
		array_push($DriverCars, $arrCars[$car]);
	}
}
$strDriverCars = implode(',', $DriverCars);

?>

<h1>查看 <?php echo $model->name; ?> 的报名信息</h1>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-queue-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>

<table border=1 style="line-height:25px;">
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
<td><?php echo $dataZhaopin['mobile']?></td>
<td>户口所在地:</td>
<td><?php echo $dataZhaopin['domicile']?></td>
<td>居住地区：</td>
<td><?php echo $districts[$dataZhaopin['district_id']]?></td>
</tr>
<tr>
<td>是否需要担保:</td>
<td><?php echo $dataZhaopin['assure'] ? '是' : '否';?></td>
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
<td>紧急联系人姓名:</td>
<td><?php echo $dataZhaopin['contact']?></td>
<td>电话号码:</td>
<td><?php echo $dataZhaopin['contact_phone']?></td>
<td>关系</td>
<td><?php echo $dataZhaopin['contact_relate']?></td>
</tr>
<tr>
<td>居住详细地址：</td>
<td colspan="5"><?php echo $dataZhaopin['address'];?></td>
</tr>
<tr>
<td>熟练驾驶车型：</td>
<td colspan="5"><?php echo $strDriverCars;?></td>
</tr>
<tr>
<td><?php echo $form->labelEx($model, 'experience'); ?></td>
<td colspan="5"><pre><?php echo $dataZhaopin['experience']; ?></pre></td>
</tr>
</table>
<?php $this->endWidget(); ?>