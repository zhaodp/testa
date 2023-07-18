<?php
$this->pageTitle="添加试题";
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php echo $this->renderPartial('_ecform', array('model'=>$model)); ?>