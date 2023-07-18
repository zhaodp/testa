<?php
$this->pageTitle = Yii::app()->name . ' - 司机评价管理';
Yii::app()->clientScript->registerScript('search', "

$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('comments-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<!--<h1>司机评价管理</h1>-->
<?php $this->renderPartial('com_nav'); ?>
<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
    'statusStr'=>$statusStr
)); ?>
</div><!-- search-form -->
<input class="btn btn-success" id="send_msg_btn" onclick="toComplain()" type="button" value="批量转投诉">
<?php

//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'comments-grid',
	'dataProvider'=>$model->search(50),
	'itemsCssClass'=>'table',
	'pagerCssClass'=>'pagination text-center',
	'pager'=>Yii::app()->params['formatGridPage'],
	'rowCssClassExpression'=>'($data->level==1)?"alert-error":""',
	'columns'=>array (
                array(
                    'class' => 'CCheckBoxColumn',
                    'selectableRows' => 2,
                    'value' => '$data->id',
                ),
		array (
			'header'=>'司机信息',
			'headerHtmlOptions'=>array (
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'getDriverUser'),
		),
		array (
			'header'=>'城市',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'getDriverCityId'),
		),
		array (
			'header'=>'评价内容',
			'headerHtmlOptions'=>array (
				'width'=>'220px',
				'nowrap'=>'nowrap'
			),
            'type'=>'raw',
            'value'=>'($data->content)?$data->content:$data->raw_content',
		),
			array (
					'name'=>'评价类型',
					'headerHtmlOptions'=>array (
							'width'=>'60px',
							'nowrap'=>'nowrap'
					),
					'value'=>'($data->sms_type==1)?"价格核实":"服务评价"',
			),
		array (
			'name'=>'level',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
			'value'=>'$data->level'
		),
		array (
			'name'=>'sender',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link(Common::parseCustomerPhone($data->sender), array("commentSms/admin", "sender"=>$data->sender))'
		),
		array(
			'name'=>'order_status',
			'headerHtmlOptions'=>array(
				'width' => '60px',
				'nowrap' =>'nowrap'
			),
			'value'=>array($this,'getOrderStatus'),
		),
        array(
            'name'=>'订单号',
            'headerHtmlOptions'=>array(
                'width' => '60px',
                'nowrap' =>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'CHtml::link($data->order_id,array("order/admin", "Order[order_id]"=>$data->order_id),array("target"=>"_blank"))'
		),
		 array(
            'name'=>'来源',
            'headerHtmlOptions'=>array(
                'width' => '60px',
                'nowrap' =>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'($data->channel==1)?"APP":"短信"'
		),
		array(
				'name'=>'处理情况',
				'headerHtmlOptions'=>array(
						'width' => '60px',
						'nowrap' =>'nowrap'
				),
				'value'=>'($data->status==0)?"未处理":"已处理"',
		),
		array (
			'name'=>'created',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			)
		),
		array(
			'header'=>'操作',
            'class'=>'CButtonColumn',
            'template'=>'{operate} {sms} {delete}',
            'deleteButtonImageUrl' => FALSE,
            'deleteConfirmation' => '确定要转投诉吗？',
            'buttons'=>array(
                         'operate'=>
                            array(
                                'label'=>'处理',
                            	'url'=>'$this->grid->controller->createUrl("reply", array("id"=>$data->id,"asDialog"=>1,"gridId"=>$this->grid->id))',
                                'click'=>'function(){$("#cru-frame").attr("src",$(this).attr("href")); $("#cru-dialog").dialog("open");$("#ui-id-1").html("差评处理");  return false;}',
                            	'visible'=>'AdminActions::model()->havepermission("commentSms", "reply")'
                                ),
                             'sms'=>
                             array(
                             		'label' =>'短信'	,
                             		'url'=>'$this->grid->controller->createUrl("SmsContent/Create", array("id"=>$data->id,"phone"=>$data->sender))',
                             		'click'=>'function(){$("#cru-frame").attr("src",$(this).attr("href")); $("#cru-dialog").dialog("open");$("#ui-id-1").html("发送短信");  return false;}',
                             ),
                            'delete'=>
                            array(
                                'label' =>'转投诉'	,
                                'url'=>'Yii::app()->createUrl("commentSms/reply", array("id"=>$data->id,"quickReply"=>1))',
                            ),

                      ),
        ),
	)
)
);

//--------------------- begin new code --------------------------
   // add the (closed) dialog for the iframe
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'cru-dialog',
    'options'=>array(
        'title'=>'差评处理',
        'autoOpen'=>false,
	    'modal'=>true,
        'width'=>750,
        'height'=>450,
		'buttons'=>array(
        	'关闭'=>'js:function(){$("#cru-dialog").dialog("close");}'
		)
    ),
    ));
?>
<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>
<?php

$this->endWidget();
//--------------------- end new code --------------------------
?>

<script>
    function toComplain(){
        var id_seclect = $("input[name='comments-grid_c0[]']:checked");
        if(id_seclect.length<=0){
            alert("请选择需要转投诉的记录！");
            return false;
        }
        var id_str = '';
        for(i=0;i<id_seclect.length;i++){
            id_str += id_seclect.eq(i).val()+'_';
        }
//        $('body').hide(2500,function(){
//            $('#waitting_view').show(200,function(){
//                window.setInterval(function(){$('body').toggle(2500)},2800)
//            });
//        });
        $.ajax({
            url:"<?php echo Yii::app()->createUrl("commentSms/reply"); ?>",
            data:{quickReply:1,id:id_str},
            cache:false,
            success:function(data){
                window.location.href="<?php echo Yii::app()->request->url; ?>";
            }
        });
    }
</script>
<div id="waitting_view" style="display: none;font-size:24px;width:100%;height:100%;z-index:10000;position: absolute;background: #DAD9D9;top:0px;left:0px;text-align:center;padding-top:300px;">努力处理请求中，请稍等。。。</div>
