<?php
$this->pageTitle = '修改用户信息';
?>

<h1>修改用户信息</h1>
<hr class="divider"/>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>