<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'action_list_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array (
        'title'=>'查看角色组权限',
        'autoOpen'=>false,
        'width'=>'700',
        'height'=>'550',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#action_list_dialog").dialog("close");}'))));
echo '<div id="action_list_dialog"></div>';
echo '<iframe id="actionlist_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

?>
<div class="row">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'admin-user-new-form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>true,
)); ?>


<?php echo $form->errorSummary($model); ?>

<div class="row span2" style="width:100px;">
    <?php echo $form->labelEx($model,'name',array('style'=>'width:80px;')); ?>
    <?php echo $form->textField($model,'name',array('maxlength'=>20,'style'=>'width:60px;')); ?>
    <?php echo $form->error($model,'name');
    echo CHtml::hiddenField('back_url',isset($_GET['back_url']) ? $_GET['back_url'] : '')?>
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
    <?php echo $form->labelEx($model,'type'); ?>
    <?php echo $form->dropDownList($model, 'type', AdminUserNew::getUserType());?>
    <?php echo $form->error($model,'type'); ?>
</div>

<div class="row span3">
    <?php
    echo $form->labelEx($model,'city_id');
    //City=0显示城市列表
    //if(Yii::app()->user->city ==0){
    echo $form->dropDownList($model,'city_id', Dict::items('city'), array('style'=>'width:130px;'));
    //}else{
    //    $model->city_id = Yii::app()->user->city;
    //    echo CHtml::textField('',Dict::item('city',Yii::app()->user->city), array('disabled'=>true,'style'=>'width:130px;'));
    //    echo $form->hiddenField($model,'city_id');
    //}
    ?>
    <?php echo $form->error($model,'city_id'); ?>

</div>

<div class="row span3">
    <?php echo $form->labelEx($model,'department_id'); ?>
    <?php if($admin_info['admin_level'] == AdminUserNew::LEVEL_ADMIN){
        echo $form->dropDownList($model, 'department_id',AdminDepartment::model()->getAll(1));
    } else {
        $depart_info = AdminDepartment::model()->getInfoByid($admin_info['department']);
        echo CHtml::textField('',$depart_info['name'], array('disabled'=>true,'style'=>'width:130px;'));
        echo $form->hiddenField($model,'department_id',array('value'=>$admin_info['department']));
    }
    ?>
    <?php echo $form->error($model,'department_id'); ?>
</div>
<div class="row span3">
    <?php if($admin_info['admin_level'] == AdminUserNew::LEVEL_ADMIN || $admin_info['admin_level'] == AdminUserNew::LEVEL_DEPARTMENT_ADMIN){
        echo $form->labelEx($model,'group_id');
        //print_r($this->getGroupByDep($model->department_id));
        echo '<span id="group_pool">';
        $dep_id_group = $model->isNewRecord ? Yii::app()->user->department : $model->department_id;
        echo $form->dropDownList($model, 'group_id', $this->GetGroupByDep($dep_id_group,1));
        echo '</span>';
        echo $form->error($model,'group_id');
    }
    else {
        $admin_db_info = AdminUserNew::model()->findByPk($admin_info['user_id']);
        if($admin_db_info->group_id){
            $group_info = AdminDepartment::model()->getInfoByid($admin_db_info->group_id);
            echo $form->labelEx($model,'group_id');
            echo CHtml::textField('',$group_info['name'], array('disabled'=>true,'style'=>'width:130px;'));
            echo $form->hiddenField($model,'group_id',array('value'=>$admin_db_info->group_id));
        }

    }
    ?>
</div>

<div class="row span3">
    <?php echo $form->labelEx($model,'level'); ?>
    <?php if($admin_info['admin_level'] == AdminUserNew::LEVEL_ADMIN){
        echo $form->dropDownList($model, 'level', AdminUserNew::getUserLevel());
    }elseif($admin_info['admin_level'] == AdminUserNew::LEVEL_DEPARTMENT_ADMIN && ! in_array($model->level,array(AdminUserNew::LEVEL_DEPARTMENT_ADMIN,AdminUserNew::LEVEL_ADMIN))){
        echo $form->dropDownList($model, 'level', AdminUserNew::getUserLevel('','',1));
    }else {
        echo CHtml::textField('',AdminUserNew::getUserLevel($model->level), array('disabled'=>true,'style'=>'width:130px;'));

        echo $form->hiddenField($model,'level',array('value'=>$model->level ? $model->level : AdminUserNew::LEVEL_NORMAL));
    } ?>
    <?php echo $form->error($model,'level'); ?>
</div>

<div class="row span3">
    <?php echo $form->labelEx($model,'status'); ?>
    <?php echo $form->dropDownList($model, 'status',AdminUserNew::getUserStatus()); ?>
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
    <h4>角色组分配：</h4>
    <div id="role_pool">
        <?php
        //var_dump($role_info);die;
        if(!($admin_info['admin_level'] == AdminUserNew::LEVEL_ADMIN &&  $model->isNewRecord) ){
            $this->widget('zii.widgets.grid.CGridView',
                array (
                    'id'=>'admin-usernew-role-grid',
                    'itemsCssClass'=>'table table-striped',
                    'dataProvider'=>$role_info->search(),
                    'selectableRows'=>2,
                    'columns'=>array (
                        array(
                            'class' => 'CCheckBoxColumn',
                            'checkBoxHtmlOptions' => array(
                                'name' => 'role_id[]',
                                'value'=> '$data->id',
                            ),
                            'checked'=>function ($data) use ($my_role_info) {
                                    return in_array($data->id, $my_role_info);
                                },
                        ),
                        'id',
                        'name',
                        'desc',
                        'create_time',
                        array(
                            'name'=>'查看功能',
                            'type'=>'raw',
                            'value' => 'CHtml::link("查看功能", "javascript:void(0);", array (
						    "onclick"=>"{showRoles($data->id);}"));'
                        ),

                    ),
                )
            );
        }

        ?>
    </div>

</div>
<label></label>
<div class="row buttons span3">
    <br>
    <?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存',array('class'=>'span2 btn-success')); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
    $(document).ready(function(){
        $('#AdminUserNew_department_id').change(function(){
            //alert($('#AdminUserNew_department_id').val());
            var dep_id = $('#AdminUserNew_department_id').val();
            //alert(dep_id == '');
            if(dep_id != '' ){
                $.get('<?php echo Yii::app()->createUrl('adminuserNew/GetRoleByDep',array('user_id'=>isset($_GET['id']) ? $_GET['id'] : ''));?>&dep_id='+dep_id,function(result){
                    $('#role_pool').html(result);
                });
                <?php if($admin_info['admin_level'] == AdminUserNew::LEVEL_ADMIN){ ?>
                //var group_id = $('#AdminUserNew_group_id').val();
                //alert(dep_id);
                $.get('<?php echo Yii::app()->createUrl('adminuserNew/GetGroupHtml');?>&dep_id='+dep_id,function(result){
                    //alert(result);
                    $('#group_pool').html(result);
                });
                <?php }?>
            }
        });

        $('#AdminUserNew_group_id').change(function(){
            //alert($('#AdminUserNew_department_id').val());
            var dep_id = $('#AdminUserNew_group_id').val();
            //alert(dep_id);
            if(dep_id == ''){
                dep_id = $('#AdminUserNew_department_id').val();
            }
            $.get('<?php echo Yii::app()->createUrl('adminuserNew/GetRoleByDep',array('user_id'=>isset($_GET['id']) ? $_GET['id'] : ''));?>&dep_id='+dep_id,function(result){
                $('#role_pool').html(result);
            });


        });

    });
    function changeGroup(){
        var dep_id = $('#AdminUserNew_group_id').val();
        //alert(dep_id);
        if(dep_id == ''){
            dep_id = $('#AdminUserNew_department_id').val();
        }
        if(dep_id != ''){
            $.get('<?php echo Yii::app()->createUrl('adminuserNew/GetRoleByDep',array('user_id'=>isset($_GET['id']) ? $_GET['id'] : ''));?>&dep_id='+dep_id,function(result){
                $('#role_pool').html(result);
            });
        }
    }
    function showRoles(id){

        var url = '<?php echo Yii::app()->createUrl('/adminuserNew/getActionByRoleid');?>&id='+id;
        $('#actionlist_frame').attr('src',url);
        $('.ui-dialog-title').html('查看角色组权限');
        $('#action_list_dialog').dialog('open');return false;
    }
</script>