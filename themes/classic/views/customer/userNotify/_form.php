<?php
/* @var $this UserNotifyController */
/* @var $model UserNotify */
/* @var $form CActiveForm */
?>
<style>
    div.form label.labelForRadio {display:inline-block;width:auto;float:none; margin:10px;text-align:center}
</style>
<div class="form span11">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-notify-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
    'enableClientValidation'=>true,
    'clientOptions' => array(
        'validateOnSubmit' => true  //在这个位置做验证
    ),
    'action'=>array('customer/userNotify'),

)); ?>


     <?php echo $form->errorSummary($model); ?>
    <input type="hidden" name='action' value='save'>
	<div class="row">
         <?php echo $form->labelEx($model,'city_id'); ?>
        <input type="checkbox" name="che_all" id="che_all" value="1">&nbsp;全选&nbsp;&nbsp;
        <input type="checkbox" name="unche_all" id="unche_all" value="1">&nbsp;反选
        <br><br>
        <input type="checkbox" name="all_city" id="all_city" value="0">全部城市（含未开通代驾服务的城市）<br>
        <?php

        //$citys = Dict::items('city');
        $citys = RCityList::model()->getOpenCityList();
        unset($citys[0]);
        foreach ($citys as $key=>$item){
            $disabled = false;
            echo CHtml::checkBox("city[]",false,array("value"=>$key,'class'=>'city_id','disabled'=>$disabled)).$item.'&nbsp;&nbsp;';
        }

        ?>

        <?php echo $form->error($model,'city_id'); ?>
	</div>
    <br><br>
	<div class="row">
         <?php echo $form->labelEx($model,'user_type'); ?>
         <?php echo $form->dropDownList($model, 'user_type', Dict::items('user_type')); ?>
         <?php echo $form->error($model,'user_type'); ?>
	</div>

	<div class="row">
         <?php echo $form->labelEx($model,'notify_type'); ?>
         <?php echo $form->dropDownList($model, 'notify_type', Dict::items('notify_type')); ?>
         <?php echo $form->error($model,'notify_type'); ?>
	</div>
   
	<div class="row">
         <?php echo $form->labelEx($model,'client_os_type'); ?>
        <?php echo $form->dropDownList($model, 'client_os_type', Dict::items('client_os_type')); ?>
         <?php echo $form->error($model,'client_os_type'); ?>
	</div>

	<div class="row">
         <?php echo $form->labelEx($model,'client_version_lowest'); ?>
         <?php echo $form->textField($model,'client_version_lowest',array('size'=>60,'maxlength'=>255)); ?>
         <?php echo $form->error($model,'client_version_lowest'); ?>
	</div>

	<div class="row">
         <?php echo $form->labelEx($model,'sdate'); ?>

        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array (
            'name'=>'UserNotify[sdate]',
            'model'=>$model,  //Model object
            'mode'=>'datetime',  //use "time","date" or "datetime" (default)
            'value'=>$model->sdate,
            'options'=>array (
                'dateFormat'=>'yy-mm-dd'
            ),  // jquery plugin options
            'language'=>'zh',
            'htmlOptions'=>	array('size'=>60,'maxlength'=>100)
        ));
        ?>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array (
            'name'=>'UserNotify[edate]',
            'model'=>$model,  //Model object
            'mode'=>'datetime',  //use "time","date" or "datetime" (default)
            'value'=>$model->edate,
            'options'=>array (
                'dateFormat'=>'yy-mm-dd'
            ),  // jquery plugin options
            'language'=>'zh',
            'htmlOptions'=>	array('size'=>60,'maxlength'=>100)
        ));
        ?>
         <?php echo $form->error($model,'sdate'); ?>
        <?php echo $form->error($model,'edate'); ?>
	</div>




	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '下一步' : 'Save',array('name' => 'save','id'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script>



    $('#che_all').click(function(){
        if($(this).attr("checked")){

            $("input:enabled[name='city[]']").each(function(){
                $(this).attr("checked","true");
                $('#unche_all').removeAttr("checked");
            });
        }//else{
        //       $("input[name='city[]']").each(function(){
        //            $(this).removeAttr("checked");
        //   });
        //   }
    });

    $('#unche_all').click(function(){
        if($(this).attr("checked")){
            $("input:enabled[name='city[]']").each(function(){
                if($(this).attr("checked")){
                    $(this).removeAttr("checked");
                    $('#che_all').removeAttr("checked");
                }else{
                    $(this).attr("checked","true")
                }
            });
        }//else{
        //     $("input[name='city[]']").each(function(){
        //          $(this).removeAttr("checked");
        // });
        // }
    });

    $('.city_id').click(function(){
        if(this.checked==false){
            $('#che_all').attr('checked',false);
        }else if($(".city_id:checked").size()==$('.city_id').length){
            $('#che_all').attr('checked',true);
        }
    });


</script>