<?php
/**
 * User: zhanglimin
 * Date: 13-7-30
 * Time: 下午3:02
 */
$this->pageTitle = '结伴返城管理';

echo "<h1>结伴返城管理</h1>";

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#driver-goback-log-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'driver-goback-log-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'id',
        'driver_id',
        'goback'=>array(
            'filter'=>DriverGobackLog::$goback,
            'name'=>'goback',
            'value'=>'DriverGobackLog::$goback[$data->goback]',
        ),
        'lng',
        'lat',
        array(
          'header'=>'地址',
          'value'=>'$data->getAddress($data->lng,$data->lat)',
        ),
        'status'=>array(
            'filter'=>DriverGobackLog::$goback_stauts,
            'name'=>'status',
            'value'=>'DriverGobackLog::$goback_stauts[$data->status]',
        ),
        'created',
    ),
)); ?>