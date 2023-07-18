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
<?php if ($is_manager == 0) { ?>
    <?php echo CHtml::link('全国城市查看', Yii::app()->createUrl('bonusLibrary/bonus_distri'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php echo CHtml::link('全国渠道查看', Yii::app()->createUrl('bonusLibrary/channel_bonus'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php
    echo CHtml::link('全国已分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distried'), array('class' => 'btn btn-success', 'target' => '_self'));
    ?>
    &nbsp;
    <?php
    echo CHtml::link('全国异常卡', Yii::app()->createUrl('bonusLibrary/bonus_distried&type=3'), array('class' => 'btn btn-success', 'target' => '_self'));

    ?>
    &nbsp;
    <?php echo CHtml::link('库房未分配', Yii::app()->createUrl('bonusLibrary/bonus_distring'), array('class' => 'btn btn-success', 'target' => '_self')); ?>

    <?php if ($city_id > 0) { ?>
        &nbsp;
        <?php echo CHtml::link('分公司详情', Yii::app()->createUrl('bonusLibrary/channel_bonus&city_id=' . $city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
        &nbsp;
        <?php echo CHtml::link('分公司已分配', Yii::app()->createUrl('bonusLibrary/bonus_distried&type=1&city_id=' . $city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
        &nbsp;
        <?php echo CHtml::link('分公司未分配', Yii::app()->createUrl('bonusLibrary/bonus_distring&type=1&city_id=' . $city_id), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    <?php } ?>

<?php } else { ?>
    &nbsp;
    <?php echo CHtml::link('按渠道查看', Yii::app()->createUrl('bonusLibrary/channel_bonus'), array('class' => 'btn btn-success', 'target' => '_self')); ?>
    &nbsp;
    <?php
    echo CHtml::link('已分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distried'), array('class' => 'btn btn-success', 'target' => '_self'));
    ?>

    &nbsp;
    <?php echo CHtml::link('未分配实体卷', Yii::app()->createUrl('bonusLibrary/bonus_distring'), array('class' => 'btn btn-success', 'target' => '_self')); ?>

<?php } ?>
<div class="search-form" style="display:block;">
    <?php

    $this->renderPartial('_search_distried', array(
        'model' => $model,
        'is_manager' => $is_manager,
        'type' => $type,
        'channel' => $channel,
        'arr_dis'=>$arr_dis,
        'show_type'=>$show_type,
        'dateStart'=>$dateStart,
        'dateEnd'=>$dateEnd
    ));
    ?>
</div>

<?php

if ($show_type == 0 || $show_type == 1) {

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
} elseif ($show_type == 2 || $show_type == 3) {
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
                'header' => '未分配',
                'value' => '$data["money"]==""?0:$data["money"]',
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
                'value' => '$data["update_by"]==0?0:$data["update_by"]',
            ),
        ),
    ));
} else {
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
//            array(
//                'name' => 'channel',
//                'header' => '已分配',
//                'type' => 'raw',
//                'value' => '$data["channel"]==""?0:$data["channel"]',
//            ),
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
//            array(
//                'name' => 'money',
//                'type' => 'raw',
//                'header' => '未分配',
//                'value' => '$data["money"]==""?0:$data["money"]',
//            ),
            array(
                'name' => 'owner',
                'header' => '回收率',
                'type' => 'raw',
                'value' => '$data["bonus_sn"]==0?0:(round($data["number"]/$data["bonus_sn"], 2)*100)."%"',
            ),
            array(
                'name' => 'contact',
                'header' => '联系人',
                'type' => 'raw',
                'value' => '$data["contact"]',
            ),
            array(
                'name' => 'tel',
                'header' => '联系电话',
                'type' => 'raw',
                'value' => '$data["tel"]',
            ),
            array(
                'name' => 'distri_by',
                'header' => '分配人',
                'type' => 'raw',
                'value' => '$data["distri_by"]',
            ),
        ),
    ));
}
?>


<?php

if ($show_type == 0) {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid',
        'dataProvider' => $repdp,
        'itemsCssClass' => 'table table-striped',
        'enableSorting' => FALSE,
        'columns' => array(
            array(
                'name' => 'channel',
                'header' => '城市名称',
                'type' => 'raw',
                'value' => 'Dict::item("city",$data["city_id"])',
            ),
            array(
                'name' => 'channel',
                'header' => '渠道名称',
                'type' => 'raw',
                //'value' => 'Dict::item(\'bonus_channel\',$data["channel"])',
            ),
            array(
                'name' => 'number',
                'header' => '实体卷编号',
                'type' => 'raw',
                'value' => '$data["number"]',
            ),
            array(
                'name' => 'name',
                'header' => '实体卷名称',
                'type' => 'raw',
                'value' => '$data["name"]',
            ),
            array(
                'name' => 'num_all',
                'header' => '创建时间',
                'type' => 'raw',
                'value' => 'substr($data["created"],0,16)',
            ),
            array(
                'name' => 'date',
                'header' => '日期限制',
                'type' => 'raw',
                'value' => '
                "生效日期：".substr($data["effective_date"],0,16)."<br>".
                "绑定截止：".substr($data["binding_deadline"],0,16)."<br>".
                "使用截止：".substr($data["end_date"],0,16)."<br>"
            ',
            ),
            array(
                'name' => 'channel_limited',
                'htmlOptions' => array(
                    'style' => 'width:18%;'
                ),
                'header' => '类型限制',
                'type' => 'raw',
                'value' => '
                "使用：".Dict::item(\'channel_limited\',$data["channel_limited"])
            ',
            ),
            array(
                'name' => 'money',
                'type' => 'raw',
                'header' => '金额',
                'value' => '$data["money"]',
            ),
            array(
                'name' => 'distri_by',
                'header' => '分配人',
                'type' => 'raw',
                'value' => '$data["distri_by"]',
            ),
            array(
                'name' => 'distri_type',
                'header' => '类型',
                'type' => 'raw',
                'value' => '$data["distri_type"]==0?"销售":"分配"',
            ),
            array(
                'name' => 'is_use',
                'header' => '状态',
                'type' => 'raw',
                'value' => '$data["is_use"]==1?"已使用":"未使用"',
            ),
            array(
                'name' => 'use_date',
                'header' => '使用时间',
                'type' => 'raw',
                'value' => 'empty($data["use_date"])?"无":substr($data["use_date"],0,16)',
            ),
            array(
                'header' => '订单号',
                'headerHtmlOptions' => array(
                    'width' => '40px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data["order_id"]==0?"无":CHtml::link("详情", array("/order/view", "id"=>$data["order_id"]))'
            ),
        ),
    ));
} elseif ($show_type == 1) {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid',
        'dataProvider' => $repdp,
        'itemsCssClass' => 'table table-striped',
        'enableSorting' => FALSE,
        'columns' => array(
            array(
                'name' => 'channel',
                'header' => '城市名称',
                'type' => 'raw',
                'value' => 'Dict::item("city",$data["city_id"])',
            ),
            array(
                'name' => 'number',
                'header' => '实体卷编号',
                'type' => 'raw',
                'value' => '$data["number"]',
            ),
            array(
                'name' => 'name',
                'header' => '实体卷名称',
                'type' => 'raw',
                'value' => '$data["name"]',
            ),
            array(
                'name' => 'num_all',
                'header' => '创建时间',
                'type' => 'raw',
                'value' => 'substr($data["created"],0,16)',
            ),
            array(
                'name' => 'date',
                'header' => '日期限制',
                'type' => 'raw',
                'value' => '
                "生效日期：".substr($data["effective_date"],0,16)."<br>".
                "绑定截止：".substr($data["binding_deadline"],0,16)."<br>".
                "使用截止：".substr($data["end_date"],0,16)."<br>"
            ',
            ),
            array(
                'name' => 'channel_limited',
                'htmlOptions' => array(
                    'style' => 'width:18%;'
                ),
                'header' => '类型限制',
                'type' => 'raw',
                'value' => '
                "使用：".Dict::item(\'channel_limited\',$data["channel_limited"])
            ',
            ),
            array(
                'name' => 'money',
                'type' => 'raw',
                'header' => '金额',
                'value' => '$data["money"]',
            ),
            array(
                'name' => 'distri_by',
                'header' => '分配人',
                'type' => 'raw',
                'value' => '$data["distri_by"]',
            ),
            array(
                'name' => 'status',
                'header' => '类型',
                'type' => 'raw',
                'value' => '$data["status"]==2?"坏卡":"未分配"',
            ),
            array(
                'name' => 'is_use',
                'header' => '状态',
                'type' => 'raw',
                'value' => '$data["is_use"]==0?"未使用":"已使用"',
            ),
            array(
                'name' => 'use_date',
                'header' => '使用时间',
                'type' => 'raw',
                'value' => 'empty($data["use_date"])?"无":substr($data["use_date"],0,16)',
            ),
            array(
                'header' => '订单号',
                'headerHtmlOptions' => array(
                    'width' => '40px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data["order_id"]==0?"无":CHtml::link("详情", array("/order/view", "id"=>$data["order_id"]))'
            ),
        ),
    ));

} elseif ($show_type == 2||$show_type == 3) {
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid',
        'dataProvider' => $repdp,
        'itemsCssClass' => 'table table-striped',
        'enableSorting' => FALSE,
        'columns' => array(
            array(
                'name' => 'channel',
                'header' => '渠道名称',
                'type' => 'raw',
                //'value' => 'Dict::item(\'bonus_channel\',$data["channel"])',
            ),
            array(
                'name' => 'number',
                'header' => '实体卷编号',
                'type' => 'raw',
                'value' => '$data["number"]',
            ),
            array(
                'name' => 'name',
                'header' => '实体卷名称',
                'type' => 'raw',
                'value' => '$data["name"]',
            ),
            array(
                'name' => 'num_all',
                'header' => '创建时间',
                'type' => 'raw',
                'value' => 'substr($data["created"],0,16)',
            ),
            array(
                'name' => 'date',
                'header' => '日期限制',
                'type' => 'raw',
                'value' => '
                "生效日期：".substr($data["effective_date"],0,16)."<br>".
                "绑定截止：".substr($data["binding_deadline"],0,16)."<br>".
                "使用截止：".substr($data["end_date"],0,16)."<br>"
            ',
            ),
            array(
                'name' => 'channel_limited',
                'htmlOptions' => array(
                    'style' => 'width:18%;'
                ),
                'header' => '类型限制',
                'type' => 'raw',
                'value' => '
                "使用：".Dict::item(\'channel_limited\',$data["channel_limited"])
            ',
            ),
            array(
                'name' => 'money',
                'type' => 'raw',
                'header' => '金额',
                'value' => '$data["money"]',
            ),
            array(
                'name' => 'distri_by',
                'header' => '分配人',
                'type' => 'raw',
                'value' => '$data["distri_by"]',
            ),
            array(
                'name' => 'distri_type',
                'header' => '类型',
                'type' => 'raw',
                'value' => '$data["distri_type"]==0?"销售":"分配"',
            ),
            array(
                'name' => 'is_use',
                'header' => '状态',
                'type' => 'raw',
                'value' => '$data["is_use"]==1?"已使用":"未使用"',
            ),
            array(
                'name' => 'use_date',
                'header' => '使用时间',
                'type' => 'raw',
                'value' => 'empty($data["use_date"])?"无":$data["use_date"]',
            ),
            array(
                'header' => '订单号',
                'headerHtmlOptions' => array(
                    'width' => '40px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data["order_id"]==0?"无":CHtml::link("详情", array("/order/view", "id"=>$data["order_id"]))'
            ),
        ),
    ));
} else {

    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'bonus-library-grid',
        'dataProvider' => $repdp,
        'itemsCssClass' => 'table table-striped',
        'enableSorting' => FALSE,
        'columns' => array(

            array(
                'name' => 'number',
                'header' => '实体卷编号',
                'type' => 'raw',
                'value' => '$data["number"]',
            ),
            array(
                'name' => 'name',
                'header' => '实体卷名称',
                'type' => 'raw',
                'value' => '$data["name"]',
            ),
            array(
                'name' => 'num_all',
                'header' => '创建时间',
                'type' => 'raw',
                'value' => 'substr($data["created"],0,16)',
            ),
            array(
                'name' => 'date',
                'header' => '日期限制',
                'type' => 'raw',
                'value' => '
                "生效日期：".substr($data["effective_date"],0,16)."<br>".
                "绑定截止：".substr($data["binding_deadline"],0,16)."<br>".
                "使用截止：".substr($data["end_date"],0,16)."<br>"
            ',
            ),
            array(
                'name' => 'channel_limited',
                'htmlOptions' => array(
                    'style' => 'width:18%;'
                ),
                'header' => '类型限制',
                'type' => 'raw',
                'value' => '
                "使用：".Dict::item(\'channel_limited\',$data["channel_limited"])
            ',
            ),
            array(
                'name' => 'money',
                'type' => 'raw',
                'header' => '金额',
                'value' => '$data["money"]',
            ),
            array(
                'name' => 'distri_by',
                'header' => '分配人',
                'type' => 'raw',
                'value' => '$data["distri_by"]',
            ),
            array(
                'name' => 'distri_type',
                'header' => '类型',
                'type' => 'raw',
                'value' => '$data["distri_type"]==0?"销售":"分配"',
            ),
            array(
                'name' => 'status',
                'header' => '状态',
                'type' => 'raw',
                'value' => '$data["is_use"]==1?"已使用":"未使用"',
            ),
            array(
                'name' => 'use_date',
                'header' => '使用时间',
                'type' => 'raw',
                'value' => 'empty($data["use_date"])?"无":substr($data["use_date"],0,16)',
            ),
            array(
                'header' => '订单号',
                'headerHtmlOptions' => array(
                    'width' => '40px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data["order_id"]==0?"无":CHtml::link("详情", array("/order/view", "id"=>$data["order_id"]))'
            ),
        ),
    ));
}
?>

