<?php $this->pageTitle = '新建长文章';?>
<?php if ($model->isNewRecord) { ?>
    <h1>新建长文章</h1>
<?php } else { ?>
    <h1>修改长文章</h1>
<?php } ?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>