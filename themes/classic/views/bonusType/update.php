<?php
$this->pageTitle = '更新优惠券';
?>

<h1><?php echo $this->pageTitle; echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>