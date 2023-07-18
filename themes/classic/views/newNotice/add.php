<?php $this->pageTitle = '新建公告';?>

<?php if($model->isNewRecord) { ?>
    <h1>新建公告</h1>
<?php }else{ ?>
    <h1>修改公告</h1>
<?php } ?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>