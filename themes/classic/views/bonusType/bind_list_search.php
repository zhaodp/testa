<?php
$this->pageTitle = '优惠券查看列表';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('bonus-type-bind-list-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1><?php echo $this->pageTitle; ?></h1	>
<hr/>
<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'id'=>'CustomerBonusSearch'
));
?>
	<div class="row-fluid">
		<div class="span3">
			<?php echo $form->label($model,'customer_phone'); ?>
			<?php echo $form->textField($model,'customer_phone',array('maxlength'=>50)); ?>
		</div>
		<div class="span3">
			<?php echo $form->label($model,'bonus_sn'); ?>
			<?php echo $form->textField($model,'bonus_sn',array('maxlength'=>50)); ?>
		</div>
		<div class="span3">
			<label for="CustomerBonus_bonus_sn">&nbsp;</label>
			<?php echo CHtml::submitButton('搜索',array('class'=>'span4')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
<div class="row-fluid">
	客户当天订单数(用于同一客户一天之内多个订单检查，呼叫中心派单可能是多人预约, 由于只有组长收到抵扣短信，所以可以不给组员补账,注:订单报单时间15点前昨15点至今天15点算一天,15点后至明天15点算一天)
</div>
<?php 
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'bonus-type-bind-list-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-condensed',
	'columns'=>array(
		array (
			'name'=>'bonus_type_id', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this, 'getBonusTypeName')
		),
		
		array (
			'name'=>'customer_phone', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			//'value' => 'substr_replace($data->customer_phone, "****" , 3, 4)'
		    'value' => 'Common::parseCustomerPhone($data->customer_phone)'
        ),
		
		array (
			'name'=>'bonus_sn', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value' => array($this, "getBonusSnNoLink")
		),
		array (
			'name'=>'金额', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this, 'getBonusTypeMoney')
		),
		array (
			'name'=>'order_id', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this, "getOrderStatus")
		),
		
		array (
			'name'=>'created', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value'=>'$data->created ? date("Y-m-d H:i:s", $data->created) : ""' 
		),
		array (
			'name'=>'used', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value'=>'$data->used ? date("Y-m-d H:i:s", $data->used) : ""' 			
		),
		array (
			'name'=>'报单时间', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value'=>array($this, 'getOrderCompleteTime') 			
		),
				

	),
)); ?>