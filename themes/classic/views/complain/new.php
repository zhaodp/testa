<?php $this->pageTitle = Yii::app()->name . ' - 客服添加投诉'; ?>
    <h1>客服添加投诉</h1>
<?php echo $this->renderPartial('_form_cs_add', array('model'=>$model,'typelist' => $typelist)); ?>