	
<div class="block">

<div>
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
<?php
	$item_list = array('A','B','C','D','E','F');
	foreach (json_decode($question->contents) as $qkey => $q_list){
		if(!empty($q_list[$qkey])){
			echo "<input type='checkbox' value='".$item_list[$qkey]."' name='question[]' id='q_".$index."_".$item_list[$qkey]."'/>".$item_list[$qkey],'、',$q_list,'<br>';
		}
	} 
?>
</label>

</div>

</div>
<?php echo CHtml::submitButton('做下一题');?>
</div>
<?php 
}
?>

<?php 
if (empty($pass) && !empty($exam))
{
	$model = new Question();
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
			$q = $model-> findByPk($id);
			
			if (!empty($q))
			{
				$arrC = explode(',', $c);
				$exam_error_count ++;
				$list = json_decode($q->contents);
				
?>
<div class="row">
<div class="span9">
<H4><?php echo $i . '.' . $q->title?></H4>
<?php echo "以下是正确答案:"; ?>
</div>
<?php if (in_array('A', $arrC)) {?>
<div class="span9" style="color:#CC0000;font-size:1.2em;">
	<?php echo $list[0];?>
</div>
<?php }?>
<?php if (in_array('B', $arrC)) {?>
<div class="span9" style="color:#CC0000;font-size:1.2em;">
	<?php echo $list[1];?>
</div>
<?php }?>
<?php if (in_array('C', $arrC)) {?>
<div class="span9" style="color:#CC0000;font-size:1.2em;">
	<?php echo $list[2];?>
</div>
<?php }?>
<?php if (in_array('D', $arrC)) {?>
<div class="span9" style="color:#CC0000;font-size:1.2em;">
	<?php echo $list[3];?>
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

