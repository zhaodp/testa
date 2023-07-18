<?php
/* @var $this DriverBalanceLogController */
/* @var $model DriverBalanceLog */

$this->breadcrumbs = array(
    'Driver Balance Logs' => array('index'),
    'Manage',
);

$this->menu = array(
    array('label' => 'List DriverBalanceLog', 'url' => array('index')),
    array('label' => 'Create DriverBalanceLog', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    var DriverBalanceLog_created = $('#DriverBalanceLog_created').val();
    if(DriverBalanceLog_created != '' && DriverBalanceLog_created < '2013-07-01'){
        alert('选择时间不能小于2013年7月1日。');
        return false;
    }
    $('#driver-balance-log-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

    <h1>信息费历史账单</h1>

    <div class="search-form" style="display:''">
        <div class="well clearfix">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'action' => Yii::app()->createUrl($this->route),
                'method' => 'get',
            )); ?>

            <div class="row span3">
                <?php echo $form->label($model, 'driver_id'); ?>
                <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10)); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'name'); ?>
                <?php echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20)); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'created'); ?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $created_date = isset($model) ? $model->attributes['created'] : date('Y-m-d');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'DriverBalanceLog[created]',
                    'model' => $model, //Model object
                    'value' => $created_date,
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                ));
                ?>
            </div>

            <div class="row buttons span2">
                <?php echo $form->label($model, '&nbsp;'); ?>
                <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
            </div>

            <?php $this->endWidget(); ?>

        </div>
    </div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'driver-balance-log-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        'driver_id',
        'name',
        'balance',
        'order_id',
        array(
            'name' => 'type',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'value' => '($data->type == 5 &&  $data->order_id != 0) ? "订单重结" : Dict::item("account_type",$data->type)'
        ),
        'created',
//        array(
//            'class' => 'CButtonColumn',
//        ),
    ),
)); ?>