<div class="wide form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>

    <div class="form">
        <?php echo $form->label($model,'mod_code'); ?>
        <?php echo $form->dropDownList($model,
            'mod_code',
                $model->getModCodeConfig(),
            array('empty'=>'全部')
        );?>

    </div>


    <div class="form buttons">
        <?php echo CHtml::submitButton('查询'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->
