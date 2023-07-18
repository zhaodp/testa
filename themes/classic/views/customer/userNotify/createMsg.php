<?php
/* @var $this UserNotifyMsgController */
/* @var $model UserNotifyMsg */

$this->breadcrumbs=array(
	'User Notify Msgs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List UserNotifyMsg', 'url'=>array('index')),
	array('label'=>'Manage UserNotifyMsg', 'url'=>array('admin')),
);
?>
    <h2>设置新的通知（2/2）</h2>
    <h1>push和客户端弹屏提示</h1>

<?php $this->renderPartial('userNotify/_formMsg', array('model'=>$model)); ?>