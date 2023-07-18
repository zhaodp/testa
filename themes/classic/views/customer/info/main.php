<?php
/* @var $this VipController */
/* @var $model Vip */


$this->breadcrumbs=array(
	'Vips'=>array('index'),
	'Manage',
);


$this->pageTitle = '用户管理';
?>

<h1>用户管理</h1>
<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('vip-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form">
	<?php 
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); 
	?>
	<div class="row-fluid">
		<div class="span3">
		<?php echo CHtml::label('姓名','name'); ?>
		<?php echo $form->textField($model,'name');?>
		</div>
		<div class="span3">
		<?php echo CHtml::label('性别','gender');?>
		<?php 
			$gender_list = CustomerMain::$gender_dict;
			$gender_list[0] = '全部';
			ksort($gender_list);
		?>
		<?php echo $form->dropDownList($model, 'gender', $gender_list); ?>
		</div>
		<div class="span3">
		<?php echo CHtml::label('地区','city_id');?>
		<?php echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
		</div>
		<div class="span3">
		<?php echo CHtml::label('手机号码','phone'); ?>
		<?php echo $form->textField($model,'phone');?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span3">
		<?php echo CHtml::label('类型','type');?>
		<?php 
			$type_list = CustomerMain::$type_dict;
			$type_list[0] = '全部';
			ksort($type_list);
		?>
		<?php echo $form->dropDownList($model, 'type', $type_list); ?>
		</div>
		<div class="span3">
		<?php echo CHtml::label('原vip卡号','vip_card');?>
		<?php echo $form->textField($model,'vip_card'); ?>
		</div>
		<div class="span3">
		<?php echo CHtml::label('来源','channel');?>
		<?php 
			$channel_list = CustomerMain::$channel_dict;
			$channel_list[0] = '全部';
			ksort($channel_list);
		?>
		<?php echo $form->dropDownList($model, 'channel', $channel_list); ?>
		</div>
		<div class="span3">
		<?php echo CHtml::label('状态','status');?>
		<?php 
			$status_list = CustomerMain::$status_dict;
			$status_list[0] = '全部';
			ksort($status_list);
		?>
		<?php echo $form->dropDownList($model, 'status', $status_list); ?>
		</div>		
	</div>
	<div class="row-fluid">
		<div class="span3">
			<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span3">
			<?php echo CHtml::link('添加新用户',Yii::app()->createUrl("customer/maincreate/"),array('class'=>'btn btn-success', 'style'=>'margin-top:10px;')); ?>
		</div>
	</div>
</div>
<?php $this->endWidget(); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vip-grid',
	'dataProvider'=>$model->search(),
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table  table-condensed',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'columns'=>array(
		'id',
		'name',
		array(
			'name' => '性别',
			'value' => 'Yii::app()->controller->getGender($data->gender)'
		),
		'birthday',
		'phone',
		'backup_phone',
		'imei',
		array(
			'name' => '城市',
			'value' => 'Yii::app()->controller->getCity($data->city_id)',
		),
		'company',
		array(
			'name' => '类型',
			'value' => 'Yii::app()->controller->getType($data->type)',
		),
		'credit',
		'activity',
		array(
			'name' => '操作',
			'value' => array($this, 'showMainButton'),
		),
	),
)); 
?>