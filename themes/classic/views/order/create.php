<?php
$this->pageTitle = Yii::app()->name . ' - 订单补录';
?>

<h1>订单补录</h1>

<?php echo $this->renderPartial('_order_form', array('model'=>$model)); ?>