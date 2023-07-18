<?php
/* @var $this BonusLibraryController */
/* @var $model BonusLibrary */

$this->breadcrumbs=array(
	'Bonus Libraries'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List BonusLibrary', 'url'=>array('index')),
	array('label'=>'Manage BonusLibrary', 'url'=>array('admin')),
);
?>

<h1>Create BonusLibrary</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>