<hr/>
<form id='examForm' class="span12" enctype="multipart/form-data" action="<?php echo Yii::app()->createUrl('/customerExam/createExam');?>" method="POST">
	
		<div class="span3">
			<label>城市选择</label>
			<?php echo CHtml::dropDownList('city_id', '0',Dict::items('city'))?>
			<label>生成考卷题数</label>
			<?php echo CHtml::textField('exam_num','0', array('size'=>5,'maxlength'=>3))?>
			<label>服务规范题目数</label>
			<?php echo CHtml::textField('service', '0', array('size'=>5,'maxlength'=>'3'))?>
			<label>交通规则题目数</label>
			<?php echo CHtml::textField('traffic','0', array('size'=>5,'maxlength'=>'3'))?>
			<label>地理地图题目数</label>
			<?php echo CHtml::textField('map','0', array('size'=>5,'maxlength'=>'3'))?>
		</div>	
		<div class="span3">
			<?php echo CHtml::submitButton('打印司机考卷',array('class'=>'btn btn-success','id' => 'createExam','name'=>'createExam')); ?>
		</div>
</form>
<script language='javascript' type='text/javascript'>
$('#createExam').click(function(){
	var exam_num = $('#exam_num').val();
	if(trim(exam_num) == ''){
		alert('请输入考卷题目数.');
		return false;
	}
	alert('adfadsf');
	return false;
});

</script>