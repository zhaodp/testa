<?php
/* @var $this AdminusernewController */
/* @var $model Adminusernew */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'admin-dep-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <label for="AdminDepartment_name" class="required">小组名称 <span class="required">*</span></label>
    <?php echo $form->textField($model,'name'); ?>
    <?php echo $form->error($model,'name'); ?>

    <?php echo $form->labelEx($model,'desc'); ?>
    <?php echo $form->textField($model,'desc'); ?>
    <?php echo $form->error($model,'desc'); ?>
    <?php echo $form->hiddenField($model,'parent_id',array('value'=>$_GET['dep_id'])); ?>

    <?php echo $form->labelEx($model,'status'); ?>
    <?php echo $form->dropDownList($model,'status',AdminDepartment::getStatus()); ?>
    <?php echo $form->error($model,'status'); ?>


    <div class="buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?>
    </div>

    <legend>&nbsp;</legend>

    <div class="row">
        <h4>功能模块列表：</h4>
        <?php
        if($action_info){

            foreach( $action_info as $g => $item_mods )
            {
                //echo '<legend>'.CHtml::label($g, null, array ( 'style'=>'display:inline')).'</legend>';
                $all_select = " <input type='checkbox'  style='vertical-align:top;' name='chk_all' data='{$g}' />全选/取消";

                echo '<legend><div style="margin-bottom:0px;width:200px;padding:4px 0px 0px 8px" class="alert alert-success">'.CHtml::label($g.$all_select, null)." </div></legend>";
                echo "<div id = '{$g}' >";

                $i=0;
                foreach($item_mods as $item) {
                    $readonly = !$can_edit && $item['can_allocate'] != AdminActions::CAN_ALLOCATE ? true : false;
                    $disabled = !$can_edit && $item['can_allocate'] != AdminActions::CAN_ALLOCATE ? true : false;
                    if($i==0){
                        echo "&nbsp;&nbsp;";
                        $i++;
                    }
                    echo '<label class="checkbox inline">';
                    $selected = isset($role_now_action) && in_array($item['id'],$role_now_action);

                    echo CHtml::checkBox('AdminRole[role][]', $selected, array (
                        'id'=>'AdminRole_' . $item['id'],
                        'value'=>$item['id'],
                        'name'=>$item['controller']."_".$item['action'],
                        'readonly'=>$readonly,
                        'disabled'=>$disabled,
                        'separator'=>''));
                    echo $item['name']."({$item['controller']}/{$item['action']})----描述：{$item['desc']}".'</label><br>';
                }
                echo "</div>";
            }
        }

        ?>

    </div>
    <?php $this->endWidget(); ?>

</div>

<script type="text/javascript">

    $(document).ready(function(){

        $("input[name='chk_all']").click(function(){
            var attr = "checked" == $(this).attr("checked") ? true : false ;
            var id = $(this).attr('data');
            $("#"+id+" label :checkbox").attr("checked",attr );
        });
    });

</script>