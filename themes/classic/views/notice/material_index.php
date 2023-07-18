<?php
/* @var $this DriverTrainDataController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Driver Train Datas',
);
?>

<h1>前台管理</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_material_view',
)); ?>
