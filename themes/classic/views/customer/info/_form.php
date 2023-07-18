<?php
/* @var $this CustomerMainController */
/* @var $model CustomerMain */
/* @var $form CActiveForm */
?>

<?php 
	if(Yii::app()->user->hasFlash('error')) {
		echo Yii::app()->user->getFlash('error');
	}
?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customer-main-form',
	'enableAjaxValidation'=>false,
)); ?>
<!--<div class="container-fluid">-->
<div class="span12">
    <div class="span6 ">

        <div class="row-fluid">
            <div class="span6">
                <?php echo $form->labelEx($model,'name'); ?>
                <?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
                <?php echo $form->error($model,'name'); ?>
            </div>
            <div class="span2">
                <?php echo $form->labelEx($model,'gender'); ?>
                <?php echo CHtml::dropDownList('gender',$model->gender, array('1'=>'男','2'=>'女'),array('class'=>'span9')); ?>
                <?php echo $form->error($model,'gender'); ?>
            </div>
            <div class="span4">
                <?php echo $form->labelEx($model,'city_id'); ?>
                <?php echo $form->dropDownList($model, 'city_id', Dict::items('city'),array('class'=>'span7')); ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <?php echo $form->labelEx($model,'phone'); ?>
                <?php echo $form->textField($model,'phone',array('size'=>32,'maxlength'=>32)); ?>
            </div>
            <div class="span6">
                <?php echo $form->labelEx($model,'birthday'); ?>
                <?php echo $form->textField($model,'birthday'); ?>
                <?php echo $form->error($model,'birthday'); ?>
            </div>

        </div>

        <div class="row-fluid">
            <div class="span6">
                <?php echo $form->labelEx($model,'backup_phone'); ?>
                <?php echo $form->textField($model,'backup_phone',array('size'=>32,'maxlength'=>32)); ?>
                <?php echo $form->error($model,'backup_phone'); ?>
            </div>
            <div class="span6">
                <?php echo $form->labelEx($model,'email'); ?>
                <?php echo $form->textField($model,'email',array('size'=>32,'maxlength'=>32)); ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6">
                <?php echo $form->labelEx($model,'vip_card'); ?>
                <?php echo $form->textField($model,'vip_card',array('size'=>50,'maxlength'=>50)); ?>
                <?php echo $form->error($model,'vip_card'); ?>
            </div>
            <div class="span6">
                <?php echo $form->labelEx($model,'company'); ?>
                <?php echo $form->textField($model,'company',array('size'=>50,'maxlength'=>50)); ?>
                <?php echo $form->error($model,'company'); ?>
            </div>
        </div>

        <div class="row-fluid">

            <div class="span6">
                <?php echo $form->labelEx($model,'remark'); ?>
                <?php echo $form->textArea($model,'remark',array('style'=>"width: 218px; height: 90px;")); ?>
                <?php echo $form->error($model,'remark'); ?>
            </div>
            <div class="span6">
                <?php echo $form->labelEx($model,'invoice_title'); ?>
                <?php echo $form->textField($model,'invoice_title',array('size'=>50,'maxlength'=>50)); ?>
                <?php echo $form->error($model,'invoice_title'); ?>

                <?php echo $form->labelEx($model,'invoice_remark'); ?>
                <?php echo $form->textField($model,'invoice_remark',array('size'=>50,'maxlength'=>50)); ?>
                <?php echo $form->error($model,'invoice_remark'); ?>
            </div>

        </div>
    </div>
    <div class="span3">
        <div class="row-fluid">
            <div class="span12">
                <?php echo $form->labelEx($model,'status'); ?>
                <?php echo $form->dropDownList($model,'status',array('1'=>'正常','2'=>'屏蔽')); ?>
                <?php echo $form->error($model,'status'); ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $form->labelEx($model,'bill_receive_mode'); ?>
                <?php echo $form->dropDownList($model,'bill_receive_mode',array('1'=>'短信','2'=>'邮件','3'=>'短信&邮件')); ?>
                <?php echo $form->error($model,'bill_receive_mode'); ?>
            </div>

        </div>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $form->labelEx($model,'type'); ?>
                <?php echo $form->dropDownList($model,'type',CustomerMain::$type_dict); ?>
                <?php echo $form->error($model,'type'); ?>

            </div>

        </div>
        <div class="row-fluid">
            <div class="span12">
                <?php echo $form->labelEx($model,'channel'); ?>
                <?php
                $channel_list = CustomerMain::$channel_dict;
                ?>
                <?php echo $form->dropDownList($model,'channel',$channel_list); ?>
                <?php echo $form->error($model,'channel'); ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
            </div>
        </div>
        <input type="hidden" name="re" value="<?php echo isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:''?>"/>
        <div class="row-fluid">
            <div class="span12">
                <?php echo CHtml::submitButton($model->isNewRecord ? '添 加' : '保 存',array('class'=>'btn btn-large  btn-success')); ?>
            </div>
        </div>
    </div>
</div>
<!--</div>-->
<?php $this->endWidget(); ?>

