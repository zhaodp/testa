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
        <?php echo $form->labelEx($model,'name',array('style'=>'width:80px;'));
        echo $form->textField($model,'name',array('maxlength'=>20,'style'=>'width:60px;'));
        echo $form->error($model,'name');
        echo CHtml::hiddenField('back_url',isset($_GET['back_url']) ? $_GET['back_url'] : '')?>
    </div>

    <div class="row span2" style="width:170px;margin-left:15px;">
        <?php echo $form->labelEx($model,'phone',array('style'=>'width:90px;'));
        echo $form->textField($model,'phone',array('maxlength'=>11,'style'=>'width:90px;'));
        echo $form->error($model,'phone'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'email');
        echo $form->textField($model,'email');
        echo $form->error($model,'email'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'type');
        echo $form->dropDownList($model, 'type', AdminUserNew::getUserType());
        echo $form->error($model,'type'); ?>
    </div>

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
        ?>

        <?php echo $form->error($model,'city_id'); ?>

    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'department_id');
        if($admin_info['admin_level'] == AdminUserNew::LEVEL_ADMIN){
         echo $form->dropDownList($model, 'department_id',AdminDepartment::model()->getAll(1));
        } else {
            $depart_info = AdminDepartment::model()->getInfoByid($admin_info['department']);
            echo CHtml::textField('',$depart_info['name'], array('disabled'=>true,'style'=>'width:130px;'));
            echo $form->hiddenField($model,'department_id',array('value'=>$admin_info['department']));
        }
         echo $form->error($model,'department_id'); ?>
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
        <?php echo $form->labelEx($model,'level');
        if($admin_info['admin_level'] == AdminUserNew::LEVEL_ADMIN){
            echo $form->dropDownList($model, 'level', AdminUserNew::getUserLevel());
        }elseif($admin_info['admin_level'] == AdminUserNew::LEVEL_DEPARTMENT_ADMIN && ! in_array($model->level,array(AdminUserNew::LEVEL_DEPARTMENT_ADMIN,AdminUserNew::LEVEL_ADMIN))){
            echo $form->dropDownList($model, 'level', AdminUserNew::getUserLevel('','',1));
        }else {
            echo CHtml::textField('',AdminUserNew::getUserLevel($model->level), array('disabled'=>true,'style'=>'width:130px;'));

            echo $form->hiddenField($model,'level',array('value'=>$model->level ? $model->level : AdminUserNew::LEVEL_NORMAL));
        }
        echo $form->error($model,'level'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'status');
        echo $form->dropDownList($model, 'status',AdminUserNew::getUserStatus());
        echo $form->error($model,'status'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'access_begin');
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array (
            'name'=>'access_begin',
            //'model'=>$form,  //Model object
            'value'=>$model->access_begin,
            'mode'=>'time',  //use "time","date" or "datetime" (default)
            'language'=>'zh'
        ));
        echo $form->error($model,'access_begin'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'access_end');
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array (
            'name'=>'access_end',
            //'model'=>$form,  //Model object
            'value'=>$model->access_end,
            'mode'=>'time',  //use "time","date" or "datetime" (default)
            'language'=>'zh'
        ));
        echo $form->error($model,'access_end'); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'expiration_time');
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
        echo $form->error($model,'expiration_time'); ?>
    </div>


    <div class="row span3">
        <?php echo $form->labelEx($model,'auth_type');
        echo $form->dropDownList($model, 'auth_type',AdminUserNew::getAuthType());
        echo $form->error($model,'auth_type');?>
    </div>


    <div class="row span3">
        <?php echo $form->labelEx($model,'organization_id');
        echo $form->dropDownList($model, 'organization_id', $organization);
        echo $form->error($model,'organization_id');?>
    </div>



<div class="row span9">
<label for="city_list" class="required">区域选择</label>
<div class="city-selector-wrapper">
            <input name="AdminUserNew[city_list]" id="city_list" type="hidden" style="width:600px;height:26px;" />
        </div>
    <div class="row buttons span3">
        <br>
        <?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存',array('class'=>'span3 btn-success')); ?>
    </div>
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

        $('#city_list').citySelector({multiple: true, data:<?php if (isset($city_list_dict)) {echo $city_list_dict;} else {echo '[]';} ?>});

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

        $('#AdminUserNew_city_id').change(function(){
            var city_id = $('#AdminUserNew_city_id').val();
            if(city_id == ''){
                return true;
            }

            $.ajax({
                url: '<?php echo Yii::app()->createUrl('adminuserNew/GetOrganizaByCity');?>',
                data: {city_id: city_id},
                dataType: 'json',
                type: 'GET',
                success: function(json){

                    if(json.code == 0 && json.data){
                        $('#AdminUserNew_organization_id option').remove();
                        $('#AdminUserNew_organization_id').html("<option value=''>请选择</option>");
                        $.each(json.data,function(id,name) {
                            $('#AdminUserNew_organization_id').append("<option value='" + id+"'>"+name+"</option>");
                        });
                    }

                }
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
