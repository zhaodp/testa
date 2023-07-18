<?php
$this->pageTitle = '修改队列属性';
?>
<h1><?php echo $this->pageTitle;?></h1>


<?php echo $this->renderPartial('_update_form', array('model'=>$model)); ?>
