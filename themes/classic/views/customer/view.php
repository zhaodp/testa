<?php
/* @var $this QuestionnaireController */
/* @var $model Questionnaire */

$this->breadcrumbs=array(
	'Questionnaires'=>array('index'),
	$model->name,
);
?>

<h1>回访详情</h1>
<hr/>
<p><b>客户姓名:</b><?php echo $model->name; ?>&nbsp;&nbsp;&nbsp;&nbsp;<b>客户电话:</b><?php echo $model->phone; ?></p>
<p><b>接通问候:</b><?php echo ($model->status == 0)? "未回访":(($model->status == 1)? "已成功":(($model->status == 2)? "不记得使用过":"不方便"))?></p>
<?php if($model->status != 1){?>
<p><b>预约呼叫:</b><?php echo ($model->visit_time == "0000-00-00 00:00:00" && $model->status >= 2)?"拒绝访问":(($model->visit_time == "0000-00-00 00:00:00") ? "空" : "可预约</p><p><b>预约时间：</b>".$model->visit_time);?></p>
<?php }else{
	foreach ($customer_list as $c_list){
		if(in_array($c_list['id'], $vid)){
			echo "<p><b>".$c_list['id']."、".$c_list['title']."</b></p>";
			$contents = json_decode($c_list['contents']);
			$choice = $answer[$c_list['id']]['answer'];
			echo "<p>回答：".$contents[$choice].($answer[$c_list['id']]['answer_ext']!=''?"（".$answer[$c_list['id']]['answer_ext'].")":"")."<p/>";
		}
	}
}
?>


