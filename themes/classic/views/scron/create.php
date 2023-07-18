<?php
$this->pageTitle = '添加定时任务';
?>
<h1><?php echo $this->pageTitle;?></h1>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
