<?php
$this->pageTitle = '历史充值记录';

?>

<h1><?php echo $this->pageTitle;?></h1>

<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('recharge-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="search-form">
	<div class="well span12">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div>
		<?php echo $form->label($model,'user',array('class'=>'span1')); ?>
		<?php echo CHtml::textField('EmployeeAccount[user]',$model->user); ?>
	</div>
	
	<div>
		<label for="EmployeeAccount_comment" class="span1">操作人</label>
		<?php echo CHtml::textField('EmployeeAccount[comment]',$model->comment); ?>
	</div>
	
	<div>
		<label for="EmployeeAccount_created" class="span1">操作月</label>		
		<?php
			$monthArray = array();
			$last_month = date('n');
			$last_year = date('Y');
			$last_month_string = date('Ym');
			while ($last_month_string > 201205){
				$last_month_time = mktime(0, 0, 0, $last_month - 1, 1, $last_year);
				$last_month = date('n', $last_month_time);
				$last_year = date('Y', $last_month_time);
				$last_month_string = date('Ym', $last_month_time);
				$monthArray[$last_month_string] = $last_month_string;
			}
			
			echo $form->dropDownList($model, 'created', $monthArray);
		?>
	</div>

	<div class="buttons">
		<?php echo CHtml::submitButton('搜索'); ?>
	</div>

<?php $this->endWidget(); ?>
	</div>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'recharge-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'htmlOptions'=>array('class'=>'row span11'),
	'columns'=>array(
		array (
			'name'=>'司机工号', 
			'headerHtmlOptions'=>array (
				'width'=>'20px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>'$data["user"]'
		), 
		array (
			'name'=>'充值类型', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>'Dict::item("account_type", $data["type"]);'
		),
		array (
			'name'=>'费用', 
			'headerHtmlOptions'=>array (
				'width'=>'25px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>'$data["cast"]'
		),
		array (
			'name'=>'备注', 
			'headerHtmlOptions'=>array (
				'width'=>'250px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>'$data["comment"]'
		), 
		array (
			'name'=>'操作时间', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>'date("Y-m-d", $data["created"])'
		),
	),
)); ?>

