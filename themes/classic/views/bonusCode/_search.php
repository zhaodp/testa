<div class="well row-fluid">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>
    <div class="span12">
        <div class="span3">
            <?php echo $form->label($model, 'name'); ?>
            <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 60)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'money'); ?>
            <?php echo $form->textField($model, 'money'); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'channel'); ?>
            <?php echo $form->dropDownList($model, 'channel', Dict::items('bonus_channel'), array('empty' => '全部')); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'sn_type'); ?>
            <?php echo $form->dropDownList($model, 'sn_type', Dict::items('bonus_sn_type'), array('empty' => '全部')); ?>
        </div>
    </div>

    <div class="span12">
        <div class="row span3">
            <?php echo $form->label($model, 'user_limited'); ?>
            <?php echo $form->dropDownList($model, 'user_limited', Dict::items('user_limited'), array('empty' => '全部')); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, 'repeat_limited'); ?>
            <?php echo $form->dropDownList($model, 'repeat_limited', Dict::items('repeat_limited'), array('empty' => '全部')); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, 'channel_limited'); ?>
            <?php echo $form->dropDownList($model, 'channel_limited', Dict::items('channel_limited'), array('empty' => '全部')); ?>
        </div>

        <div class="row span3 <?php if(!$this->hasAuditPermission()){ echo('hidden'); }  ?>">
            <?php echo CHtml::label('申请人', 'BonusCode_create_by'); ?>
            <?php echo $form->label($model, 'create_by', array('style' => 'display:none;')); ?>
            <?php echo $form->textField($model, 'create_by', array('size' => 32, 'maxlength' => 32)); ?>
        </div>
    </div>

    <div class="span12">
        <div class="row span3">
            <?php echo $form->label($model, 'status'); ?>
            <?php echo $form->dropDownList($model, 'status', Dict::items('bonus_code_status'), array('empty' => '全部')); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, '&nbsp'); ?>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->