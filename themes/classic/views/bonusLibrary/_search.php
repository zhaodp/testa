<div class="well span12">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

        <div class="row span3">
            <?php echo $form->label($model, 'bonus_sn'); ?>
            <?php echo $form->textField($model, 'bonus_sn', array('size' => 30, 'maxlength' => 30)); ?>
        </div>

        <div class="row span3" style="display: none;">
            <?php echo $form->label($model, 'bonus_id'); ?>
            <?php $bonusId = is_array($model->bonus_id) ? implode(',', $model->bonus_id) : $model->bonus_id; ?>
            <?php echo CHtml::textField('BonusLibrary[bonus_id]', $bonusId); ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, 'status'); ?>
            <?php
            $bindType = array();
            $bindType[0] = '未绑定';
            $bindType[1] = '已绑定';
            echo $form->dropDownList($model, 'status', $bindType, array('empty' => '全部'));
            ?>
        </div>

        <div class="row buttons">
            <br>
        <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
        </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->