<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs=array(
	'Vips'=>array('index'),
	'Manage',
);


$this->pageTitle = '司机发卡赚钱明细';
?>

<h1>司机发卡赚钱明细</h1>
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
<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'订单信息',
        'autoOpen'=>false,
		'width'=>'750',
		'height'=>'450',
		'modal'=>true,
		'buttons'=>array(
            'OK'=>'js:function(){dialogClose($("#OrderLog_order_id").val(), $("#OrderLog_status").val(), $("#OrderLog_description").val())}',    
        	'Close'=>'js:function(){$("#mydialog").dialog("close");}'
		),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<div class="search-form">
	<?php 
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); 
	?>
	<table>
		<tr>
			<td>
		地区
		<select name="city_id">
		<?php foreach (Dict::items('city') as $city_id=>$city_name) {?>
			<option value="<?php echo $city_id;?>" ><?php echo $city_name;?></option>
		<?php } ?>
		</select>
			</td>
			<td>
		绑定开始时间
		<?php   
		$this->widget('zii.widgets.jui.CJuiDatePicker',array(  
			'attribute'=>'visit_time',  
			'language'=>'zh_cn',  
			'name'=>'start_time',  
			'options'=>array(  
				'showAnim'=>'fold',  
				'showOn'=>'both',  
				'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',  
				'buttonImageOnly'=>true,  
				//'minDate'=>'new Date()',  
				'dateFormat'=>'yy-mm-dd',
				'changeYear'=>true,
				'changeMonth'=> true,	
			),  
			'htmlOptions'=>array(  
				'style'=>'height:18px',  
			),  
		));  
		?>
			</td>
		<td>
		绑定结束时间
		<?php   
		$this->widget('zii.widgets.jui.CJuiDatePicker',array(  
			'attribute'=>'visit_time',  
			'language'=>'zh_cn',  
			'name'=>'end_time',  
			'options'=>array(  
				'showAnim'=>'fold',  
				'showOn'=>'both',  
				'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',  
				'buttonImageOnly'=>true,  
				//'minDate'=>'new Date()',  
				'dateFormat'=>'yy-mm-dd',
				'changeYear'=>true,
				'changeMonth'=> true,	
			),  
			'htmlOptions'=>array(  
				'style'=>'height:18px',  
			),  
		));    
		?>
		</td>
		<td>
			司机工号：
			<input type="text" name="driver_id" />
		</td>
		<td>
			<input type="submit" value="搜索" />
		</td>
		</tr>
	</table>
	<?php $this->endWidget(); ?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vip-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table  table-condensed',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'columns'=>array(
		array(
			'name' => '司机工号',
			'value' => 'Yii::app()->controller->getDriverId($data->bonus_sn)',
		),
		array(
			'name' => '司机姓名',
			'value' => 'Yii::app()->controller->getDriverName($data->bonus_sn)',
		),
		array (
			'name' => '优惠卡号',
			'value' => '$data->bonus_sn',
		),
		array(
			'name' => '绑定手机号',
			'value' => '$data->customer_phone',
		),
		array(
			'name' => '绑定时间',
			'value' => 'date("Y-m-d",$data->created)',
		),
		array(
			'name' => '是否已消费',
			'value' => '$data->used ? "是":"否"',
		),
		array(
			'name' => '消费时间',
			'value' => '$data->updated ? date("Y-m-d",$data->updated) : ""',
		),
		/*
		array(
			'name' => '订单号',
			'value' => '$data->order_id',
		),
		*/
		array (
			'name'=>'订单编号', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'orderIdAndNumber')
		),
		array(
			'name'=> '轨迹',
			'value' => array($this, 'getPositionLink'),
		),
	),
)); 
?>
<script>
function orderDialogdivInit(orderId){
	$('#dialogdiv').html("<img src='<?php echo SP_URL_IMG;?>loading.gif' />");	
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/order/view');?>',
		'data':'id='+orderId,
		'type':'get',
		'success':function(data){
			$('#dialogdiv').html(data);
		},
		'cache':false
	});
	jQuery("#mydialog").dialog("open");
	return false;
}
</script>