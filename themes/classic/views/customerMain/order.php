<?php $this->pageTitle = '代驾记录';?>
<div class="search-form">
	<?php 
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->request->url,
		'method'=>'post',
	)); 
	?>
	<div class="row-fluid">
	代驾时间：
	<?php   
    $this->widget('zii.widgets.jui.CJuiDatePicker',array(  
        'attribute'=>'visit_time',  
        'language'=>'zh_cn',  
        'name'=>'s',  
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
	到
	<?php   
    $this->widget('zii.widgets.jui.CJuiDatePicker',array(  
        'attribute'=>'visit_time',  
        'language'=>'zh_cn',  
        'name'=>'e',  
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
		<input type="submit" value="查询" class='btn btn-success'/>
	</div>
	<?php $this->endWidget(); ?>
</div>
<h1>代驾记录</h1>
<!--
订单编号
司机工号
司机姓名
呼叫时间
预约时间
出发地
目的地
代驾费
抵扣金额
抵扣类型
抵扣类型编号
实收现金
报单/销单
销单原因分类
订单来源
-->
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'Order-grid',
	'dataProvider'=>$model->search($criteria),
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table  table-condensed',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'columns'=>array(
		'order_number',
		'driver_id',
		array(
			'name' => '司机姓名',
			'value' => ''
		),
		array(
			'name' => '呼叫时间',
			'value' => 'date("Y-m-d H:i",$data->call_time)'
		),
		array(
			'name' => '预约时间',
			'value' => 'date("Y-m-d H:i",$data->booking_time)',
		),
		'location_start',
		'location_end',
		'income',
		'cast',
		'cost_type',
		'price',
		'cancel_type',
		'cancel_desc',
		'source',
	),
)); 
?>
