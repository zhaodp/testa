<?php
/* @var $this ZhaopinController */
$this->pageTitle = '司机报名成功 - e代驾';

?>
<div class="span3 bs-docs-sidebar">
	<ul class="nav nav-list bs-docs-sidenav affix">
	</ul>
</div>		
<div class="span9">
	<section id="agreement" class="agreement">
		<div class="page-header">
			<h2>报名成功</h2>
		</div>
		<div>
<?php 		
		$info = "添加成功!你的报名流水号是"  . $this->getZhaopinQueueNumber($model->id, $model->city_id);
		echo $info;
?>				
		</div>
	</section>
</div>