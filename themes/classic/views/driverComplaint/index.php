<?php
/* @var $this DriverComplaintController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Driver Complaints',
);

$this->menu=array(
	array('label'=>'Create DriverComplaint', 'url'=>array('create')),
	array('label'=>'Manage DriverComplaint', 'url'=>array('admin')),
);
?>

<h1>Driver Complaints</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
