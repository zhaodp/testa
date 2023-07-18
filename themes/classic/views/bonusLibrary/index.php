<?php
/* @var $this BonusLibraryController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Bonus Libraries',
);

$this->menu=array(
	array('label'=>'Create BonusLibrary', 'url'=>array('create')),
	array('label'=>'Manage BonusLibrary', 'url'=>array('admin')),
);
?>

<h1>Bonus Libraries</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
