<?php

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
    var title = $(this).text() == '收起搜索' ? '展开搜索' : '收起搜索';
    $(this).text(title);
	return false;
});
$('.search-form form').submit(function(){
	$('#bonus-library-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	$('#bonus-library-grid-count').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");



?>

<?php echo CHtml::link('收起搜索', '#', array('class' => 'search-button btn')); ?>
&nbsp;
<?php echo CHtml::link('全国城市查看', Yii::app()->createUrl('bonusLibrary/bonus_distri'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
&nbsp;
<?php echo CHtml::link('全国渠道查看', Yii::app()->createUrl('bonusLibrary/channel_bonus'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
&nbsp;
<?php echo CHtml::link('全国已分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distried'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
&nbsp;
<?php echo CHtml::link('全国异常卡', Yii::app()->createUrl('bonusLibrary/bonus_distried&type=3'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
&nbsp;
<?php echo CHtml::link('库房未分配', Yii::app()->createUrl('bonusLibrary/bonus_distring'), array('class' => 'btn btn-success', 'target' => '_self')); ?>

<div class="search-form" style="display:block;">
    <?php
    $this->renderPartial('_search_distri', array(
        'model' => $model,
        'dateStart' => $dateStart,
        'dateEnd' => $dateEnd
    ));
    ?>

</div>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bonus-library-grid-count',
    'dataProvider' => $num,
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        array(
            'name' => 'bonus_sn',
            'header' => '实体卷总数',
            'type' => 'raw',
            'value' => '$data["bonus_sn"]==""?0:$data["bonus_sn"]',
        ),
        array(
            'name' => 'channel',
            'header' => '已分配',
            'type' => 'raw',
            'value' => '$data["channel"]==""?0:$data["channel"]',
        ),
        array(

            'name' => 'number',
            'type' => 'raw',
            'header' => '已使用',
            'value' => '$data["number"]==""?0:$data["number"]',
        ),

        array(
            'name' => 'password',
            'type' => 'raw',
            'header' => '未使用',
            'value' => '$data["password"]==""?0:$data["password"]',
        ),
        array(
            'name' => 'money',
            'type' => 'raw',
            'header' => '库存',
            'value' => '$data["money"]==""?0:$data["money"]',
        ),
        array(
            'name' => 'bonus_id',
            'type' => 'raw',
            'header' => '坏卡',
            'value' => '$data["bonus_id"]==""?0:$data["bonus_id"]',
        ),
        array(
            'name' => 'owner',
            'header' => '异常卡',
            'type' => 'raw',
            'value' => '$data["owner"]==""?0:$data["owner"]',
        ),
        array(
            'name' => 'owner',
            'header' => '回收率',
            'type' => 'raw',
            'value' => '$data["bonus_sn"]==0?0:(round($data["number"]/$data["bonus_sn"], 2)*100)."%"',
        ),
        array(
            'name' => 'update_by',
            'header' => '渠道数量',
            'type' => 'raw',
            'value' => '$data["update_by"]==""?0:$data["update_by"]',
        ),
    ),
));
?>


<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bonus-library-grid',
    'dataProvider' => $data,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        array(
            'name' => 'city_id',
            'header' => '城市',
            'type' => 'raw',
            'value' => 'Dict::item("city",$data["city_id"])',
        ),
        array(
            'name' => 'channel_num',
            'header' => '渠道数量',
            'type' => 'raw',
            'value' => 'BonusChannel::getChannelCount($data["city_id"])',
        ),
        array(

            'name' => 'num_all',
            'type' => 'raw',
            'header' => '实体卷数量',
        ),

        array(
            'name' => 'distried',
            'type' => 'raw',
            'header' => '已分配数量',
        ),
        array(
            'name' => 'distring',
            'type' => 'raw',
            'header' => '未分配数量',
        ),
        array(
            'name' => 'unusual',
            'type' => 'raw',
            'header' => '异常',
        ),
        array(
            'name' => 'used',
            'type' => 'raw',
            'header' => '已使用数量',
        ),
        array(

            'name' => 'use',
            'header' => '回收率',
            'type' => 'raw',
            'value' => '$data["num_all"]==0?"0%":(round($data["used"]/$data["num_all"], 2)*100)."%"',
        ),
        array(
            'header' => '详情',
            'class' => 'CButtonColumn',
            'template' => '{select}',
            'buttons' => array(
                'select' => array(
                    'label' => '详情',
                    //   'visible' => 'in_array($data->status, array(BonusCode::STATUS_APPROVED))',
                    'options' => array('target' => '_self'),
                    'url' => 'Yii::app()->createUrl("bonusLibrary/channel_bonus", array("city_id"=>$data["city_id"],"dateStart"=>$data["dateStart"],"dateEnd"=>$data["dateEnd"]))',
                ),
            )
        ),
    ),
));
?>


