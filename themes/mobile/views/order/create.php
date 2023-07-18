<?php
$this->pageTitle = Yii::app()->name . ' - 订单补录';
?>

<h3>订单补录</h3>

<?php echo $this->renderPartial('_order_form', array('model'=>$model)); ?>