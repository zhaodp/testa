<?php
$this->pageTitle = '订单管理';

$yesterday = date('Y-m-d', time() - 24 * 3600) . ' 09:00';
$today = date('Y-m-d H:i', strtotime($yesterday) + 24 * 3600);
$day = idate('d') - 1;
$month_call = date('Y-m-d', strtotime('-'.$day.' day')). "00:00";
$month_booking = date('Y-m-d H:i', time());

$last_month_call = date("Y-m-d", strtotime('-2 month'))." 00:00";
$last_month_booking = date("Y-m-d", strtotime('-1 month'))." 23:59";
?>



<h1><?php echo $this->pageTitle;?></h1>

<script type='text/javascript'>
function updateStatus(order_id){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/order/updateInvoiceStatus');?>',
                'data':{'order_id':order_id},
                'dataType':'json',
                'type':'get',
                'success':function(data){
			$('#order_'+order_id).html(data.msg);
			},
                'cache':false           
                });

}
</script>

<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('order-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<?php         
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'order_cancel_dialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '',
        'autoOpen' => false,
        'width' => '900', 
        'height' => '580',
        'modal' => true, 
        'buttons' => array( 
            '关闭' => 'js:function(){$("#order_cancel_dialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<div id="order_cancel_dialog_div"></div>';
echo '<iframe id="cru-frame-order-cancel" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
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

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_driver_dialog', 
	'options'=>array (
		'title'=>'查看司机信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_customer_dialog', 
	'options'=>array (
		'title'=>'查看VIP信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_customer_dialog").dialog("close");}'))));
echo '<div id="view_customer_dialog"></div>';
echo '<iframe id="view_customer_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_booking_dialog', 
	'options'=>array (
		'title'=>'查看预约信息', 
		'autoOpen'=>false, 
		'width'=>'950', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_booking_dialog").dialog("close");}'))));
echo '<div id="view_booking_dialog"></div>';
echo '<iframe id="view_booking_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_view = <<<EOD
function(){
	$("#view_driver_frame").attr("src",$(this).attr("href"));
	$("#view_driver_dialog").dialog("open");
	return false;
}
EOD;
?>

<div class="btn-group">
	<?php echo CHtml::link('高级搜索', array("#"),array('class'=>"search-button btn-primary btn"));?>

    <?php echo CHtml::link('当月', array("order/admin", "Order"=>array("call_time"=>$month_call, "booking_time" =>$month_booking)),array('class'=>"btn"));?>
    <?php echo CHtml::link('上月', array("order/admin", "Order"=>array("call_time"=>$last_month_call, "booking_time" =>$last_month_booking)),array('class'=>"btn"));?>

	<?php echo CHtml::link('昨日订单', array("order/admin", "Order"=>array("call_time"=>$yesterday, "booking_time" =>$today)),array('class'=>"btn"));?>
	<?php echo CHtml::link('昨日未报单', array("order/admin", "Order"=>array("status"=>"0", "call_time"=>$yesterday, "booking_time" =>$today)),array('class'=>"btn"));?>
	<?php echo CHtml::link('昨日已报单', array("order/admin", "Order"=>array("status"=>"1", "call_time"=>$yesterday, "booking_time" =>$today)),array('class'=>"btn"));?>
	<?php echo CHtml::link('昨日待销单', array("order/admin", "Order"=>array("status"=>"2", "call_time"=>$yesterday, "booking_time" =>$today)),array('class'=>"btn"));?>
	<?php echo CHtml::link('昨日呼叫中心订单', array("order/admin", "Order"=>array("source"=>"1", "call_time"=>$yesterday, "booking_time" =>$today)),array('class'=>"btn"));?>
	<?php echo CHtml::link('昨日直接呼叫订单', array("order/admin", "Order"=>array("source"=>"0", "call_time"=>$yesterday, "booking_time" =>$today)),array('class'=>"btn"));?>
	<?php echo CHtml::link('昨日司机补单', array("order/admin", "Order"=>array("source"=>"3", "call_time"=>$yesterday, "booking_time" =>$today)),array('class'=>"btn"));?>
</div>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
    'callCenterUserType' => $callCenterUserType,
)); ?>
</div>

<?php
$criteria = new CDbCriteria();
if($channel){
    $criteria->addCondition('channel = :channel');
    $criteria->params[':channel'] = $channel;
}
//$criteria = new CDbCriteria(array(
//	'order'=>'call_time desc',
//));
//
//if ($model->order_id)
//{
//	$criteria->addCondition('order_id='.$model->order_id); 
//}

/*
if(Yii::app()->user->city != 0){
	$criteria->join =',t_driver d';
	$criteria->condition = 'd.user = t.driver_id and d.city_id ='.Yii::app()->user->city;
}
*/

$dataProvider = $model->search($criteria);
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid', 
	'dataProvider'=>$dataProvider, 
	'cssFile'=>SP_URL_CSS . 'table.css',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'itemsCssClass'=>'table table-condensed',
	'rowCssClassExpression'=>array($this,'orderStatus'),
	'htmlOptions'=>array('class'=>'row-fluid'),
	'columns'=>array (
		array (
			'name'=>'订单编号', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'orderIdAndNumber')
		),
		array (
			'name'=>'司机信息', 
			'headerHtmlOptions'=>array (
				'width'=>'80px'
			),
			'type'=>'raw',
			'value'=>array($this,'adminDriverInfo'),
		), 
		array (
			'name'=>'客户信息', 
			'headerHtmlOptions'=>array (
                'style' => 'width:130px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>array($this,'orderPhone')
		),
		array (
			'name'=>'订单时间', 
			'headerHtmlOptions'=>array (
				'style'=>'width:120px',
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw', 
			'value'=>array($this,'orderTime')
		),
		array (
			'name'=>'起始地点', 
			'headerHtmlOptions'=>array (
				'style'=>'width:120px',
				'nowrap'=>'nowrap'
			),
			'type' => 'raw',
			'value' => array($this,'OrderAddr')
		), 
		array (
			'name'=>'收费', 
			'headerHtmlOptions'=>array (
				'style'=>'width:120px',
				'nowrap'=>'nowrap'
			),
			'type' => 'raw',
			'value' => array($this, 'orderFee')
		), 
		
		array (
			'name'=>'description', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=> array($this, 'orderSource')
			
		),		 
		array (
			'header'=>'订单描述', 
			'headerHtmlOptions'=>array (
				'width'=>'62px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'$data->description',
		),
		array (
			'header'=>'销单', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>array($this,'orderCancel')
		),
		array (
			'header'=>'状态', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			), 
			'type'=>'raw',
			'value'=>array($this,'confirmOrderCacnel')
		),
        array (
            'header'=>'发票',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=> array($this,'invoice')
        ),
		array (
			'name'=>'操作', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=> array($this,'operateOrder')
			//'value'=>'($data->status == ORDER::ORDER_COMFIRM) ? CHtml::link("确认", "javascript:void(0);", array("onclick"=>"{dialogConfirmInit(\'$data->order_id\', 3);}")) . "<br/>" . CHtml::link("拒绝", "javascript:void(0);", array("onclick"=>"{dialogConfirmInit(\'$data->order_id\', 4);}")) : ""'
		),
	)
));
?>

<script type='text/javascript'>

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

function orderRelation(orderId){
	$('#dialogdiv').html("<img src='<?php echo SP_URL_IMG;?>loading.gif' />");	
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/order/relation');?>',
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

function driverDialogdivInit(src){
	$("#view_driver_frame").attr("src",src);
	$("#view_driver_dialog").dialog("open");
	return false;
}

function dialogConfirmInit(id, status){
	if (status == <?php echo Order::ORDER_CANCEL;?>){
		if(!confirm('确认该订单的可以销单？')) return false;
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/order/confirm');?>',
			'data':{'id':id},
			'type':'get',
			'success':function(data){
				$.fn.yiiGridView.update('order-grid');
			},
			'cache':false		
		});
	} else {
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/order/rejectReason');?>',
			'data':{'id':id, 'status':status},
			'type':'get',
			'success':function(data){
				$('#dialogdiv').html(data);
			},
			'cache':false		
		});
		$("#mydialog").dialog("open");
		return false;
	}
}

//function dialogConfirmCancel(id, contact_phone, driver_id, status){//销单
//}

function dialogConfirmCancel(href) {
        $("#cru-frame-order-cancel").attr("src", href);
        $("#order_cancel_dialog").dialog("open");
        return false; 
}
//判断未报单的订单是否能销单
function judgeCancelOrder(order_id){
    $.ajax({
        'url':'<?php echo Yii::app()->createUrl('/order/judgeCancel');?>',
        'data':'id='+order_id,
        'type':'get',
        'success':function(data){
            if(data != ''){
		var mess = data.split(':')[0];
		var code = data.split(':')[1];
		if(code != '0'){//不允许销单
		    alert(mess);
		}else{
		    var href = '<?php echo Yii::app()->createUrl('/order/orderCancel');?>' + '&status=0&id='+order_id;
		    dialogConfirmCancel(href);
		}
	    }
        },
        'cache':false
    });
}

function dialogClose(id, status, do_reason){
	if (id == '0' && status == '0') {
		$("#mydialog").dialog("close");
		return false;
	}
	if (status == <?php echo Order::ORDER_NOT_COMFIRM;?> 
		|| status == <?php echo Order::ORDER_CANCEL;?>
		|| status == <?php echo Order::ORDER_READY;?>
		|| status == <?php echo Order::ORDER_COMFIRM;?>){
		if (do_reason == '') {
			alert ("请填写原因。");
			return false;
		} else {
			$.ajax({
				'url':'<?php echo Yii::app()->createUrl('/order/doreject');?>',
				'data':{'id':id, 'status':status, 'description':do_reason},
				'type':'get',
				'success':function(data){
					$.fn.yiiGridView.update('order-grid');
				},
				'cache':false		
			});	
			$("#mydialog").dialog("close");
			return false;
		}
	} else {
		$("#mydialog").dialog("close");
		return false;
	}
}


function getCustomerInfo(phone) {
	var src='<?php echo Yii::app()->createUrl('/customer/info');?>'+'&phone='+phone+'&dialog=1';
	$("#view_customer_frame").attr("src",src);
	$("#view_customer_dialog").dialog("open");
	return false;
}

function getBookingInfo(order_id) {
	var src='<?php echo Yii::app()->createUrl('/order/queue');?>'+'&order_id='+order_id+'&dialog=1';
	$("#view_booking_frame").attr("src",src);
	$("#view_booking_dialog").dialog("open");
}


</script>
