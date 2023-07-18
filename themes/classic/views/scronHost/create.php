<?php
$this->pageTitle = '添加主机';
?>
<h1><?php echo $this->pageTitle;?></h1>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
