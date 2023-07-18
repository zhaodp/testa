<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<div class="container-fluid">
    <div class="row-fluid" style="margin-top: 100px;">

        <div class="span6">
            <!--Body content-->
        </div>

        <div class="span6">
            <div class="form">
                <?php $form=$this->beginWidget('CActiveForm', array(
                    'id'=>'login-form',
                    'enableClientValidation'=>true,
                    'clientOptions'=>array(
                        'validateOnSubmit'=>true,
                    ),
                    'htmlOptions' => array(
                        'class' => 'form-horizontal'
                    )
                )); ?>

                <div class="control-group">
                    <?php echo $form->labelEx($model,'username', array('class'=>'control-label')); ?>
                    <div class="controls">
                    <?php echo $form->textField($model,'username'); ?>
                    <?php echo $form->error($model,'username'); ?>
                    </div>
                </div>

                <div class="control-group">
                    <?php echo $form->labelEx($model,'password', array('class'=>'control-label')); ?>
                    <div class="controls">
                    <?php echo $form->passwordField($model,'password'); ?>
                    <?php echo $form->error($model,'password'); ?>
                    </div>
                </div>

                 <div class="control-group">
                    <?php echo $form->labelEx($model,'verifyCode', array('class'=>'control-label')); ?>
                    <div class="controls">
                    <?php echo $form->textField($model,'verifyCode', array('style'=>'width:80px')); ?>
                    <?php
                        $this->widget('CCaptcha',array(
                            'showRefreshButton' => false,
                            'clickableImage' => true,
                            'imageOptions' => array('class' => 'captche','title'=>'重新获取'),
                        ));
                    ?>
                    <?php echo $form->error($model,'verifyCode'); ?>
                    </div>
                </div>


                <div class="control-group">
                    <div class="controls">
                        <!--
                        <label class="checkbox">
                            <?php echo $form->checkBox($model,'rememberMe'); ?>
                            <?php echo $form->label($model,'rememberMe'); ?>
                            <?php echo $form->error($model,'rememberMe'); ?>
                        </label>
                        -->
                        <?php echo CHtml::submitButton('登录', array('class'=>'btn btn-large', 'style'=>'width:200px')); ?>
                    </div>
                </div>

                <?php $this->endWidget(); ?>

            </div><!-- form -->
        </div>
    </div>
</div>
