<div class="row-fluid">
    <?php
    $this->widget('zii.widgets.grid.CGridView',
        array(
            'id' => 'customer-main-grid',
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
                array(
                    'name' => '日期',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data["month"]',
                ),
                array(
                    'name' => '按成单分成',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data["type_1"]',
                ),
                array(
                    'name' => '按新客成单分成',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data["type_2"]',
                ),
                array(
                    'name' => '按流水分成',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data["type_3"]',
                ),
                array(
                    'name' => '按老客成单分成',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data["type_4"]',
                ),
                array(
                    'name' => '总计',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data["cast"]',
                ),

                array(
                    'header' => '付款状态',
                    'htmlOptions' => array(
                        'style' => 'width:85px;'
                    ),
                    'class' => 'CButtonColumn',
                    'template' => '{pay} {payed} ',
                    'buttons' => array(
                        'pay' => array(
                            'label' => '付款',
                            'visible' => '$data["status"] == ThirdBillStatus::STATUS_UN_SETTLE && strlen($data["month"]) == 7',
                            'url' => 'Yii::app()->createUrl("thirdStage/pay", array("channel"=>$data["channel"], "month" => $data["month"]))',
                            'click' => 'function(){
                        $(\'#auditIframe\').attr(\'src\',$(this).attr(\'href\'));
                        $(\'#auditIframe\').show();
                        $("#auditDialog").dialog("open");
                        return false;
                    }'
                        ),
                        'payed' => array(
                            'label' => '已付款',
                            'visible' => '$data["status"] == ThirdBillStatus::STATUS_SETTLED',
                        ),
                    ),
                ),
            ),
        )
    ); ?>
</div>

<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'auditDialog',
    'options' => array(
        'title' => '付款',
        'autoOpen' => false,
        'width' => '600',
        'height' => '450',
        'buttons' => array('关闭' => 'js:function(){$("#auditDialog").dialog("close");}')
    ),
));
?>
<iframe id="auditIframe" src="" style="width:550px;height:330px;border:0px;margin:0px;display:none;"></iframe>
<?php
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
