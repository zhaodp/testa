<?php
$this->pageTitle = 'VIP消费跟进管理 - ' . $this->pageTitle;
?>

<h1>VIP消费跟进管理</h1>

<div class="search-form">
    <?php $this->renderPartial('record/_search', array('model' => $model)); ?>
</div>

<?php
$criteria = new CDbCriteria();
if (($model->aveCost !== null && $model->aveCost !== '')||
        ($model->changeRate !== null && $model->changeRate !== '')||
        ($model->changeCost !== null && $model->changeCost !== '')) {
    $criteria->with = 'vipCostExt';         //连接vipCostExt（联合搜索）
    $criteria->compare('vipCostExt.ave_cost', $model->aveCostType . $model->aveCost);
    $criteria->compare('vipCostExt.change_rate_cost', $model->changeRateType . $model->changeRate);
    $criteria->compare('vipCostExt.change_cost', $model->changeCostType . $model->changeCost);
}
$recordStatus = $model->recordStatus;
if ($recordStatus !== null && $recordStatus !== '') {
    $recordVipIds = VipRecordRedis::model()->getLastRecordThisWeekVipIds();
    if ($recordStatus == 0) {
        $criteria->addNotInCondition('t.id', $recordVipIds);
    } else if ($recordStatus == 1) {
        $criteria->addInCondition('t.id', $recordVipIds);
    }
}


$ajaxStr = '$("#iframeDialog").dialog("open");$(".ui-dialog-title").html($(this).attr("title"));$("#operationFrame").attr("src",$(this).attr("href"));return false;';
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'vip-grid',
    'dataProvider' => $model->search($criteria),
    'enableSorting' => FALSE,
    'cssFile' => SP_URL_CSS . 'table.css',
    'itemsCssClass' => 'table  table-condensed',
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'rowCssClassExpression' => array($this, 'amountStatus'),
    'columns' => array(
        array(
            'name' => 'id',
            'type' => 'raw',
            'value' => '
                CHtml::link($data->id, array("vip/view", "id"=>$data->id), array("target"=>"_blank"))
                ."<br>"
                .Yii::app()->controller->getStatus($data->status)
            ',
            'htmlOptions' => array(
                'style' => 'width:90px;'
            ),
        ),
        array(
            'header' => 'VIP信息',
            'type' => 'raw',
            'value' => '
                $data->name
                ."&nbsp;"
                .Yii::app()->controller->showPhoneNumber($data)
                ."<br>"
                .$data->company
            ',
            'htmlOptions' => array(
                'style' => 'width:150px;'
            ),
        ),
        array(
            'header' => '平均周消费(金额/单数)',
            'value' => '$data->vipCostExt ? ((int)$data->vipCostExt->ave_cost."(".(int)$data->vipCostExt->ave_count.")") : "--(--)"',
            'htmlOptions' => array(
                'style' => 'width:90px;'
            ),
        ),
        array(
            'header' => '上上一周消费(金额/单数)',
            'value' => '$data->vipCostExt ? ((int)$data->vipCostExt->last_second_week_cost."(".(int)$data->vipCostExt->last_second_week_count.")") : "--(--)"',
            'htmlOptions' => array(
                'style' => 'width:90px;'
            ),
        ),
        array(
            'header' => '上一周消费(金额/单数)',
            'value' => '$data->vipCostExt ? ((int)$data->vipCostExt->last_week_cost."(".(int)$data->vipCostExt->last_week_count.")") : "--(--)"',
            'htmlOptions' => array(
                'style' => 'width:90px;'
            ),
        ),
        array(
            'header' => '变化量(金额/单数)',
            'value' => '$data->vipCostExt ? ((int)$data->vipCostExt->change_cost."(".(int)$data->vipCostExt->change_count.")") : "--(--)"',
            'htmlOptions' => array(
                'style' => 'width:90px;'
            ),
        ),
        array(
            'header' => '变化率(金额/单数)',
            'value' => '$data->vipCostExt ? ((int)$data->vipCostExt->change_rate_cost."%(".(int)$data->vipCostExt->change_rate_count."%)") : "--(--)"',
            'htmlOptions' => array(
                'style' => 'width:90px;'
            ),
        ),
        array(
            'header' => '最近代驾距今(天)',
            'value' => array($this, 'lastOrderTime'),
            'htmlOptions' => array(
                'style' => 'width:70px;'
            ),
        ),
        array(
            'header' => '跟进状态',
            'type' => 'raw',
            'value' => '@Common::isSameWeek(VipRecord::model()->getLastRecord($data->id)->create_time, time()) ? "<span style=\"color:green\">已处理</span>" : "未处理"',
            'htmlOptions' => array(
                'style' => 'width:70px;'
            ),
        ),
        array(
            'header' => '上次备注',
            'type' => 'raw',
            'htmlOptions' => array(
                'style' => 'width:250px;'
            ),
            'value' => '($lastRecord=VipRecord::model()->getLastRecord($data->id)) ? ("<font title=\"$lastRecord->mark_content\">".Common::wsubstr($lastRecord->mark_content, 0, 100) ."</font><br>". date("Y-m-d H:i:s", $lastRecord->create_time) ."<br>". $lastRecord->operator_id) : "无记录"',
        ),
        array(
            'header' => '操作',
            'class' => 'CButtonColumn',
            'template' => '{addRecord} <br> {recordLog} <br> {costTrend}',
            'htmlOptions' => array(
                'style' => 'width:60px;'
            ),
            'buttons' => array(
                'addRecord' => array(
                    'label' => '添加备注',
                    'url' => 'Yii::app()->createUrl("vip/record", array("vipId"=>$data->id, "dialog"=>1))',
                    'options' => array('onclick' => $ajaxStr),
                ),
                'recordLog' => array(
                    'label' => '跟进记录',
                    'url' => 'Yii::app()->createUrl("vip/recordList", array("vipId"=>$data->id, "dialog"=>1))',
                    'options' => array('onclick' => $ajaxStr),
                ),
                'costTrend' => array(
                    'label' => '消费趋势',
                    'url' => 'Yii::app()->createUrl("vip/costWeekList", array("vipId"=>$data->id, "dialog"=>1))',
                    'options' => array('onclick' => $ajaxStr),
                ),
            ),
        ),
    ),
));
?>


<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'iframeDialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '添加备注',
        'autoOpen' => false,
        'width' => '800',
        'height' => '600',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#iframeDialog").dialog("close");}'
        ),
    ),
));
echo '<iframe id="operationFrame" width="100%" height="99%" style="border:0px;padding:0px;margin:0px;"></iframe>';
$this->endWidget();
?>

<script>
    function reloadItems() {
        $("#iframeDialog").dialog("close");
        $.fn.yiiGridView.update('vip-grid');
    }
</script>