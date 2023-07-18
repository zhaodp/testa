<div class="wide form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <?php if (Yii::app()->user->city == 0) { ?>
        <div class="row span3">
            <?php echo $form->label($model, 'city'); ?>
            <?php echo $form->dropDownList($model, 'city', Dict::items('city')); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, 'department'); ?>
            <?php echo $form->dropDownList($model, 'department', Dict::items('department')); ?>
        </div>
    <?php } ?>

    <div class="row span3">
        <?php echo $form->label($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20)); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, '&nbsp'); ?>
        <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
        <?php if (AdminRoles::model()->havingPermissions('adminuser', 'create')) echo CHtml::link('创建用户', Yii::app()->createUrl('/adminuser/create'), array('class' => 'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->