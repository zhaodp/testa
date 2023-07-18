<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs=array(
	'Vips'=>array('index'),
	'Create',
);

$this->pageTitle = '新建Vip卡';
?>

<h1>新建Vip 主卡</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>