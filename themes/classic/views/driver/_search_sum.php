<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions'=>array('class'=>'form')
)); ?>

<div class="row-fluid">
	<div class="span2">
		<label>城市：</label>
		<?php 
			$citys = Dict::items('city');
			$citys[0] = '--请选择城市--';
			
			echo CHtml::dropDownList('Account[city_id]','city_id',$citys,array('class'=>'span11'));
		?>
	</div>
    <div class="span2">
		<?php echo $form->label($model,'user'); ?>
		<?php echo CHtml::textField('Account[user]',$model->user,array('placeholder'=>'工号','class'=>'span11')); ?>
    </div>

    <div class="span2">
    	<label>余额小于：</label>
		<?php echo CHtml::textField('Account[total_max]',$model->total_max,array('placeholder'=>'余额','class'=>'span11')); ?>
    </div>
    
    <div class="span2">
    	<label>&nbsp;</label>
		<?php echo CHtml::checkBox('Account[mark]',false); ?>只显示已屏蔽司机
    </div>

</div>
<div class="row-fluid">
    <div class="span12">
	    <?php echo CHtml::submitButton('搜索',array('class'=>"btn btn-success span1")); ?>
    </div>
</div>

<?php $this->endWidget(); ?>
</div><!-- search-form -->