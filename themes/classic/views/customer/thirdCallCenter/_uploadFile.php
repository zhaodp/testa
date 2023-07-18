<?php echo '上传第三方通话中心的 excel 文件' ?>
<div class="well span12">
	<?php
	$form = $this->beginWidget('CActiveForm',
		array(
			'action' => Yii::app()->createUrl('/customer/thirdCallCenter'),
			'method' => 'POST',
			'htmlOptions' => array('enctype' => 'multipart/form-data'),
		));
	?>
	<div class="span12">
		<?php echo CHtml::label('选择来源', 'source'); ?>
		<?php
		echo $form->dropDownList($model, 'source', $sourceList, array('style' => 'margin-bottom:0'));
		?>
		<?php
		echo '选择文件:';
		echo CHtml::activeFileField($model, 'file');
		?>
		<?php
		echo CHtml::submitButton('上传分析', array('class' => 'btn btn-success'));
		?>
	</div>
	<?php $this->endWidget(); ?>
</div>