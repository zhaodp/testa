<?php
/* @var $this BonusCodeController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Bonus Codes',
);

$this->menu=array(
	array('label'=>'Create BonusCode', 'url'=>array('create')),
	array('label'=>'Manage BonusCode', 'url'=>array('admin')),
);
?>

<h1>Bonus Codes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
