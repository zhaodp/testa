<?php
/* @var $this CustomerExamController */
/* @var $model CustomerExam */
/* @var $form CActiveForm */
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customer-exam-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class='grid-view'>
	<p class='note'></p>
	<?php echo $form->errorSummary($model); ?>

	<label>答卷标题</label>
	<?php echo $form->textField($model,'exam_title',array('size'=>50,'maxlength'=>50)); ?>
	<label>答卷类型</label>
	<?php echo $form->dropDownList($model, 'type', array('1'=>'调查问卷','2'=>'司机考卷'));?>
	<label>创建时间</label>
	<?php echo $form->textField($model,'created',array('readonly'=>true,'value'=>$model->isNewRecord ? date('Y-m-d',time()) : $model->created)); ?>
	<label>答卷试题</label>
	<?php 
		$count = count($paperlist);
		if ($count>0){
			foreach ($paperlist as $k => $title){
		?>
		<input type='text' value="<?php echo $paperlist[$k];?>" size='20' style='width:300px' name='theText[]' />
		<input type='text' value="<?php echo $k; ?>" size='20' style='width:300px;display:none;' name='numText[]' />
		<br/>
		<?php 		
			}
		}else {
			?>
			<div id='exam_paper'></div>
			<input id='hiddensum' style='display:none' value='0'/>
			<?php 
		}
	?>
	<div id='exam_paper'></div>
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'customer-question-grid',
		'dataProvider'=>$question->search_list(),
		'itemsCssClass'=>'table table-striped',
		'columns'=>array(
			'id',
			'title',
			array(
				'name'=>'question_type',
				'headerHtmlOptions'=>array(
					'width'=>'20',
					'nowrap'=>'nowrap',
				),
				'value'=>'$data->question_type == 1 ? "问题" : "考题"',
			),
			array(
				'type'=>'raw',
				'value'=>'CHtml::textField("Text[$data->id]",$data->title,array("style"=>"display:none"))',
			),
			array(
				'name'=>'选择问题',
				'type'=>'raw',
				'value'=>'CHtml::Button("选择",array("onclick"=>"addText($data->id)"))',
			),
		),
	)); ?>
	
	<div class="buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存'); ?>
		<?php echo CHtml::Button('取消',array('onclick'=>'window.open("'.Yii::app()->createUrl('customerExam/index').'","_self","param")'));?>
	</div>

<?php $this->endWidget(); ?>
</div>
</div><!-- form -->
<script language='javascript' type='text/javascript'>
$('#customer-exam-form').submit(function(){
	var title = $('#CustomerExam_exam_title').val();
	if (title == '' || title == null){
		alert('请输入问卷题目');
		$('#exam_title').focus();
		return false;
	}
})
function addText($qid){
	var str_text = $('#Text_'+$qid).val();
	var $txt = $("<input type='text' value='"+str_text+"' size='20' style='width:500px' name='theText[]' />");
	var $hide_num = $("<input type='text' value='"+$qid+"' size='20' style='width:500px;display:none;' name='numText[]' />");
	var $btn = $("<input type='button' value='删除'/>");
	var $br = $("<br/>");
	$btn.click(function(){
		$txt.remove();
		$hide_num.remove();
		$btn.remove();
		$br.remove();
	});
	$("#exam_paper").append($txt).append($hide_num).append($btn).append($br);
	$('#hiddensum').val = 1;
}
</script>

