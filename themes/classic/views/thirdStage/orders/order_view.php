<?php
$this->pageTitle = '订单数据查看 '.$dateStart.' -- '.$dateEnd;
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#customer-main-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>
<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>

    <div class="span12">
        <?php Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker'); ?>
        <div class="span3">
            <label>开始时间</label>
            <?php  $this->widget('CJuiDateTimePicker', array(
                'name' => 'dateStart',
                'value' =>  $dateStart,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));?>
        </div>
        <div class="span3">
            <label>结束时间</label>
            <?php  $this->widget('CJuiDateTimePicker', array(
                'name' => 'dateEnd',
                'value' => $dateEnd,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        </div>

    </div>
    <?php $this->endWidget(); ?>
</div>

<div class="row-fluid">
    <?php
    $this->widget('zii.widgets.grid.CGridView',
        array(
            'id' => 'customer-main-grid',
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
                array(
                    'name' => 'date',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                ),
                array(
                    'name' => 'orderCount',
                    'type' => 'raw',
                ),
                array(
                    'name' => 'customerCount',
                    'type' => 'raw',
                ),
                array(
                    'name' => 'inviteCount',
                    'type' => 'raw',
                ),
            ),
        )
    ); ?>
</div>

