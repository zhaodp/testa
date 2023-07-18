<?php
/* @var $this PartnerController */
/* @var $model Partner */

/*$this->breadcrumbs=array(
	'Partners'=>array('index'),
	'Create',
);*/
$this->pageTitle = '新增商家 - '.$this->pageTitle;
/*$this->menu=array(
	array('label'=>'List Partner', 'url'=>array('index')),
	array('label'=>'Manage Partner', 'url'=>array('admin')),
);*/
?>

<h1>新增商家</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'paySort' => $paySort,)); ?>