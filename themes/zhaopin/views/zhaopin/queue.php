<?php
/* @var $this ZhaopinController */
$this->pageTitle = '报名一览 - e代驾招募';

?>

<div class="block">
	<div style="height:67px;"></div>
	<section id="agreement" class="agreement">
		<div class="page-header">
			<h2>报名一览</h2>
		</div>
		<div>
		</div>
	</section>
<div style="float:left;width:300px;">
<?php 
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));
echo CHtml::label('城市选择', 'city');
$citys = Dict::items('city');
?>
<div style="width:225px; float:left">
<?php 
	echo CHtml::dropDownList('city', $this->city_id, $citys);
?>
</div>
<div style="width:75px; float:left">
<?php 
echo CHtml::submitButton('确认',array('class'=>'btn'));
?>
</div>
<?php 
$this->endWidget(); 
?>
</div>
<div>
<?php 
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); 
?>
<label for="id_card">请输入身份证号</label>
<div style="width:225px; float:left">
	<input type="text" id="id_card" name="id_card" value="<?php echo $this->id_card;?>" />
</div>
<div style="width:75px; float:left">
<?php 
	echo CHtml::submitButton('查询',array('class'=>'btn'));
?>
</div>
<?php 
$this->endWidget(); 
?>
</div>
<?php


//title	content	contact address	zipcode	telephone	status	description	created
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid', 
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array (
		array(
                'name'=>'序号',
                'value'=>'$row+1+'.($page-1)*$dataProvider->getPagination()->pageSize
        ),
		array(
                'name'=>'报名流水号',
                'value'=>'Yii::app()->controller->getZhaopinQueueNumber($data->id, $data->city_id)',
        ),
		array (
			'name'=>'name',
			'type'=>'raw',
			'value'=>'mb_substr($data->name, 0, 3) . "*"' 
		), 
		array (
			'name'=>'mobile',
			'type'=>'raw',
			'value'=>'mb_substr($data->mobile, 0, 4) . "****" . mb_substr($data->mobile, 8, 10)'
		),
		array(
			'name'=>'id_card',
			'type'=>'raw',
			'value'=>'mb_substr($data->id_card, 0, 3) . "***********" . mb_substr($data->id_card, 14)'
		),
		array(
			'name'=>'status',
			'value' => 'Yii::app()->controller->getZhaopinStatus($data->status)'
		)
	)
)); 

?>
</div>