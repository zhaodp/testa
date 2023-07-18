<?php
/* @var $this VipCostMonthController */
/* @var $model VipCostMonth */
/* @var $form CActiveForm */
/* @author liuxiaobo */
/* @since 2014-1-7 */
?>

<div class="well search-form">
    <?php
        $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        ));
    ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model, 'id'); ?>
            <?php echo $form->textField($model, 'id'); ?>
        </div>
        <div class='span3'>
            <?php echo $form->label($model, 'company') ?>
            <?php echo $form->textField($model, 'company') ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'phone'); ?>
            <?php echo $form->textField($model, 'phone'); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('VIP状态', 'status'); ?>
            <?php
                $status = Dict::items('vip_status');
                $status[0] = '全部';
                ksort($status);
                echo $form->dropDownList($model, 'status', $status);
            ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('变化率(金额)', 'sdf'); ?>
            <?php echo $form->dropDownList($model, 'changeRateType', array('>' => '>', '=' => '=', '<' => '<'), array('class' => 'span4')); ?>
            <?php echo $form->textField($model, 'changeRate', array('class' => 'span4')); ?>
            <span style="font-size: 16px;margin-left: -20px;margin-top:-3px;color:rgb(158,158,158);">%</span>
        </div>
        <div class="span3">
            <?php echo CHtml::label('平均周消费', 'sdf'); ?>
            <?php echo $form->dropDownList($model, 'aveCostType', array('>' => '>', '=' => '=', '<' => '<'), array('class' => 'span4')); ?>
            <?php echo $form->textField($model, 'aveCost', array('class' => 'span6')); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('变化量(金额)', 'sdf'); ?>
            <?php echo $form->dropDownList($model, 'changeCostType', array('>' => '>', '=' => '=', '<' => '<'), array('class' => 'span4')); ?>
            <?php echo $form->textField($model, 'changeCost', array('class' => 'span6')); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('跟进状态', 'status'); ?>
            <?php
            $recordStatus = array('0' => '未处理', '1' => '已处理');
            echo $form->dropDownList($model, 'recordStatus', $recordStatus, array('empty' => '全部'));
            ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php
                echo CHtml::submitButton('搜索', array('class' => 'btn btn-success'));
            ?>

        </div>
    </div>
</div>
<?php $this->endWidget(); 