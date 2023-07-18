<?php
$this->pageTitle = '近期对账单明细';
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_driver_dialog', 
	'options'=>array (
		'title'=>'查看信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'500', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_driver_frame").attr("src","");$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h1><?php echo $this->pageTitle;?></h1>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table historyAccount">
	<tr class="alert alert-success">
		<td>您当前信息费余额为：<?php echo $settle["total"] ?>元</td>
		<td colspan=3></td>
	</tr>
	<tr class="alert alert-info">
		<td>信息费充值：<?php echo (isset($settle['t5']) && $settle['t5'] != '0')?CHtml::link($settle["t5"], "javascript:;", array("onclick"=>"{openMonth('$month',5);}")):'0.00'?>元</td>
		<td>现金收入：<?php echo (isset($settle['t0']) && $settle['t0'] != '0')?CHtml::link($settle["t0"], "javascript:;", array("onclick"=>"{openMonth('$month',0);}")):'0.00'?>元</td>
		<td>VIP收入：<?php echo (isset($settle['t3']) && $settle['t3'] != '0')?CHtml::link($settle["t3"], "javascript:;", array("onclick"=>"{openMonth('$month',3);}")):'0.00'?>元</td>
		<td></td>
	</tr>
	<tr class="alert alert-info">
		<td>抵扣转账：<?php echo (isset($settle['t7']) && $settle['t7'] != '0')?CHtml::link($settle["t7"], "javascript:;", array("onclick"=>"{openMonth('$month',7);}")):'0.00'?>元</td>
		<td>优惠券返现：<?php echo (isset($settle['t8']) && $settle['t8'] != '0')?CHtml::link($settle["t8"], "javascript:;", array("onclick"=>"{openMonth('$month',8);}")):'0.00'?>元</td>
		<td>司机发卡返现：<?php echo (isset($settle['t9']) && $settle['t9'] != '0')?CHtml::link($settle["t9"], "javascript:;", array("onclick"=>"{openMonth('$month',9);}")):'0.00'?>元</td>
		<td>优惠券补偿：<?php echo (isset($settle['t10']) && $settle['t10'] != '0')?CHtml::link($settle["t10"], "javascript:;", array("onclick"=>"{openMonth('$month',10);}")):'0.00'?>元</td>
	</tr>
	<tr class="alert alert-error">
		<td>信息费：<?php echo (isset($settle['t1']) && $settle['t1'] != '0')?CHtml::link($settle["t1"], "javascript:;", array("onclick"=>"{openMonth('$month',1);}")):'0.00'?>元</td>
		<td>发票扣税：<?php echo (isset($settle['t2']) && $settle['t2'] != '0')?CHtml::link($settle["t2"], "javascript:;", array("onclick"=>"{openMonth('$month',2);}")):'0.00'?>元</td>
		<td>罚金扣费：<?php echo (isset($settle['t4']) && $settle['t4'] != '0')?CHtml::link($settle["t4"], "javascript:;", array("onclick"=>"{openMonth('$month',4);}")):'0.00'?>元</td>
		<td></td>
	</tr>
	<tr class="alert alert-error">
		<td>保险费：<?php echo (isset($settle['t6']) && $settle['t6'] != '0')?CHtml::link($settle["t6"], "javascript:;", array("onclick"=>"{openMonth('$month',6);}")):'0.00'?>元</td>
		<td colspan=3></td>
	</tr>
	<tr>
		<td><?php echo CHtml::link('返回对账单汇总', array("account/driverhistory"));?></td>
		<td colspan=3></td>
	</tr>
</table>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'silver_table',
	'itemsCssClass'=>'table table-striped historyAccount',
	'dataProvider'=>$dataProvider,
	//'htmlOptions'=>array('class'=>''),
	'columns'=>array(
		array (
			'name'=>'报单日期', 
			'headerHtmlOptions'=>array (
				'width'=>'70px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link($data["id"], "javascript:;", array("onclick"=>"{openInit(\'$data[id]\');}"))'
		),
		array (
			'name'=>'订单数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'$data["t"]'
		),
		array (
			'name'=>'现金收入', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>'CHtml::link($data["t0"], "javascript:;", array("onclick"=>"{openInit(\'$data[id]\',0);}"))'
		),
		array (
			'name'=>'信息费', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>'CHtml::link($data["t1"], "javascript:;", array("onclick"=>"{openInit(\'$data[id]\',1);}"))'
		),
		array (
			'name'=>'发票扣税', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t2"]'
		),
		array (
			'name'=>'VIP收入', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>'!empty($data["t3"]) ? $data["t3"] : CHtml::link($data["t3"], "javascript:;", array("onclick"=>"{openInit(\'$data[id]\',3);}"))'
		),
		array (
			'name'=>'罚金', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t4"]'
		),
		array (
			'name'=>'充值', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t5"]'
		),
		array (
			'name'=>'保险费', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t6"]'
		),
		array (
			'name'=>'优惠券抵扣转账', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t7"]'
		),
		array (
			'name'=>'优惠券返现', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t8"]'
		),
		array (
			'name'=>'司机发卡返现', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t9"]'
		),
		array (
			'name'=>'优惠券补账', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t10"]'
		),
	)
	
));
?>

<script>
function openInit(day, type){
	var src = "<?php echo Yii::app()->createUrl('account/driverHistoryDayList'); ?>";
	var href = src + "&day=" + day + "&tablename=1";
	if(type != null){
		href = href +"&type=" + type;
	}
	$("#view_driver_frame").attr("src",href);
	$("#view_driver_dialog").dialog("open");
	return false;
}
function openMonth(month, type){
	var src = "<?php echo Yii::app()->createUrl('account/driverHistoryMonList'); ?>";
	var href = src + "&month=" + month + "&tablename=1";
	if(type != null){
		href = href +"&type=" + type;
	}
	$("#view_driver_frame").attr("src",href);
	$("#view_driver_dialog").dialog("open");
	return false;
}
</script>