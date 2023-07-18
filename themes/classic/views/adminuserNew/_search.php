<div class="wide form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="row span3">
        <?php
        $html_options = [
            'onmousedown'=>"javascript:return false;",
            'onclick' => "modalCity.showModal();",
            'id' => "AdminUserNew_city_id"
        ];
        $cityList = Dict::items('city');
        $edj_city_select  = new EdjCitySelect('A', false, '', '城市', 'AdminUserNew[city_id]', $model->city_id, $cityList, $html_options);
        $this->renderPartial('/helper/_edj_city_select', array(
            'edj_city_select' => $edj_city_select,
            'style' => ['class'=>'']
        ));
        echo (isset($_GET['dep_id']) && !empty($_GET['dep_id'])) ? CHtml::hiddenField('dep_id',$_GET['dep_id']) : '';?>
    </div>

    <?php if (Yii::app()->user->admin_level == AdminUserNew::LEVEL_ADMIN && !$dep_info) { ?>
        <div class="row span2" style="width:100px;">
            <?php echo $form->label($model, 'department_id');
            echo $form->dropDownList($model, 'department_id', AdminDepartment::model()->getAll(1),array('style'=>'width:100px'));
            ?>
        </div>
        <div class="row span2" style="width:100px;<?php if(!isset($_GET['AdminUserNew']['group_id'])) echo 'display:none"';?> id="group_box">
            <?php echo $form->label($model, 'group_id'); ?>
            <span id="group_pool"><?php if(isset($_GET['AdminUserNew']['group_id'])){ echo $form->dropDownList($model, 'group_id', AdminDepartment::model()->getAll(1,$_GET['AdminUserNew']['department_id']),array('style'=>'width:100px')); } ?></span>
        </div>
    <?php }else if(Yii::app()->user->admin_level == AdminUserNew::LEVEL_DEPARTMENT_ADMIN || ( $dep_info && !$group_info)) {?>
    <div class="row span2" style="width:100px;">
        <?php echo $form->label($model, 'group_id');
        $dep_id_tmp = isset($_GET['parent_id']) ? $_GET['parent_id'] : $dep_id;
        //echo $dep_id_tmp;
        echo $form->dropDownList($model, 'group_id', AdminDepartment::model()->getAll(1,$dep_id_tmp),array('style'=>'width:100px'));?>
    </div>
    <?php }
    if(isset($_GET['parent_id'])) echo CHtml::hiddenField('parent_id',$_GET['parent_id']);?>

    <div class="row span2" style="width:100px;">
        <?php echo $form->label($model, 'level');
        if(Yii::app()->user->admin_level == AdminUserNew::LEVEL_GROUP_ADMIN) $noshowDepadminLevel = true; else $noshowDepadminLevel = false; echo $form->dropDownList($model, 'level', AdminUserNew::getUserLevel('',1,$noshowDepadminLevel),array('style'=>'width:100px')); ?>
    </div>

    <div class="row span2" style="width:100px;">
        <?php echo $form->label($model, 'status');
        echo $form->dropDownList($model, 'status', AdminUserNew::getUserStatus('',1),array('style'=>'width:80px')); ?>
    </div>

    <div class="row span2" style="width:150px;">
        <?php echo $form->label($model, 'name');
        echo $form->textField($model, 'name', array('size' => 10, 'maxlength' => 10,'style'=>'width:100px')); ?>
    </div>

    <?php
        if(isset($_GET['dep_id']) && $_GET['dep_id']) {
            echo '<div class="row span2" style="width:170px;">';
            echo '<label for="AdminUserNew_role">角色组</label>';
            $role_value = isset($_GET['AdminUserNew']['role_id']) ? $_GET['AdminUserNew']['role_id'] : '';
            echo CHtml::dropDownList('AdminUserNew[role_id]', $role_value, AdminRole::model()->getRolesByDepid($_GET['dep_id'],1), array('style'=>'width:150px','id'=>'AdminUserNew_role')); //$model, 'name', array('size' => 10, 'maxlength' => 10,'style'=>'width:100px'));
            echo '</div>';
        }
    ?>

    <div class="row">
        <?php echo $form->label($model, '&nbsp');
        echo CHtml::submitButton('Search', array('class' => 'btn'));
        if (AdminActions::model()->havepermission('adminuserNew', 'create')) echo CHtml::link('创建用户', Yii::app()->createUrl('/adminuserNew/create',array('dep_id'=>$dep_id,'back_url'=>Yii::app()->request->getUrl())), array('class' => 'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->