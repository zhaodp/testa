<?php
$this->pageTitle = '新建优惠券';
?>

<h1><?php echo $this->pageTitle; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>