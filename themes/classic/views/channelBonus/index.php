<?php
$this->breadcrumbs=array(
	'Channel Bonuses',
);

$this->menu=array(
	array('label'=>'Create ChannelBonus', 'url'=>array('create')),
	array('label'=>'Manage ChannelBonus', 'url'=>array('admin')),
);
?>

<h1>Channel Bonuses</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
