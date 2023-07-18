<?php
$this->pageTitle = '添加队列';
?>
<h1><?php echo $this->pageTitle;?></h1>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
