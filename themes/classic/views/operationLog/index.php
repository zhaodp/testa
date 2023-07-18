<?php

$this->pageTitle = '操作日志管理';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#operation-log-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php $this->renderPartial('_search',array(
    'model'=>$model,
)); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'operation-log-grid',
    'dataProvider'=>$model->search(),
    //'filter'=>$model,
    'columns'=>array(
        'id',
        'route',
        //'mod_name',
        //'mod_code',
        'opt_type'=>array(
            'name'=>'opt_type',
            'value'=>'$data->getModTypeConfig($data->opt_type)'
        ),
        //'data_log',
        'opt_user',
        'created',
        /*array(
            'class'=>'CButtonColumn',
        ),*/
    ),
)); ?>
