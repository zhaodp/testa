<?php
	$this->pageTitle = "试题详情";
?>
<h1><?php echo $this->pageTitle?></h1>
<hr>
<div class="span12">
	<h4><span class="span6"><?php echo $model->id.'. 【'.Dict::item('exam_type', $model->type).'】'.$model->title; ?></span>
			<span class="span3"> 正确答案（<?php echo  strtoupper($model->correct);?>）</span></h4>
	<ul class="unstyled span12">
		<li>A. <?php echo $model->a; ?></li>
		<li>B. <?php echo $model->b; ?></li>
		<li>C. <?php echo $model->c; ?></li>
		<li>D. <?php echo $model->d; ?></li>
	</ul>
</div>