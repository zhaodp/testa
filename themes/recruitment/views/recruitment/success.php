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
		$info = "<p>添加成功!你的报名流水号是"  . $this->getRecruitmentQueueNumber($model->id, $model->city_id)."</p>";
		$info.= "<p>请学习培训资料中 的内容，并参加在线考试。考试通过后，公司会通知您面试和路考。&nbsp;&nbsp; <a href='http://zhaopin.edaijia.cn/notice'>培训资料</a>&nbsp;&nbsp; <a href='http://zhaopin.edaijia.cn/exam'>在线考试</a></p>";
		echo $info;
?>				
		</div>
	</section>
</div>
