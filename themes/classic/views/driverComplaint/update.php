<?php
/* @var $this DriverComplaintController */
/* @var $model DriverComplaint */

$this->breadcrumbs=array(
	'Driver Complaints'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List DriverComplaint', 'url'=>array('index')),
	array('label'=>'Create DriverComplaint', 'url'=>array('create')),
	array('label'=>'View DriverComplaint', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DriverComplaint', 'url'=>array('admin')),
);
?>

<h1>Update DriverComplaint <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>