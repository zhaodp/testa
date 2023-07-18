<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'agent-allot-update-form',
    'enableAjaxValidation'=>false,
)); ?>

    <?php echo $form->errorSummary($model); ?>

	<?php echo $form->labelEx($model,'agent_num'); ?>
	<?php echo $form->textField($model,'agent_num',array('readonly'=>'readonly')); ?>

	<?php echo $form->labelEx($model,'phone'); ?>
	<?php 
		if($model->is_lock==1){
			echo $form->textField($model,'phone',array('disabled'=>'disabled'));
		}else{
			echo $form->textField($model,'phone');
		}
	?>
	
	<?php echo $form->labelEx($model,'user_id'); ?>
	<?php
	   $dep_list = AdminDepartment::model()->getAll();
           $dep_show = array();
           foreach($dep_list as $i) {
               $dep_show[$i['id']] = $i['name'];
           }
           $dep_show['0'] = '全部';
           echo CHtml::activeDropDownList($model, 'department', $dep_show,
               array(
                   'options' => array('4'=>array('selected'=>'selected')),
                   'class' => 'user_list',
                   'ajax' =>array(
                       'type' => 'GET',
                       'url'  => CController::createUrl('adminuser/getuserlist'),
                       'update'=>'#AdminAgent_user_id',
                       'data' => array('mid'=>"js:this.value")
                   ),
               )
           );
	   //默认取客服(4)人员
           echo CHtml::activeDropDownList($model, 'user_id', AdminUserNew::model()->getAgentUsers(4),
               array(
                   'class' => 'user_list',
               )
           );
	?>
	
    <div class="buttons">
		<?php echo CHtml::submitButton('保存',array('class'=>'btn btn-success')); ?>
    </div>

<?php $this->endWidget(); ?>

</div>
