<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-7-11
 * Time: 下午1:34
 */
?>
<h1>手动结账</h1>

<div class="well">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>

    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model, 'order_id'); ?>
            <?php echo $form->textField($model, 'order_id'); ?>
        </div>

        <div class="buttons span2">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn span12')); ?>
        </div>
        <div class="buttons span2">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::button('重新结账', array('class' => 'btn span12', 'id' => 'AjaxManualInvoice')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'recharge-grid',
    'dataProvider' => $model->manual_search(),
    'cssFile' => SP_URL_CSS . 'table.css',
    'itemsCssClass' => 'table table-condensed',
    'htmlOptions' => array('class' => 'row span11'),
    'columns' => array(
        array(
            'name' => 'order_id',
            'headerHtmlOptions' => array(
                'width' => '20px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->order_id'
        ),
        array(
            'name' => 'user',
            'headerHtmlOptions' => array(
                'width' => '20px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->user'
        ),
        array(
            'name' => 'cast',
            'headerHtmlOptions' => array(
                'width' => '25px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->cast'
        ),
        array(
            'name' => 'balance',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->balance'
        ),
        array(
            'name' => 'comment',
            'headerHtmlOptions' => array(
                'width' => '200px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->comment'
        ),
        array(
            'name' => 'created',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'date("Y-m-d H:i", $data->created)'
        ),

    ),
)); ?>

<script type="text/javascript">


    $("#AjaxManualInvoice").click(function () {
        var order_id = $("#EmployeeAccount_order_id").val();


        if (order_id == '') {
            alert("必须先输入流水号");
            return false;
        } else {
            if (confirm('确定要为' + order_id + '重新结账吗？')) {
                $.ajax({
                        'url': '<?php echo Yii::app()->createUrl('/account/ajaxManualInvoice'); ?>',
                        'data': 'order_id=' + order_id,
                        'type': 'get',
                        'success': function (data) {
                            if (data == 1) {
                                alert("结账成功");
                                window.location.href = window.location.href;
                            } else {
                                alert("结账失败");
                            }
                        },
                        'cache': false
                    }
                )
                ;
            }
        }
    })
    ;
</script>



