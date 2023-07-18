<?php $this->pageTitle = Yii::app()->name . ' - 添加投诉'; ?>
<h1>添加投诉</h1>
<?php echo $this->renderPartial('_form_add', array('model'=>$model,'typelist'=>$typelist)); ?>