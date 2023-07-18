<?php
$this->pageTitle = '更改主机信息';
?>
    <h1><?php echo $this->pageTitle;?></h1>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>