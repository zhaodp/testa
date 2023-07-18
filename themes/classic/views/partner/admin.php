<?php
/* @var $this PartnerController */
/* @var $model Partner */

/*$this->breadcrumbs=array(
	'Partners'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Partner', 'url'=>array('index')),
	array('label'=>'Create Partner', 'url'=>array('create')),
);*/

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	var title = $(this).text() == '收起搜索' ? '展开搜索' : '收起搜索';
    $(this).text(title);
	return false;
});
$('.search-form form').submit(function(){
	$('#partner-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>商家列表</h1>

<!--<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>-->

<?php echo CHtml::link('展开搜索','#',array('class'=>'btn search-button')); ?>
&nbsp;
<?php echo CHtml::link('新增商家', Yii::app()->createUrl('partner/create'), array('class' => 'btn btn-success','target'=>'_blank')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'partner-grid',
	'dataProvider'=>$model->search(),
    'itemsCssClass' => 'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
	//'filter'=>$model,
	'columns'=>array(
        array(
            'name' => 'city',
            'type' => 'raw',
            'value' => 'Dict::model()->item("city", $data->city)',
        ),
		array(
            'name' => 'name'
        ),

		'contact',
		'phone',
        array(
            'name' => '坐席数量',
            'value' => '$data->seat_number'
        ),
        array(
            'name' => '结算方式',
            'value' => '$data->pay_sort == 1 ? \'报单分成\' : ($data->pay_sort == 2 ? \'优惠券减免\' : \'VIP全额免单\')',
        ),
        array(
            'name' => '分成金额',
            'value' => '$data->sharing_amount',

        ),
        array(
            'name' => 'VIP余额',
            'value' => 'Partner::model()->getVipBalance($data->vip_card)',
        ),
        array(
            'name' => 'bonus_phone',
            'value' => '$data->bonus_phone ? $data->bonus_phone : 0'
        ),
        array(
            'name' => '订单数',
            'value' => 'Order::model()->getOrderTotal($data->channel_id)',
        ),
        array(
            'name' => 'status',
            'value' => '$data->status == 0 ? "正常" : "屏蔽" ',
        ),
		array(
            'header' => '操作',
            'htmlOptions' => array(
                'style' => 'width:160px;'
            ),
			'class'=>'CButtonColumn',
            'template' => '{view} {update} {shield} {enshield} {bill}',
            'buttons' => array(
                'view' => array(
                    'label' => '查看',
                    'imageUrl' => false,
                    'options' => array(
                        'target' => '_blank',
                    ),
                ),
                'update' => array(
                    'label' => '修改',
                    'imageUrl' => false
                ),
                'shield' => array(
                    'label' => '屏蔽',
                    'url' => 'Yii::app()->createUrl("partner/block",array("id"=>$data->id, "status" => $data->status))',
                    'visible' => '$data->status == Partner::PARTNER_STATUS_ENABLE',
                    'click' => 'function(){
                    if(confirm(\'确定屏蔽吗？\')){
                    var url = $(this).attr(\'href\');
                        $.ajax({
                            \'url\':url,
                            \'type\':\'get\',
                            \'dataType\':\'json\',
                            \'cache\':false,
                            \'success\':function(data){
                                if(data.status == 1){
                                    alert(data.message);
                                }else{
                                    alert(data.message);
                                }
                                $.fn.yiiGridView.update("partner-grid");
                            }
                        });
                    }
                    return false;
                 }',
                ),
                'enshield' => array(
                    'label' => '开启',
                    'url' => 'Yii::app()->createUrl("partner/block",array("id"=>$data->id, "status" => $data->status))',
                    'visible' => '$data->status == Partner::PARTNER_STATUS_SHIELDED',
                    'click' => 'function(){
                    if(confirm(\'确定开启吗？\')){
                        var url = $(this).attr(\'href\');
                        $.ajax({
                            \'url\':url,
                            \'type\':\'get\',
                            \'dataType\':\'json\',
                            \'cache\':false,
                            \'success\':function(data){
                                if(data.status == 1){
                                    alert(data.message);
                                }else{
                                    alert(data.message);
                                }
                                $.fn.yiiGridView.update("partner-grid");
                            }
                        });
                    }
                    return false;
                  }'
                ),
                'bill' => array(
                    'label' => '账单明细',
                    'url' => 'Yii::app()->createUrl("partner/orderBill",array("Bill" => array("channel"=>$data->channel_id)))',
                    'options' => array('target' => '_blank')
                )
            ),
		),
        array(
            'name' => '导出坐席',
            'htmlOptions' => array(
                'style' => 'width:90px;'
            ),
            'type' => 'raw',
            'value' => 'CHtml::link("导出", array("partner/report", "Partner" => array("id" => $data->id)))',
        ),
	),
)); ?>