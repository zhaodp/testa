<?php
/* @var $this QuestionnaireController */
/* @var $model Questionnaire */
/* @var $form CActiveForm */
?>
<style>
div.form label.labelForRadio {display:inline-block;width:auto;float:none;}
</style>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'questionnaire-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
		<?php echo $form->hiddenField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->hiddenField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
	<div class="row">
		<label><b>1.您好，打扰您一下，我是e代驾的客服经理，您在今年X月X日，使用过一次e代驾提供的代驾服务，我想给您做一次服务的回访，大概耽误您2分钟的时间，作为回报，我们会送您一张39元的e代驾代金券，您看现在方便吗？</b></label>
		<label style = "color:blue;">【A.（进入第3题）B.（进入第2题）
C.是这样，我这里有使用您的手机号预订e代驾服务的记录，那可能是您的朋友用您的手机预订的吧。没关系，我们还是会赠送您代金券，您使用一次看看？您使用过我们的服务之后，我能给您打个电话问问您对我们服务的意见吗？】</label>
		<?php echo $form->radioButtonList($model, 'status', array('1'=>'<b>A.</b>方便','3'=>'<b>B.</b>不方便','2'=>'<b>C.</b>不记得使用过'),array('class'=>'status','separator'=>'&nbsp','labelOptions'=>array('class'=>'labelForRadio')));?>
	</div>
	<div id="hf" style = "display:none;">
		<div class="row">
			<label><b>2.预约呼叫</b></label>
			<input class="status_date" value="0" checked="checked" type="radio" name="CustomerVisit[date]">
			<label class="labelForRadio"><b>A</b>可预约</label>&nbsp;
			<input class="status_date" value="1" type="radio" name="CustomerVisit[date]"> 
			<label class="labelForRadio"><b>B</b>拒绝预约</label>
			<label id="date_div">
				<?php
				Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
				$this->widget('CJuiDateTimePicker', array (
					'name'=>'CustomerVisit[visit_time]', 
					//'model'=>$model,  //Model object
					'value'=>'', 
					'mode'=>'datetime',  //use "time","date" or "datetime" (default)
					'options'=>array (
						'dateFormat'=>'yy-mm-dd'
					),  // jquery plugin options
					'language'=>'zh'
				));
				?>
			</label>
		</div>
		<label style = "color:blue;"><b>结束语：</b>耽误您的时间了，感谢您对我的工作的配合。祝您工作生活愉快。再见！</label>
		<label style = "color:blue;"><b>如果是问题1-C过来的顾客，再加一句：</b>稍后我将优惠券以短信形式发到您的手机上，您注意查收。</label>
	</div>
	<div id = "list_hf">
	<?php
	$item_list = array('A','B','C','D','E','F','G','H','I','J','K','L');
	foreach ($customer_list as $key => $list){
		$li_key = $list['id'];
		switch ($list['type']){
			case 0:
	?>
		<div class="row" id = "item<?php echo $li_key;?>"
			<?php if($li_key == 4||$li_key == 5){echo "style='display:none'";}?>>
			<label><b><?php echo ($key+3).$list['title']?></b></label>
			<?php foreach (json_decode($list['contents']) as $k=>$list_item){
				echo "<input id='Item".$li_key."_".$k."' value='$k' type='radio' name='item$li_key' class='item$li_key'>";
				echo "<label class='labelForRadio'><b>$item_list[$k].</b>$list_item</label>&nbsp;&nbsp;&nbsp;";
			}
			if($list["ext"] == 1)
				echo "<div id='item".$li_key."txt' style='display:none;'><input type='text' name='item".$li_key."txt'></div>";
			?>
			<?php if($li_key == 4){
				echo "<label style='color:blue;'>
				<b>结束语（选A、B、C、E）：</b>好的，您的意见我已经记录下来了。那稍后我通过短信形式给您的手机号发送一张e代驾的代金券。您可以在手机上安装我们的客户端，以后有需要时就可以直接呼叫我们的司机了。<br/>
				感谢您对我的工作的配合。祝您工作生活愉快。再见！<br/>
				<b>D.（进入问题7）</b>
				</label>";
			}?>
			<?php if($li_key == 5){
				echo "<label style='color:blue;'>
				<b>结束语：</b>好的。您反映的问题我已经全部记录下来了。衷心感谢您对e代驾提出的宝贵意见和建议。<br/>
				那稍后我通过短信形式给您的手机号发送一张e代驾的代金券。您可以在手机上安装我们的客户端，以后有需要时就可以直接呼叫我们的司机了。<br/>
				感谢您对我的工作的配合。祝您工作生活愉快。再见！
				</label>";
			}?>
		</div>
	<?php
				break;
			default:
				echo '无';
		}
	}
	?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->