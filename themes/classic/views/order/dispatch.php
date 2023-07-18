<?php
$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery');
?>

<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('orderqueue-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="search-form">
<?php $this->renderPartial('_queue_search',array(
	'model'=>$model,
)); ?>
</div>

<div style="margin-top: 15px;margin-bottom: 5px;">
    <table>
        <tr><td class="btn-info">&nbsp;催单1次&nbsp;</td>
            <td class="btn-success">&nbsp;催单2次&nbsp;</td>
            <td class="btn-warning">&nbsp;催单3次&nbsp;</td>
            <td class="btn-danger">&nbsp;催单3次以上&nbsp;</td>
        </tr>
    </table>
</div>
<?php

//派单司机弹窗 开始
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    'options'=>array(
        'title'=>'查看己派单司机',
        'autoOpen'=>false,
        'width'=>'750',
        'height'=>'450',
        'modal'=>true,
        'buttons'=>array(
            'OK'=>'js:function(){$("#mydialog").dialog("close");}',
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
//派单司机弹窗 结束



$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'orderqueue-grid',
	'dataProvider'=>$model->search(),
	'cssFile'=>SP_URL_CSS . 'table.css',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
	'itemsCssClass'=>'table table-condensed',
	'rowCssClassExpression'=>array($this,'queueStatus'),
	//'filter'=>$model,
	'columns'=>array(
		array (
			'header'=>'取消',
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'),
			'type'=>'raw',
			'value'=>array($this,'queueCancel')
		), 

		array (
			'name'=>'booking_time',
			'header'=>'预约时间',
			'headerHtmlOptions'=>array (
				'width'=>'75px',
				'nowrap'=>'nowrap'
			),
			'value'=>'date("m-d H:i",strtotime($data->booking_time))'
		), 
		array (
			'name'=>'city_id',
			'header'=>'城市',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("city", $data->city_id)'
		),
		array (
			'name'=>'name',
			'header'=>'客户姓名',

			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
            'type'=>'raw',
            'value'=>'($data->is_vip)?"<span class=\"vip\" title=\"vip\"></span>".$data->name:$data->name',
		),
		array (
			'name'=>'phone',
			'header'=>'客户电话',
			'headerHtmlOptions'=>array (
				'width'=>'90px',
				'nowrap'=>'nowrap'
			),
			'value'=>'AdminSpecialAuth::model()->haveSpecialAuth("user_phone") ? $data->phone : trim(substr_replace($data->phone, "*****", 3, 5))'
		),
        array (
            'name'=>'phone',
            'header' =>'联系电话',
            'headerHtmlOptions'=>array (
                'width'=>'70px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'AdminSpecialAuth::model()->haveSpecialAuth("user_phone") ? $data->contact_phone : trim(substr_replace($data->contact_phone, "*****", 3, 5))'
        ),
		array (
			'name'=>'address',
			'header'=>'地址',
			'headerHtmlOptions'=>array (
				'width'=>'15%',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'number',
            'header'=>'预约人数',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
        array (
            'name'=>'dispatch_number',
            'header'=>'己派人数',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
        ),
		array (
			'name'=>'comments',
			'header'=>'备注',
			'headerHtmlOptions'=>array (
				'width'=>'10%',
			),
			'type'=>'raw'
		),
		array (
			'name'=>'created',
			'header'=>'接单时间',
			'headerHtmlOptions'=>array (
				'width'=>'75px',
				'nowrap'=>'nowrap'
			),
			'value'=>'date("m-d H:i",strtotime($data->created))'
		),
		array (
			'name'=>'agent_id',
			'header'=>'接单调度',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'dispatch_agent',
			'header'=>'派单调度',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'dispatch_time',
			'header'=>'派单时间',
			'headerHtmlOptions'=>array (
				'width'=>'75px',
				'nowrap'=>'nowrap'
			),
			'value'=>'($data->dispatch_time=="0000-00-00 00:00:00")?"":date("m-d H:i",strtotime($data->dispatch_time))'
		),		
		array(
			'name'=>'派单',
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'queueDispatch'),
		),
		array(
			'name'=>'状态',
            'type'=>'raw',
			'headerHtmlOptions'=>array (
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value'=>array($this,'queueDispatchStatus'),
		),
        array(
            'class' => 'CLinkColumn',
            'headerHtmlOptions'=>array (
                'width'=>'20px',
            ),
            'label' => '修改',
            'visible' => '$data->flag==0',
            'linkHtmlOptions'=>array('target'=>'_blank'),
            'urlExpression' => 'Yii::app()->createUrl("client/changeQueue",array("qid"=>$data->id))',
        )
	),
)); ?>
<p></p>



<!-- 
<label>需求</label>
<label>1-1、司机电话显示司机的当前位置、状态、本日接单的列表</label>
<label>1-2、大客户司机推荐</label>
<label>2-1、自然点击，显示本座席当天的呼入记录</label>

每秒更新心跳信息，获取自己队列里的信息
-->
 
<script type="text/javascript">


//查看己派单的司机
function dialogInit(id){
    $.ajax({
        'url':'<?php echo Yii::app()->createUrl('/order/driverOrderView');?>',
        'data':'id='+id,
        'type':'get',
        'success':function(data){
            $('#dialogdiv').html(data);

        },
        'cache':false
    });
    $("#mydialog").dialog("open");
    return false;
}


function cancelQueue(id){
	if(confirm("确认取消此订单？")){
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/order/queuecancel');?>',
			'data':'id='+id,
			'type':'get',
			'success':function(data){
				alert('订单已经取消。');
				$.fn.yiiGridView.update('orderqueue-grid', {
	        		data: $(this).serialize()
	        	});
			},
			'cache':false
		});
	}
}


function cancelDispatch(id){
    if(confirm("确认撤销为等待派单？")){
        $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/order/dispatchToWaitStatus');?>',
            'data':'id='+id,
            'type':'get',
            'success':function(data){
                alert('己变为手动派单。');
                $.fn.yiiGridView.update('orderqueue-grid', {
                    data: $(this).serialize()
                });
            },
            'cache':false
        });
    }
}

function okDispatch(id){
    if(confirm("确认手工设置为派单完成吗？")){
        $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/order/dispatchToOkStatus');?>',
            'data':'id='+id,
            'type':'get',
            'success':function(data){
                alert('状态己变为派单完成。');
                $.fn.yiiGridView.update('orderqueue-grid', {
                    data: $(this).serialize()
                });
            },
            'cache':false
        });
    }
}

var updater = {
    poll: function(){
        $.ajax({url: "index.php",
        		data: {r:'client/ajax',method:'customer_order_queue', user:'<?php echo Yii::app()->user->id;?>'},
                type: "POST",
                dataType: "json",
                success: updater.onSuccess,
                error: updater.onError});
    },
    onSuccess: function(data, dataStatus){
        try{
            window.parent.$("div#messages").html(data+"<br>");
            $.fn.yiiGridView.update('orderqueue-grid', {
        		data: $(this).serialize()
        	});
            
        }
        catch(e){
            updater.onError();
            return;
        }
        interval = window.setTimeout(updater.poll, 10000);
    },
    onError: function(){
        console.log("Poll error;");
    }
};

updater.poll();


</script>
