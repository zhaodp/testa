<?php
/* @var $this UserNotifyBannerController */
/* @var $model UserNotifyBanner */

$this->breadcrumbs=array(
	'User Notify Banners'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List UserNotifyBanner', 'url'=>array('index')),
	array('label'=>'Manage UserNotifyBanner', 'url'=>array('admin')),
);
?>

    <h2>设置新的通知（2/2）</h2>
    <h1>当前订单页面的banner</h1>

<?php $this->renderPartial('userNotify/_formBanner', array('model'=>$model)); ?>