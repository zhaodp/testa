<?php
$this->breadcrumbs = array(
    'BonusMerchants' => array('index'),
    'Create',
);
?>
<h1><?php echo isset($_GET["flag"]) ? '充值' : '修改商家'; ?></h1>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bm-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>
    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
            <?php if (!isset($_GET["flag"])) { ?>
                <div>
                    <input id="hi_id" type="hidden" value="<?php echo $_GET['id']; ?>">
                    <?php echo $form->labelEx($model, 'name'); ?>
                    <?php echo $form->textField($model, 'name', array('size' => 15, 'maxlength' => 15)); ?>
                    <?php echo $form->error($model, 'name'); ?>
                </div>
                <div>
                    <?php echo $form->labelEx($model, 'contacts'); ?>
                    <?php echo $form->textField($model, 'contacts', array('size' => 15, 'maxlength' => 15)); ?>
                    <?php echo $form->error($model, 'contacts'); ?>
                </div>
                <div>
                    <?php echo $form->labelEx($model, 'phone'); ?>
                    <?php echo $form->textField($model, 'phone', array('size' => 20, 'maxlength' => 20)); ?>
                    <?php echo $form->error($model, 'phone'); ?>
                </div>
                <div>
                    <?php echo $form->labelEx($model, 'email'); ?>
                    <?php echo $form->textField($model, 'email', array('size' => 20, 'maxlength' => 50)); ?>
                    <?php echo $form->error($model, 'email'); ?>
                </div>
                <div class="row-fluid" style='margin-bottom:10px;'>
                    <?php echo $form->labelEx($model, 'shop_type'); ?>
                    <?php
                    $shop_type = Dict::items('bonus_shop_type');
                    ?>
                    <?php echo $form->radioButtonList($model, 'shop_type', $shop_type,
                        array(
                            'template' => '&nbsp;&nbsp;&nbsp;{input} {label}',
                            'separator' => '&nbsp;&nbsp;',
                            'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;')));?>
                    <?php echo $form->error($model, 'shop_type'); ?>
                </div>
            <?php } else { ?>
                <div>
                    <?php echo $model->name; ?>
                </div>
                <div>
                    <?php echo '账户余额:' . $getBalance; ?>
                </div>
                <div>
                    <?php echo '充值类型'; ?>
                    <?php echo CHtml::dropDownList("BonusMerchants[type]", 1, Dict::items('bonusMerchants_type')); ?>
                </div>
                <div>
                    <?php echo '充值金额'; ?>
                    <?php echo CHtml::textField("BonusMerchants[amount]", 0) . '元'; ?>
                </div>
            <?php } ?>
            <div class="span2">
                <?php echo CHtml::button('提交', array('class' => 'btn btn-success btn-block', 'onclick' => 'disSubmit()')); ?>
            </div>

        </div>
    </div>


    <?php $this->endWidget(); ?>
</div>


<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bm-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <div <?php if (!isset($_GET["flag"])) {
        echo('class="hidden"');
    } ?>>
        <div class="row-fluid">
            <h1>充值记录</h1>

            <div class="span12 well">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'trade-form',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'method' => 'post',
                    'errorMessageCssClass' => 'alert alert-error'
                )); ?>
                <div class="span3">
                    <label for="start_time">开始时间</label>
                    <?php
                    $start_time = isset($data['start_time']) ? date('Y-m-d', $data['start_time']) : date('Y-m-d', strtotime("-1 month"));
                    //$start_time = isset($data['start_time']) ? $data['start_time'] : date('Y-m-d',strtotime("-1 day"));
                    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                    $this->widget('CJuiDateTimePicker', array(
                        'name' => 'start_time',
                        //		'model'=>$model,  //Model object
                        'value' => $start_time,
                        'mode' => 'date', //use "time","date" or "datetime" (default)
                        'options' => array(
                            'dateFormat' => 'yy-mm-dd'
                        ), // jquery plugin options
                        'language' => 'zh',
                    ));
                    ?>
                </div>
                <div class="span3">
                    <label for="end_time">结束时间</label>
                    <?php
                    $end_time = isset($data['end_time']) ? date('Y-m-d', $data['end_time']) : date('Y-m-d', time());
                    //$end_time = isset($data['end_time']) ? $data['end_time'] : date('Y-m-d',time());
                    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                    $this->widget('CJuiDateTimePicker', array(
                        'name' => 'end_time',
                        //		'model'=>$model,  //Model object
                        'value' => $end_time,
                        'mode' => 'date', //use "time","date" or "datetime" (default)
                        'options' => array(
                            'dateFormat' => 'yy-mm-dd'
                        ), // jquery plugin options
                        'language' => 'zh',
                    ));
                    ?>
                </div>
                <div class="span3">
                    <label>&nbsp;</label>
                    <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
<!--                    --><?php
//                    $params = (isset($data['start_time']) ? '&start_time=' . $data['start_time'] : '') .
//                        (isset($data['end_time']) ? '&end_time=' . $data['end_time'] : '') . '&id=' . $_GET['id'];
//                    echo CHtml::link('导出', Yii::app()->createUrl('/bonusMerchants/export' . $params), array('class' => 'btn-primary btn')) . '&nbsp;';
//                    ?>
                </div>
            </div>
            <?php $this->endWidget(); ?>
        </div>

        <?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'trade-grids',
            'dataProvider' => $dataProviderTrade,
            'itemsCssClass' => 'table table-bordered table-striped',
            'columns' => array(
                array(
                    'name' => '日期',
                    'value' => 'date("Y-m-d",strtotime($data->create_time))'
                ),
                array(
                    'name' => '充值金额',
                    'value' => '$data->amount'
                ),
                array(
                    'name' => '充值类型',
                    'value' => 'Dict::item(\'bonusMerchants_type\',$data->type)'
                ),
                array(
                    'name' => '充值人',
                    'value' => '$data->created'
                ),
            ),
        ));
        ?>
    </div>

    <?php $this->endWidget(); ?>
</div>


<script type="text/javascript">
    function disSubmit(){
        if ($('#BonusMerchants_contacts').length > 0) {
            if ($('#BonusMerchants_name').val() == '') {
                alert('商家名称不能为空');
                return false;
            }
            $.ajax({
                type: 'GET',
                async: false,
                url: "<?php echo Yii::app()->createUrl('/bonusMerchants/checkMerchants');?>",
                data: {
                    bonusMerchants_name: $('#BonusMerchants_name').val(),
                    id: $('#hi_id').val()
                },
                success: function (msg) {
                    if (msg != '') {
                        alert(msg);
                        return false;
                    } else {
                        if ($('#BonusMerchants_contacts').val() == '') {
                            alert('商家联系人不能为空');
                            return false;
                        }
                        if ($('#BonusMerchants_phone').val() == '') {
                            alert('联系人电话不能为空');
                            return false;
                        } else {
                            var pattern = /\D/ig;
                            if (pattern.test($('#BonusMerchants_phone').val())) {
                                alert('联系人电话只能为数字');
                                return false;
                            }
                        }
                        if ($('#BonusMerchants_email').val() == '') {
                            alert('邮箱不能为空');
                            return false;
                        } else {
                            var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
                            if (!reg.test($('#BonusMerchants_email').val())) {
                                alert('邮箱格式不正确，请重新填写!');
                                return false;
                            }
                        }


                        $('#bm-form').submit();
                    }
                }
            })
        }

        if ($('#BonusMerchants_amount').length > 0) {
            if ($('#BonusMerchants_amount').val() == '') {
                alert('充值金额不能为空');
                return false;
            }
            if ($('#BonusMerchants_amount').val() == '0') {
                alert('充值金额不能为0');
                return false;
            }
            if (!/^\d+$/.test($('#BonusMerchants_amount').val())) {
                alert('充值金额必须是整数！');
                return false;
            }
            $('#bm-form').submit();
        }
    }

    document.onkeydown = function (event) {
        var target, code, tag;
        if (!event) {
            event = window.event; //针对ie浏览器
            target = event.srcElement;
            code = event.keyCode;
            if (code == 13) {
                tag = target.tagName;
                if (tag == "TEXTAREA") {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        else {
            target = event.target; //针对遵循w3c标准的浏览器，如Firefox
            code = event.keyCode;
            if (code == 13) {
                tag = target.tagName;
                if (tag == "INPUT") {
                    return false;
                }
                else {
                    return true;
                }
            }
        }
    };
</script>


