<?php
/* @var $this BonusLibraryController */
/* @var $model BonusLibrary */

$this->breadcrumbs=array(
	'Bonus Libraries'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List BonusLibrary', 'url'=>array('index')),
	array('label'=>'Create BonusLibrary', 'url'=>array('create')),
	array('label'=>'View BonusLibrary', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage BonusLibrary', 'url'=>array('admin')),
);
?>

<h1>Update BonusLibrary <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>