<?php
	$this->pageTitle = '考试试题';
?>
<h1><?php echo $this->pageTitle;?></h1>
<?php echo $this->renderPartial('_eform', array('model'=>$model)); ?>