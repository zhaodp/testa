<?php
$this->pageTitle = '订单管理';
?>
<h1><?php echo $this->pageTitle;?></h1>

<div class="btn-group">
<?php 
	$buttons =array('全部订单'=>'-1','未报单的订单'=>0,'销单待审核'=>2,'拒绝销单的订单'=>4,'已销单的订单'=>3,'已完成报单的订单'=>1);
	$status = (isset($_GET['status'])?$_GET['status']:-1);

	foreach ($buttons as $k=>$v){
		if($status == $v){
			$btn_status = array('class'=>"btn active");
		}else{
			$btn_status = array('class'=>"btn");
		}
		if($v != -1){
			echo CHtml::link($k, array("order/driver", "status"=>$v),$btn_status);
		}else{
			echo CHtml::link($k, array("order/driver"),$btn_status);
		}
	}

?>
</div>

<?php
//姓名	呼入电话	客户电话	呼叫时间	车型	里程	单号	代驾费用	收入	扣款	备注	余额	销单说明
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
            'OK'=>'js:function(){$(this).dialog("close");}',    
        ),
    ),
));

echo '<div id="dialogdiv"></div>';
echo '<iframe id="create_complaint_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');


$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid', 
	'dataProvider'=>$dataProvider, 
	'itemsCssClass'=>'table table-condensed',
	'cssFile'=>SP_URL_CSS . 'table.css',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
//	'summaryText'=>$dataProvider->itemCount,
	'rowCssClassExpression'=>array($this,'orderStatus'),
	'columns'=>array (
		array (
			'name'=>'order_id',
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link($data->order_id, array("order/view","id"=>$data->order_id), array("onclick"=>"{//orderDialogdivInit($data->order_id);}","target"=>"_blank"))'
		),
        array(
            'name' => 'order_number',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->order_number'
        ),
        array (
			'name'=>'name', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'orderVIP')
		), 
		array (
			'name'=>'phone', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
            'type'=>'raw',
			'value'=>array($this,'driverOrderPhone'),
		),
		array (
			'name'=>'booking_time', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("m-d H:i",$data->booking_time)'
		), 
		array (
			'name'=>'location_start', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
            'type'=>'raw'
		), 
		array (
			'name'=>'location_end', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
            'type'=>'raw'
		), 
		array (
			'name'=>'income', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap',
				'style'=>'text-align:right'
			), 
			'htmlOptions'=>array (
				'style'=>'text-align:right'
			),
            'type'=>'raw'
		), 
		array (
			'name'=>'cast', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap',
				'style'=>'text-align:right'
			), 
			'htmlOptions'=>array (
				'style'=>'text-align:right'
			),
			'value'=>'$data->cast',
            'type'=>'raw'
		), 
		array (
			'name'=>'description', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
            'type'=>'raw'
		), 
//		array (
//			'header'=>'呼叫前',
//			'headerHtmlOptions'=>array (
//				'width'=>'62px',
//				'nowrap'=>'nowrap'
//			),
//			'type'=>'raw',
//			'value'=>array($this,'driverStateBefore')
//		),
//		array (
//			'header'=>'呼叫后',
//			'headerHtmlOptions'=>array (
//				'width'=>'62px',
//				'nowrap'=>'nowrap'
//			),
//			'type'=>'raw',
//			'value'=>array($this,'driverStateEnd')
//		),
//		array (
//			'header'=>'余额', 
//			'headerHtmlOptions'=>array (
//				'width'=>'30px', 
//				'nowrap'=>'nowrap'
//			), 
//			'htmlOptions'=>array (
//				'style'=>'text-align:right'
//			),
//			'value'=>''
//		), 
		array (
			'header'=>'操作', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>array($this,'orderOptration')
		), 
		array (
			'header'=>'销单原因', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>array($this,'orderCacnelType')
		),
		array (
			'header'=>'销单说明', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>array($this,'orderCacnel')
		),
			array(
					'name'=>'是否投诉',
					'headerHtmlOptions'=>array (
							'nowrap'=>'nowrap'
					),
					'type' => 'raw',
					'value' =>'Yii::app()->controller->checkComplaint($data->order_id,$data->status)'
			),
	)
));

//$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
//    'id'=>'mydialog',
//    // additional javascript options for the dialog plugin
//    'options'=>array(
//        'title'=>'Dialog box 1',
//        'autoOpen'=>false,
//    ),
//));
//echo '<div>hellodfasdfasdfas</div>';
//$this->endWidget('zii.widgets.jui.CJuiDialog');
//// the link that may open the dialog
//echo CHtml::link('open dialog', '#', array(
//   'onclick'=>'$("#mydialog").dialog("open"); return false;',
//));

?>
<script>
function orderDialogdivInit(orderId){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/order/view');?>',
		'data':'id='+orderId,
		'type':'get',
		'success':function(data){
			$("#create_complaint_frame").height(0);
			$('#dialogdiv').html(data);
		},
		'cache':false		
	});
	jQuery("#mydialog").dialog("open");
	return false;
}

function create_complaint(orderId){
	if(orderId==""||isNaN(orderId)){
		alert("参数不正确，请刷新重试");return false;
	}
	$(".ui-dialog-title").html("提交投诉");
	$('#dialogdiv').html('');
	$("#create_complaint_frame").height('100%');
	url = '<?php echo Yii::app()->createUrl('/driverComplaint/create');?>&orderId='+orderId;
	$("#create_complaint_frame").attr("src",url);
	$("#mydialog").dialog("open");
}
</script>
<div>*列表中没有的订单请点击<a href="<?php echo Yii::app()->createUrl('/order/create'); ?>">订单补录</a></div>
