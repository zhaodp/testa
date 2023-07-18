<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-7-8
 * Time: 下午4:08
 */
?>
<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>

    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model, 'order_id'); ?>
            <?php echo $form->textField($model, 'order_id'); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'user'); ?>
            <?php echo $form->textField($model, 'user'); ?>
        </div>

        <div class="span2">
            <?php $citys = Dict::items('account_type');
            $citys[0] = '--请选择交易类型--';
            ?>
            <?php echo $form->label($model, 'type'); ?>
            <?php echo $form->dropDownList($model, 'type', $citys); ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model, '操作人'); ?>
            <?php echo $form->textField($model, 'comment'); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, '备注'); ?>
            <?php echo $form->textField($model, 'id'); ?>
        </div>


        <div class="span3">
            <label for="EmployeeAccount_created">开始时间(只能查同一个月的数据)</label>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $created_date = isset($model) ? $model->attributes['created'] : date('Y-m-d');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'EmployeeAccount[created]',
                'model' => $model, //Model object
                'value' => $created_date,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
        </div>

        <div class="span3">
            <label for="EmployeeAccount_settle_date">结束时间</label>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $created_date = isset($model) ? $model->attributes['settle_date'] : date('Y-m-d');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'EmployeeAccount[settle_date]',
                'model' => $model, //Model object
                'value' => $created_date,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="buttons span3">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn span9')); ?>
        </div>
    </div>
</div>

<?php $this->endWidget(); ?>
</div>