<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-12-2
 * Time: 下午1:23
 * To change this template use File | Settings | File Templates.
 */
$this->layout = '//layouts/main_no_nav';
?>
<style>
    .navbar .navbar-inner{
        background-color: #FAFAFA!important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067)!important;
        background-image: -moz-linear-gradient(top, #FFF, #F2F2F2);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#FFF), to(#F2F2F2));
        background-image: -webkit-linear-gradient(top, #FFF, #F2F2F2);
        background-image: -o-linear-gradient(top, #FFF, #F2F2F2);
        background-image: linear-gradient(to bottom, #FFF, #F2F2F2);
    }
</style>
<div class="navbar">
    <div class="navbar-inner" >
        <a class="brand" href="#">短信评价</a>
    </div>
</div>
<?php

$this->widget('zii.widgets.grid.CGridView', array (
        'id'=>'comments-grid',
        'dataProvider'=>$data,
        'itemsCssClass'=>'table table-bordered',
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'rowCssClassExpression'=>'($data->level==1)?"alert-error":""',
        'columns'=>array (
            array(
                //'class' => 'CCheckBoxColumn',
                //'selectableRows' => 2,
                'name'=>'ID',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'value' => '$data->id',
            ),
            /*
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
            */
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
                'value'=>'Common::parseCustomerPhone($data->sender)'
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
                'value'=>'$data->order_id'
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
            /*
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
            */
        )
    )
);
?>

