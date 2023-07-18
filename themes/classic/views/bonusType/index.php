<?php
$this->breadcrumbs=array(
	'Bonus Types',
);

$this->menu=array(
	array('label'=>'Create BonusType', 'url'=>array('create')),
	array('label'=>'Manage BonusType', 'url'=>array('admin')),
);
?>

<h1>Bonus Types</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
