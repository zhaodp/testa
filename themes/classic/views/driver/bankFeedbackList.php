<?php
/* @var $this DriverBankResultController */
/* @var $model DriverBankResult */

$this->breadcrumbs = array(
    'Driver Bank Results' => array('index'),
    'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#driver-bank-result-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

<h2>司机签约信息</h2>
<div class="search-form">
    <div class="row-fluid">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        )); ?>
        <div class="row-fluid">
            <div class="row span3">
                <?php echo $form->label($model, 'pay_name'); ?>
                <?php echo $form->textField($model, 'pay_name', array('size' => 20, 'maxlength' => 20)); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'fees_name'); ?>
                <?php echo $form->textField($model, 'fees_name', array('size' => 20, 'maxlength' => 20)); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'driver_id'); ?>
                <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 10)); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'amount'); ?>
                <?php echo $form->textField($model, 'amount'); ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="row span3">
                <?php echo $form->label($model, 'result'); ?>
                <?php echo $form->dropDownList($model,
                    'result',
                    array(
                        '' => '请选择',
                        '待确认' => '待确认',
                        '入账成功' => '入账成功',
                        '入账失败' => '入账失败',
                    )
                ); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'status'); ?>
                <?php echo $form->dropDownList($model,
                    'status',
                    array(
                        '' => '请选择',
                        '0' => '未结账',
                        '1' => '已结账',
                    )
                ); ?>
            </div>

            <div class="row span3">
                <?php echo $form->label($model, 'created'); ?>
                <?php

                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'DriverBankResult[created]',
                    'model' => $model, //Model object
                    'value' => '',
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh'
                ));

                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="row span3 buttons">
                <?php echo CHtml::submitButton('Search'); ?>
            </div>
        </div>

        <?php $this->endWidget(); ?>

    </div>
    <!-- search-form -->
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'driver-bank-result-grid',
    'itemsCssClass' => 'table table-striped',
    'dataProvider' => $model->search(),
    'columns' => array(
        array(
            'class' => 'CCheckBoxColumn',
            'selectableRows' => 2,
            'footer' => '<button type="button" onclick="GetCheckbox();" style="width:76px">批量结账</button>',
            'headerHtmlOptions' => array('width' => '33px'),
            'checkBoxHtmlOptions' => array('name' => 'selectUpdate[]'),
        ),
        'id',
        'sign_no',
        'pay_no',
        'pay_name',
        'fees_name',
        'driver_id',
        'amount',
        'remark',
        'result',
        'error_reason',
        array(
            'name' => 'status',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'),
            'type' => 'raw',
            'value' => '($data->status == 0) ? "未结账" : "<span style=\'color:red;\'>已结账</span>"'
        ),
        'created',
//        array(
//            'name' => '状态修改',
//            'headerHtmlOptions' => array(
//                'width' => '100px',
//                'nowrap' => 'nowrap'),
//            'type' => 'raw',
//            'value' => '($data->status != 0) ? "" : (($data->result != "入账成功") ? "" : CHtml::link("激活并充值", "javascript:void(0);", array("onclick"=>"{del(\'$data->id\',\'$data->driver_id\', \'$data->amount\');}")))'),
        /*
            'created',
        */
//		array(
//			'class'=>'CButtonColumn',
//			'template'=>'{update} {delete}',
//		),

    ),
)); ?>
<script type="text/javascript">
    <!--
    function del(id, driver_id, amount) {
        if (!confirm('确定要给该司机激活并充值吗?')) return false;
        var url = '<?php echo Yii::app()->createUrl('driver/activeAndRecharge');?>';
        $.ajax({
            'url': url,
            'data': 'id=' + id + '&driver_id=' + driver_id + '&amount=' + amount,
            'type': 'get',
            'success': function (data) {
                if (data == 1) {
                    alert("操作成功");
                } else {
                    alert("操作失败");
                }
                $('.search-form form').submit();
            },
            'cache': false
        });
    }
    function GetCheckbox() {
        if (!confirm('确定要给该司机激活并充值吗?')) return false;

        var data = new Array();
        $("input:checkbox[name='selectUpdate[]']").each(function () {
            if ($(this).attr("checked") == 'checked') {
                data.push($(this).val());
            }
        });

        if (data.length > 0) {
            $.post("index.php?r=driver/activeAndRecharges", {'selectID': data}, function (num) {
                if (num == 0) {
                    alert('入账失败！');
                } else {
                    alert("选了" + data.length + "条数据，其中有" + num + '充值成功！');
                }
                $('.search-form form').submit();
            });
        } else {
            alert("请选择要充值的选项!");
        }
    }
    //-->
</script>

