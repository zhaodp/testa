<?php
$this->pageTitle = '更新消息内容';
?>

<h1><?php echo $this->pageTitle; ?></h1>
<h3><?php echo MessageText::$messageDesc[$model->code]['desc']; ?></h3> 

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
