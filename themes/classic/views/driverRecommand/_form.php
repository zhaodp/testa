<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */
/* @var $form CActiveForm */
?>

<div class="form span11">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-recommand-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
		
 	<div class="row-fluid row" style='margin-bottom:10px;'>
                    <!-- <?php echo CHtml::label('奖励类型','');?>  -->

       <!--  <?php echo CHtml::radioButtonList('type',1, array('1'=>'皇冠','2'=>'e币'),
            array(
                'template' => '&nbsp;&nbsp;&nbsp;{input} {label}',
                'separator' => '&nbsp;&nbsp;',
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;')));?> -->
    </div>
	<div class="row">
		<?php echo $form->labelEx($model,'driver_id'); ?>
		<?php echo $form->textField($model,'driver_id',array('size'=>10,'maxlength'=>10)); ?>
		<span id='tipword'></span>
		<!-- <?php echo $form->error($model,'driver_id'); ?> -->
	</div>
	<div id='wealth' class="row">
		<?php echo $form->labelEx($model,'wealth'); ?>
		<?php echo $form->textField($model,'wealth'); ?><span>(-5000~5000之间)</span>
		<?php echo $form->error($model,'wealth'); ?>
	</div>
	<div id='date'>
	<div class="row">
		<?php echo $form->labelEx($model,'begin_time'); ?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'DriverRecommand[begin_time]', 
				'model'=>$model,  //Model object
				'value'=>$model->begin_time, 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
				'htmlOptions'=>	array('size'=>60,'maxlength'=>100)
			));
		?>
		<?php echo $form->error($model,'begin_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end_time'); ?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'DriverRecommand[end_time]', 
				'model'=>$model,  //Model object
				'value'=>$model->end_time, 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
				'htmlOptions'=>	array('size'=>60,'maxlength'=>100)
			));
		?>
		<?php echo $form->error($model,'end_time'); ?>
	</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'reason'); ?>
		<?php echo $form->textField($model,'reason',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'reason'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '保存' : '修改',array('name' => 'save','class'=>'btn')); ?>
		<?php 
		if($model->isNewRecord){
			echo CHtml::submitButton('保存并继续添加',array('name' => 'save_add','class'=>'btn'));
			echo "&nbsp;";
			echo CHtml::link('返回',Yii::app()->createUrl('driverRecommand/admin'),array('class'=>'btn'));
		}?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(document).ready(function () {
    $('#DriverRecommand_wealth').attr('value','');	
    var text = '该城市剩余';
   	$('#tipword').html(text+'皇冠');
    $('#wealth').hide();	
	$("input[name='type']").change(function () {
	            var type = $("input[name='type']:checked").val();
	            if(type==1){
	            	$('#date').show();
	            	$('#wealth').hide();
	            	$('#tipword').html(text+'皇冠');
	            }else if(type == 2){
					$('#date').hide();
	            	$('#wealth').show();
	            	$('#tipword').html(text+'e币');
	            }
	        });

		//司机工号异步请求
         $('#DriverRecommand_driver_id').blur(function(){
            $type=$("input[name='type']:checked").val();
            //暂时只有皇冠
            $type =1;
            var oldText = $('#tipword').html();
            	var post_data = {};
            	post_data['type'] = $type;
            	post_data['driver_id'] = $('#DriverRecommand_driver_id').val();
               	//根据工号，异步获取司机所在城市剩余e币数量
               	if (post_data['driver_id'] != '') {
               			jQuery.post(
						'<?php echo Yii::app()->createUrl('/DriverRecommand/AjaxCompute');?>',
						post_data,
						function(d) {
							// alert(d.message);
							if (d.status) {
								if(d.type ==1){
								$('#tipword').html(text+'皇冠'+d.message); //替换文案，展示剩余量
								}else if(d.type == 2){	
									$('#tipword').html(text+'e币'+d.message); //替换文案，展示剩余量
								}
							}
						},
						'json'
					);
               	};
                
         });
   
   });
</script>