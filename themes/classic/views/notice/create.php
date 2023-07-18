<?php
$this->pageTitle = '新建公告';

$this->breadcrumbs=array(
	'Notices'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Notice', 'url'=>array('index')),
	array('label'=>'Manage Notice', 'url'=>array('admin')),
);
?>

<h1>新建公告</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>