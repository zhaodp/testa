<div class="row">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'admin-user-form',
     'enableAjaxValidation'=>false,
     'enableClientValidation'=>true,
)); ?>


	<?php echo $form->errorSummary($model); ?>

    <div class="row span2" style="width:100px;">
		<?php echo $form->labelEx($model,'name',array('style'=>'width:80px;')); ?>
		<?php echo $form->textField($model,'name',array('maxlength'=>20,'style'=>'width:60px;')); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row span2" style="width:170px;margin-left:15px;">
		<?php echo $form->labelEx($model,'phone',array('style'=>'width:90px;')); ?>
		<?php echo $form->textField($model,'phone',array('maxlength'=>11,'style'=>'width:90px;')); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row span3">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
    
    <div class="row span3">
        <?php echo $form->labelEx($model,'类型'); ?>
        <select name="AdminUser[type]" id="AdminUser_type">
			<option value="1" <?php if( 1==$model->type ) { echo "selected=selected";} ?>>正式员工</option>
			<option value="0" <?php if( 0==$model->type ) { echo "selected=selected";} ?>>兼职员工</option>
		</select>
        <?php echo $form->error($model,'status'); ?>
    </div>
	
	<div class="row span3">
		<?php 
		echo $form->labelEx($model,'city');
		//City=0显示城市列表
		if(Yii::app()->user->city ==0){
			echo $form->dropDownList($model,'city', Dict::items('city'), array('style'=>'width:130px;'));
		}else{
			$model->city = Yii::app()->user->city;
			echo CHtml::textField('',Dict::item('city',Yii::app()->user->city), array('disabled'=>true,'style'=>'width:130px;'));
			echo $form->hiddenField($model,'city');
		}
		?>
		<?php echo $form->error($model,'city'); ?>
		<?php echo $form->CheckBox($model,'permissions'); ?>允许登录
	</div>	
	
    <div class="row span3">
        <?php echo $form->labelEx($model,'department'); ?>
        <?php echo $form->dropDownList($model, 'department', Dict::items('department')); ?>
        <?php echo $form->error($model,'department'); ?>
    </div>

    <div class="row span2">
        <?php echo $form->labelEx($model,'级别'); ?>
        <select class="span5" name="AdminUser[admin_level]" id="AdminUser_admin_level">
			<option value="0" <?php if( 0==$model->admin_level ) { echo "selected=selected";} ?>>普通用户</option>
			<option value="1" <?php if( 1==$model->admin_level ) { echo "selected=selected";} ?>>组管理员</option>
			<option value="2" <?php if( 2==$model->admin_level ) { echo "selected=selected";} ?>>超级管理员</option>
		</select>	
        <?php echo $form->error($model,'admin_level'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'状态'); ?>
        <select class="span3" name="AdminUser[status]" id="AdminUser_status">
			<option value="0" <?php if( 0==$model->status ) { echo "selected=selected";} ?>>禁用</option>
			<option value="1" <?php if( 1==$model->status ) { echo "selected=selected";} ?>>正常</option>
		</select>
        <?php echo $form->error($model,'status'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'access_begin'); ?>  
        <?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'access_begin', 
				//'model'=>$form,  //Model object
				'value'=>$model->access_begin,  
				'mode'=>'time',  //use "time","date" or "datetime" (default)
				'language'=>'zh'
			));
		?>
        <?php echo $form->error($model,'access_begin'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'access_end'); ?>
         <?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'access_end', 
				//'model'=>$form,  //Model object
				'value'=>$model->access_end, 
				'mode'=>'time',  //use "time","date" or "datetime" (default)
				'language'=>'zh'
			));
		?>
        <?php echo $form->error($model,'access_end'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'expiration_time'); ?>
         <?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'expiration_time', 
				//'model'=>$form,  //Model object
				'value'=>$model->expiration_time, 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
		?>
        <?php echo $form->error($model,'expiration_time'); ?>
    </div>
	
	

	<div class="row span10">
		<h4>权限设置：</h4>
		<?php 
		
			//得到当前登录用户信息
			$currentUserInfo =  Yii::app()->user->getCurrentUserInfo();
			
			//判断如果是组管理员，则只能赋予本人角色给其他人，普通用户不能给其他人分配权限，超级管理员可以分配所有权限。
			switch ( $currentUserInfo ['admin_level'] ){	
				//case 0:
				case 1://组管理员,取组管理员的角色
					$can_grant_priv = $currentUserInfo['roles'];
					$all_priv = false ;
					break;
				case 2:
					$can_grant_priv = array();
					$all_priv = true ;
					break;
				default :
					$can_grant_priv = array_keys( $model->getRoles() );
					$all_priv = false ;
					break;
			}
			
			$group = AdminGroup::model()->getGroups();
			foreach($group as $item) {
				if (isset($item['children'])) {
					echo '<legend>'.CHtml::label($item['name'], null, array (
						'style'=>'display:inline')).'</legend>';
					foreach($item['children'] as $child) {
						if( in_array($child['id'],$can_grant_priv ) || $all_priv ){
							echo '<label class="checkbox inline">';
							echo CHtml::checkBox('AdminUser[roles][]', array_key_exists($child['id'],$model->getRoles()), array (
								'id'=>'AdminUser_roles_' . $child['id'], 
								'value'=>$child['id'], 
								'name'=>$child['name'], 
								'separator'=>''));
							echo $child['name'].'</label>';
						}
					}
				}
			}
			
		?>
		
	</div>
	<label></label>
	<div class="row buttons span3">
		<?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存',array('class'=>'span2 btn-success','disabled'=>in_array('super_admins', $model->getRoles()))); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->