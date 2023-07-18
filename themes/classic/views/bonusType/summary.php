<div class="search-form">
	<?php 
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); 
	?>
	<div class="row-fluid">
		<div class="span3">
		<?php echo CHtml::label('地区','city_id');?>
		<?php $city_id = isset($_REQUEST['city_id']) ? intval($_REQUEST['city_id']):0;?>
		<?php echo CHtml::dropDownList('city_id', $city_id,Dict::items('city')); ?>
		</div>
		<div class="span3">
		<?php echo CHtml::label('司机工号','driver_id'); ?>
		<?php $driver_id = isset($_REQUEST['driver_id']) ? trim($_REQUEST['driver_id']) : '';?>
		<?php echo CHtml::textField('driver_id', $driver_id);?>
		</div>
		<div class="span3">
			<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
		</div>
	</div>
</div>
<?php $this->endWidget(); ?>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-bonus',
	'itemsCssClass'=>'table table-striped',
	'pagerCssClass'=>'pagination text-center',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name' => '排名',
			'value' => '$data["id"]',
		),
		array(
			'name' => '工号',
			'value' => '$data["driver_id"]',
		),
		array(
			'name' => '司机姓名',
			'value' => '$data["name"]',
		),
		'bind_count_count'=>array(
			'header'=>'绑定总数',
			'name'=>'bind_count_count',
		),
		'used_count_count'=>array(
			'header'=>'消费总数',
			'name' => 'used_count_count',
		),
		array(
			'name' => '收入总金额',
			'value' => '$data["bonus_count"]',
		),
		array(
			'name' => '呼叫中心补单',
			'value'=>array($this, 'getDriverBonusUseCountByHandCallCenter'),
		),
		array(
			'name' => '客户呼叫补单',
			'value'=>array($this, 'getDriverBonusUseCountByHandClient')
		),
		array(
			'name' => '异常呼入',
			'value' => array($this, 'getDriverBonusUseCountCallIn'),
		),
		array(
			'name' => '异常呼出',
			'value' => array($this, 'getDriverBonusUseCountCallOut'),
		),
		array(
			'name' => '自绑自销',
			'value' => '$data["bind_self_count"]',
		),
		array(
			'name' => '当天绑定当天消费',
			'value' => '$data["consumption_count"]',
		),
	)
));

?>
