<?php
/* @var $this CustomerVisitBatchController */
/* @var $model CustomerVisitBatch */

$this->breadcrumbs=array(
	'Customer Visit Batches'=>array('index'),
	$model->id,
);

?>

<h4><font color='red'><?php echo $model->comment; ?>--调查统计</font></h4>
<br/>
<?php
foreach ($q_list as $key){
	echo '<p>',$key->id,'、',$key->title,'</p>';
	foreach (json_decode($key->contents) as $k => $items){
		$count = CustomerVisitAnswer::model()->getAnswer($model->id,$key->id,$k);
		$answer_count = CustomerVisitAnswer::model()->getAnswerCount($model->id,$key->id);
		$share = $count/$answer_count*100;
		echo $items,'&nbsp;&nbsp;&nbsp;&nbsp;回答次数百分比:',number_format($share,2),'%<br>';
	}
	echo '<br>';
} 
?>
<div class='rows'>
<form class='span12' enctype="multipart/form-data" action="<?php echo Yii::app()->createUrl('/customerVisitBatch/derive');?>" method="POST">
<input type='hidden' id='batch_id' name='batch_id' value='<?php echo $model->id; ?>'>
<?php echo CHtml::submitButton('导出该批次数据',array('class'=>'btn btn-success','name'=>'derive')); ?>
</form>
</div>
