<?php
/* @var $this CustomerExamController */
/* @var $model CustomerExam */

$this->breadcrumbs=array(
	'Customer Exams'=>array('index'),
	$model->exam_title,
);
?>

<h1>问卷信息查看</h1>
<hr>
<p><b>问卷标题:</b><font color='red'><?php echo $model->exam_title?></font></p>
<p><b>该问卷总共有<font color='red'>
<?php 
	$strsql = 'select id from t_customer_exam_paper where exam_id ='.$model->id;
	$command = Yii::app()->db->createCommand($strsql);
	$count = $command->query()->count();
	echo $count;
	
?>
</font>题</b>&nbsp;&nbsp;&nbsp;&nbsp;答题率为
<?php 
	$selsql = 'select id from t_customer_visit_answer where exam_id ='.$model->id;
	$command = Yii::app()->db->createCommand($selsql);
	$q_count = $command->query()->count();
	$total = ($q_count / $count) * 100;
	echo number_format($total,2),'%';
?>
</p>
<p><b>问卷考题</b></p>
<p>
<?php
	foreach ($examlist as $k=>$exam){
		echo $k,'、',$examlist[$k],'<br>';
	}
?>
</p>