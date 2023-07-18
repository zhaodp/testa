<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions'=>array('class'=>'form')
)); ?>

<div class="row-fluid">
	<div class="span3">
		<label>交易类型：</label>
		<?php 
			$accountType = Dict::items('account_type');
			$accountType[0] = '--请选择交易类型--';
			
			echo CHtml::dropDownList('type','type',$accountType,array('class'=>'span10'));
		?>
	</div>	
        
    <div class="span12">
	    <?php echo CHtml::submitButton('搜索',array('class'=>"btn btn-success")); ?>
    </div>
</div>


<?php $this->endWidget(); ?>
</div><!-- search-form -->
