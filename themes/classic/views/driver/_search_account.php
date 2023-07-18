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
	<div class="span3">
		<label>交易类型：</label>
		<?php 
			$citys = Dict::items('account_type');
			$citys[0] = '--请选择交易类型--';
			
			echo CHtml::dropDownList('Account[type]','type',$citys,array('class'=>'span10'));
		?>
	</div>	
    <div class="span2">
		<?php echo $form->label($model,'user'); ?>
		<?php echo CHtml::textField('Account[user]',$model->user,array('placeholder'=>'工号','class'=>'span8')); ?>
    </div>
    <div class="span2">
		<?php echo $form->label($model,'order_id'); ?>
		<?php echo CHtml::textField('Account[order_id]',$model->order_id,array('placeholder'=>'订单号','class'=>'span8')); ?>
    </div>    
    <div class="span3">
		<?php echo $form->label($model,'created'); ?>
		<?php

			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'Account[created]', 
				'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
				'htmlOptions'=>	array('class'=>"span12")
			));

		?>
		
    </div>
        
    <div class="span12">
	    <?php echo CHtml::submitButton('搜索',array('class'=>"btn btn-success")); ?>
    </div>
</div>


<?php $this->endWidget(); ?>
</div><!-- search-form -->
