<div class="well span12">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>
    <div class="row-fluid">
        <div class="row span3">
            <label for="BonusLibrary_bonus_sn">开始编号</label>
            <input type="text" id="BonusLibrary_bonus_sn" name="BonusLibrary[start_number]" maxlength="30" size="30">
        </div>

        <div class="row span3">
            <label for="BonusLibrary_bonus_sn">结束编号</label>
            <input type="text" id="BonusLibrary_bonus_sn" name="BonusLibrary[end_number]" maxlength="30" size="30">
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

    </div>
    <div class="row-fluid">
        <div class="row span3">
            <?php echo $form->label($model, 'city_id'); ?>
            <?php
            echo $form->dropDownList($model, 'city_id', $area, array('empty' => '全部', 'onchange' => 'select_channel()'));
            ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, '分配'); ?>
            <?php
            echo $form->dropDownList($model, 'owner', array(), array('empty' => '全部'));
            ?>
        </div>

        <div class="row span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->