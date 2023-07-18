<?php
/* @var $this CustomerQuestionController */
/* @var $model CustomerQuestion */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customer-question-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type', array('0'=>'单选','1'=>'复选','3'=>'文本','4'=>'下拉列表'));?>
		<?php echo $form->error($model,'type'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textArea($model, 'title');?>
		<?php echo $form->error($model,'title'); ?>
	</div>
	
	<div class="row">
		<label for="CustomerQuestion_contents">选项列表 <a href="javascript:;" onclick="add_list();">添加选项</a></label>
		<div id="item_list">
		<?php
			$item = array('A','B','C','D','E','F','G','H','I','J');
			$list_contents = json_decode($model['contents']);
			$count = count($list_contents);
			if($count>0){
				foreach ($list_contents as $k=>$content){
		?>
			<div id="list_<?php echo $k;?>" class="customer_list">
				<label for="CustomerQuestion_contents"><?php echo $item[$k];?>选项</label>
				<input size="80" maxlength="200" name="item[]"  type="text" value="<?php echo $content;?>">
				<?php if($k>0){?>[<a href='javascript:;' onclick='remove_list(<?php echo $k;?>)'> 去除 </a>]<?php }?>
			</div>
		<?php }}else{?>
			<div id="list_0" class="customer_list">
				<label for="CustomerQuestion_contents">A选项</label>		
				<input size="80" maxlength="200" name="item[]"  type="text" value="">
			</div>
		<?php }?>
		</div>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
function add_list(){
	var Param = new Array('A','B','C','D','E','F','G','H','I');
	var list_len = $(".customer_list").length;
	var Serial = Param[list_len];
	var str = "<div id='list_"+list_len+"' class='customer_list'>";
	str += "<label>"+Serial+"选项</label>";		
	str += "<input size='80' maxlength='200' name='item[]'  type='text' value=''>[<a href='javascript:;' onclick='remove_list("+list_len+")'> 去除 </a>]";
	str += "</div>";
	$("#item_list").append(str);
}
function remove_list(id)
{
	$("#list_"+id).remove();
}
</script>


