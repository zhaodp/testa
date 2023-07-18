<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl($this->route), 'method' => 'get',)); ?>
    <div class="span12">
        <div class="span3">
            <label>抬头</label>
            <?php echo CHtml::textField('title', isset($model['title']) ? $model['title'] : ''); ?>
        </div>
        <div class="span3">
            <label>客户电话</label>
            <?php echo CHtml::textField('customer_phone', isset($model['customer_phone']) ? $model['customer_phone'] : ''); ?>
        </div>
        <div class="span3">
            <label>收件人</label>
            <?php echo CHtml::textField('contact', isset($model['contact']) ? $model['contact'] : ''); ?>
        </div>
        <div class="span3">
            <label>收件人电话</label>
            <?php echo CHtml::textField('telephone', isset($model['telephone']) ? $model['telephone'] : ''); ?>
        </div>
    </div>
    <div class="span12">
        <div class="span3">
            <label>状态</label>
            <?php echo CHtml::dropDownList('isdeal', !empty($model['isdeal']) ? $model['isdeal'] : 9, array(9 => '全部', 0 => '未处理', 1 => '已处理')); ?>
        </div>
        <div class="span3">
            <label>开始时间</label>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'start_time',
                'value' => date('Y-m-d', $model['start_time']),
                'mode' => 'date', ////use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));
            ?>
        </div>
        <div class="span3">
            <label>结束时间</label>
            <?php  $this->widget('CJuiDateTimePicker', array(
                'name' => 'end_time',
                'value' => date('Y-m-d', $model['end_time']),
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));?>
        </div>
        <div class="span3">
            <label>客服确认状态</label>
            <?php echo CHtml::dropDownList('confirm', !empty($model['confirm']) ? $model['confirm'] : 1, array(-1 => '全部', 0 => '未确认', 1 => '已确认', 2 => '取消')); ?>
        </div>
    </div>
    <div class="span12">
        <div class="span3">
            <label>财务确认状态</label>
            <?php echo CHtml::dropDownList('finance_confirm', !empty($model['finance_confirm']) ? $model['finance_confirm'] : 0, array(-1 => '全部', 0 => '未确认', 1 => '已确认')); ?>
        </div>
        <div class="span3">
            <label>开票次数</label>
            <?php echo CHtml::dropDownList('times', isset($model['times']) ? $model['times'] : -1, array(-1 => '全部', 0 => '首次开票', 1 => '非首次开票')); ?>
        </div>
        <div class="span3">
            <label>支付状态</label>
            <?php echo CHtml::dropDownList('pay_type', isset($model['pay_type']) ? $model['pay_type'] : 0, array(0 => '全部', 1 => '已支付', 2 => '未支付', 3 => '待定')); ?>
        </div>
        <div class="span3">
            <label>导出状态</label>
            <?php echo CHtml::dropDownList('export', isset($model['export']) ? $model['export'] : -1, array(-1 => '全部', 0 => '未导出', 1 => '已导出')); ?>
        </div>
    </div>
    <div class="span12">
        <div class="span3">
            <label>是否vip</label>
            <?php echo CHtml::dropDownList('src', isset($model['src']) ? $model['src'] :1, array(0 => '全部', 1 => '非Vip', 2 => 'Vip')); ?>
        </div>
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::Button('导出快递单/发票单', array('class' => 'btn btn-success', 'id' => 'export_sheet')); ?>
        </div>
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::Button('财务确认已导出', array('class' => 'btn btn-success', 'id' => 'confirm_export')); ?>
        </div>
        <!--
	<div class="span3">
            <label>&nbsp;</label>
	    <?php echo CHtml::Button('导入发票单', array('class' => 'btn btn-success', 'id' => 'import_invoice_btn')); ?>
        </div>
    -->
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::Button('导入快递单', array('class' => 'btn btn-success', 'id' => 'import_delivery_btn')); ?>
        </div>

    </div>
    <div class="span12">
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
