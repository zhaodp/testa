<?php
/* @var $this PartnerController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Partners',
);

$this->menu=array(
	array('label'=>'Create Partner', 'url'=>array('create')),
	array('label'=>'Manage Partner', 'url'=>array('admin')),
);
?>

<h1>Partners</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
