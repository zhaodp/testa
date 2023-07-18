<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-6-13
 * Time: 下午10:57
 * To change this template use File | Settings | File Templates.
 */
?>
<h1>投诉分类</h1>

<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'complaintype-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

<?php echo $form->errorSummary($model); ?>


    <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>40)); ?>
        <?php echo $form->hiddenField($model,'id'); ?>
        <?php echo $form->hiddenField($model,'parent_id'); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'status'); ?>
        <?php echo $form->dropDownList($model,'status', array('1'=>'正常','2'=>'屏蔽'));?>
        <?php echo $form->error($model,'status'); ?>

    </div>
    <?php if($model->parent_id>0) {?>
    <div class="row">
        <?php  echo $form->labelEx($model,'weight'); ?>
        <?php echo $form->textField($model,'weight');?>
        <?php echo $form->error($model,'weight'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'score'); ?>
        <?php echo $form->textField($model,'score');?>
        <?php echo $form->error($model,'score'); ?>

    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'category'); ?>
        <?php echo $form->textField($model,'category');?>
        <?php echo $form->error($model,'category'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'should_response_hour'); ?>
        <?php echo $form->textField($model,'should_response_hour');?>
        <?php echo $form->error($model,'should_response_hour'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'should_follow_hour'); ?>
        <?php echo $form->textField($model,'should_follow_hour');?>
        <?php echo $form->error($model,'should_follow_hour'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'should_closing_hour'); ?>
        <?php echo $form->textField($model,'should_closing_hour');?>
        <?php echo $form->error($model,'should_closing_hour'); ?>

        <?php echo $form->labelEx($model,'group_id'); ?>
        <?php echo $form->dropDownList($model,'group_id', $group);?>
        <?php echo $form->error($model,'score'); ?>

    </div>
    <?php } ?>

    <div class="row buttons">
        <?php
            if($model->name)
                echo '<button class="btn" type="submit" name="update">保存分类</button>';
            else
                echo '<button class="btn" type="submit" name="createsub">新建分类</button>';
        ?>

    </div>


<?php $this->endWidget(); ?>