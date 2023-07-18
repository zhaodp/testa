<?php
/* @var $this ZhaopinController */
$this->pageTitle = '在线考试  - e代驾';

?>	
<div class="block">
	<div style="height:90px;"></div>
	<div class="page-header">
	<h2>在线考试</h2>
	</div>
<div class="row">
<div class="span6">
<?php 

$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'post',
));

if (empty($question) && empty($exam))
{
?>
<label for="id_card">请输入身份证号</label>
	<input type="text" id="id_card" name="id_card" value="" style="float:left" />&nbsp;
<?php 
echo CHtml::submitButton('开始考试',array('class'=>'btn'));
} 
else 
{
	echo "<H4>您报名的身份证号是：" . $id_card . "</H4>";
	echo "<h4 id = 'error_info' style='color:#CC0000;'></h4>";
	echo CHtml::link('结束考试 ','javascript:viod(0);',array('onclick'=>'clearSession()'));
	
}
?>
</div>
</div>
<?php
if (!empty($question))
{
	 echo "<H2>第" . $index . "题</H2>";
?>
<div class="row">
<div class="span9">
<H4><?php echo $index . '.' . $question->title?></H4>
<input type="hidden" name="index" value="<?php echo $index?>" />
</div>
</div>
<div class="row">
<div class="span9">
<label id="qustion_a">
<input type="checkbox" value="A" name="question[]" id="q_<?php echo $index?>_a"> A．<?php echo $question->a;?> 
</label>
</div>
<div class="span9">
<label id="qustion_b">
<input type="checkbox" value="B" name="question[]" id="q_<?php echo $index?>_b"> B．<?php echo $question->b;?> 
</label>
</div>
<?php 
if (!empty($question->c))
{
?>
<div class="span9">
<label id="qustion_c">
<input type="checkbox" value="C" name="question[]" id="q_<?php echo $index?>_c"> C．<?php echo $question->c;?> 
</label>
</div>
<?php 
}
?>
<?php 
if (!empty($question->d))
{
?>
<div class="span9">
<label id="qustion_d">
<input type="checkbox" value="D" name="question[]" id="q_<?php echo $index?>_d"> D．<?php echo $question->d;?> 
</label>
</div>
<?php 
}
?>
</div>
<?php echo CHtml::submitButton('做下一题');?>
</div>
<?php 
}
?>

<?php 
if (empty($pass) && !empty($exam))
{
	$model = new DriverExam();
	$i = 1;
	$exam_count = count($exam);
	$exam_error_count = 0;
	foreach ($exam as $value)
	{
		$id = $value['id'];
		$k = $value['k'];
		$c = $value['c'];
		if ($k != $c)
		{
			$q = $model->findByPk($id);
			
			if (!empty($q))
			{
				$arrC = explode(',', $c);
				$exam_error_count ++;
				
?>
<div class="row">
<div class="span9">
<H4><?php echo $i . '.' . $q->title?></H4>
<?php echo "以下是正确答案:"; ?>
</div>
<?php if (in_array('A', $arrC)) {?>
<div class="span9" style="color:#CC0000;font-size:1.2em;">
	<?php echo $q->a;?>
</div>
<?php }?>
<?php if (in_array('B', $arrC)) {?>
<div class="span9" style="color:#CC0000;font-size:1.2em;">
	<?php echo $q->b;?>
</div>
<?php }?>
<?php if (in_array('C', $arrC)) {?>
<div class="span9" style="color:#CC0000;font-size:1.2em;">
	<?php echo $q->c;?>
</div>
<?php }?>
<?php if (in_array('D', $arrC)) {?>
<div class="span9" style="color:#CC0000;font-size:1.2em;">
	<?php echo $q->d;?>
</div>
<?php }?>
</div>
				
<?php
			}
		}
		$i ++;
	}
?>
<input type="hidden" name="examagain" value="1" />
<?php
	echo '<div id = "exam" style = "display:none">总共：'.$exam_count . '道题，答对' . ($exam_count - $exam_error_count) . '道，答错'. $exam_error_count .'道。</div>';
	echo CHtml::submitButton('重考');
}
if (!empty($pass))
	echo "<h2>考试通过, 请等待通知。</h2>";

$this->endWidget();
?>

<script>
$(document).ready(function(){
	$("#error_info").html($("#exam").html());
	});
function clearSession(){
	$.ajax({
		type: 'get',
		url: '<?php echo Yii::app()->createUrl('/zhaopin/AjaxClearSession');?>',
		dataType : 'html',
		success: function(html){
			if(html == 1)
				window.location.replace(window.location.href);
	}});
}
</script>

