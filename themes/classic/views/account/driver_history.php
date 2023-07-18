<?php
$this->pageTitle = '对账单汇总';
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
		<td>您当前信息费余额为：<?php echo $balance; ?>元</td>
		<td colspan=3></td>
	</tr>
</table>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'silver_table',
	'itemsCssClass'=>'table table-striped',
	'dataProvider'=>$dataProvider,
	//'htmlOptions'=>array('class'=>''),
	'columns'=>array(
		array (
			'name'=>'月度', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=> 'CHtml::link($data["settle_date"], array("account/driverhistorydetail", "month"=>$data["settle_date"]))',
		), 
		array (
			'name'=>'现金收入', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t0"]'
		),
		array (
			'name'=>'信息费', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data["t1"]'
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
			'value'=>'$data["t3"]'
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
        array(
            'name' => '当月余额',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["total"]'
        ),
	)
	
)); 
?>

<script type="text/javascript">
function openMonth(month, type){
	src = "<?php echo Yii::app()->createUrl('account/driverHistoryMonList'); ?>";
	href = src + "&month=" + month + "&tablename=1";
	if(type != null){
		href = href +"&type=" + type;
	}
	$("#view_driver_frame").attr("src",href);
	$("#view_driver_dialog").dialog("open");
	return false;
}
</script>
