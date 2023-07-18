<?php
/* @var $this ActivityController */
/* @var $model BActivity */
$h = ($model->isNewRecord) ? '创建活动' : '修改活动';
$this->pageTitle = $h . ' - ' . $this->pageTitle;
?>

<h1><?php echo $h; ?></h1>

<p>
    <?php echo CHtml::link('返回活动列表', array('activity/admin'), array('class' => 'btn')); ?>
</p>

<?php $this->renderPartial('_form', array('model' => $model)); ?>