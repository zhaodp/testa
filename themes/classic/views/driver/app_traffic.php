<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-6-24
 * Time: 下午5:52
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '流量统计管理';
?>

<h1>流量统计管理</h1>
<hr class="divider"/>

<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form">
    <?php $this->renderPartial('_search_app_traffic',array(
        'model'=>$model,
    )); ?>
</div>
<?php


$dataProvider = $model->search();
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'driver-grid',
    'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped ',
    'columns'=>array(
        'driver_id',
        'e_receive_total',
        'e_send_total',
        'phone_receive_total',
        'phone_send_total',
        'device',
        'app_ver',
        'in_date' ,
    ),
)); ?>

