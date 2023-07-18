<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bonus-type-form',
	'enableAjaxValidation'=>false,
)); ?>
    <div class="row">
	    <div class="span8">
		<h4><?php echo isset(MessageText::$messageDesc[$model->code]['var_num']) ? MessageText::$messageDesc[$model->code]['var_num'] : 0; ?> 个参数</h4>	
		</div>
	</div>
<?php
	 if (isset(MessageText::$messageDesc[$model->code]['var_num']) && MessageText::$messageDesc[$model->code]['var_num'] > 0)
	 {
	 	for ($i = 1; $i <= MessageText::$messageDesc[$model->code]['var_num']; $i ++)
	 	{
?>	
    <div class="row">
	    <div class="span8">
	    <?php echo "\\" . $i . ':' . MessageText::$messageDesc[$model->code]['vars'][$i-1]; ?>	    
		</div>
	</div>	    
<?php
	 	}
	 } 
?>
    <div class="row">
	    <div class="span12">
			<?php echo $form->textArea($model,'name', array('class'=>'span8','style' => 'height:100px;')); ?>			
		</div>
	
	</div>

    <div class="row">
	    <div class="span4">		
		<?php echo CHtml::submitButton('修改消息内容',array('class'=>'span4 btn-large btn-success btn-block')); ?>
		</div>
	    <div class="span4">		
		<?php echo CHtml::link('取消', array('message/admin'), array('class'=>'span3 btn-large btn-cancel btn-block')); ?>
		</div>		
	</div>		

<?php $this->endWidget(); ?>

</div><!-- form -->