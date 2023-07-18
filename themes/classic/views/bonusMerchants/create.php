<?php
$this->breadcrumbs = array(
    'BonusMerchants' => array('index'),
    'Create',
);
?>
<h1>新增商家</h1>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bonus-merchants-create-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
            <div>
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
            <div>
                <?php echo '优惠劵id(关联多张优惠劵时，请用","隔开)' ?>
                <?php echo CHtml::textField("BonusMerchants[bonus_ids]", ''); ?>
            </div>
            <div class="span2">
                <?php echo CHtml::link('提交', 'javascript:void(0)', array('class' => 'btn btn-success btn-block')); ?>
            </div>
        </div>

        <?php $this->endWidget(); ?>
    </div>
    <script type="text/javascript">
        $('.btn-success').click(function () {
            var name = $('#BonusMerchants_name').val();
            if (name == '') {
                alert('商家名称不能为空');
                return false;
            }
            $.ajax({
                type: 'GET',
                async: false,
                url: "<?php echo Yii::app()->createUrl('/bonusMerchants/checkMerchants');?>",
                data: 'bonusMerchants_name=' + name,
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
                        }else{
                            var pattern = /\D/ig;
                            if(pattern.test($('#BonusMerchants_phone').val())){
                                alert('联系人电话只能为数字');
                                return false;
                            }
                        }
                        if ($('#BonusMerchants_email').val() == '') {
                            alert('邮箱不能为空');
                            return false;
                        }else{
                            var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
                            if (!reg.test($('#BonusMerchants_email').val())) {
                                alert('邮箱格式不正确，请重新填写!');
                                return false;
                            }
                        }
                        var bonus_ids = $('#BonusMerchants_bonus_ids').val();
                        if (bonus_ids != '') {
                            if (bonus_ids.indexOf('，') > 0) {
                                alert('请使用英文","');
                                return false;
                            }
                            $.ajax({
                                type: 'GET',
                                async: false,
                                url: "<?php echo Yii::app()->createUrl('/bonusMerchants/checkBonusIds');?>",
                                data: 'bonus_ids=' + bonus_ids,
                                success: function (msg) {
                                    if (msg != '') {
                                        alert(msg);
                                    } else {
                                        $('#bonus-merchants-create-form').submit();
                                    }
                                }
                            });
                        } else {
                            $('#bonus-merchants-create-form').submit();
                        }
                    }
                }
            });
        });
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

                     
