<?php
/* @var $this BonusRulesController */
/* @var $model BonusRules */
/* @var $form CActiveForm */
?>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/upload/jquery.ui.widget.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/upload/jquery.fileupload.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/upload/jquery.iframe-transport.js"></script>

<style>
    .bar {
        height: 18px;
        background: green;
    }
</style>
<div style="width: 100%">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bonus-rules-form',
        'enableAjaxValidation' => false,
    )); ?>

    <p class="note"><span class="required">*</span> 为必填项</p>

    <?php echo $form->errorSummary($model); ?>



    <div style="width:50%; float: left;">

        <?php echo $form->labelEx($model, 'bonus_sn'); ?>
        <?php echo $form->textField($model, 'bonus_sn', array('size' => 20, 'maxlength' => 20)); ?>
        <?php echo $form->error($model, 'bonus_sn'); ?>

        <?php echo $form->labelEx($model, '绑定张数（每人）'); ?>
        <?php echo $form->textField($model, 'number'); ?>
        <?php echo $form->error($model, 'number'); ?>

        <?php echo $form->labelEx($model, 'merchants'); ?>
        <?php echo $form->textField($model, 'merchants', array('size' => 30, 'maxlength' => 30)); ?>
        <?php echo $form->error($model, 'merchants'); ?>
        <?php echo $form->labelEx($model, 'sms'); ?>
        <?php echo $form->textArea($model, 'sms', array('rows' => 5, 'cols' => 50)); ?>
        <?php echo $form->error($model, 'sms'); ?>
    </div>


    <div style="width:50%;  float: left;">
        <label for="uploadFileId"><input type="radio" name="importType" value="2" id="type_01">通过文件上传手机号(支持纯文本文件,最好不要超过25000呦)：</label>

        <div style="display: none"><input name="phoneText" type="file" id="uploadFileId"/></div>
        <input type="hidden" id="phone_text_loc" name="phoneTextLoc">
        <div id="upload-process"></div>

        <div id="progress">
            <div class="bar" style="width: 0%;"></div>
        </div>

        <?php echo $form->labelEx($model, 'phone'); ?>
        <?php echo $form->textArea($model, 'phone', array('rows' => 10, 'cols' => 30,'id'=>'phoneTextArea')); ?>
        <?php echo $form->error($model, 'phone'); ?>
    </div>
    <div style="width:100%;  float: left;">
        <?php echo CHtml::submitButton($model->isNewRecord ? '保存' : 'Save', array('class' => 'btn')); ?>
    </div>
    <?php $this->endWidget(); ?>

    <!-- form -->
</div>

<script>
    $('#uploadFileId').fileupload({
        autoUpload: true,
        url: "<?php echo Yii::app()->createUrl('/bonusCode/bonus_rules_create_upload');?>",
        dataType: 'json',
        done: function (e, data) {

            if(data.result.code == 0){
                $("#phone_text_loc").val(data.result.target);
                $("#upload-process").html("共上传"+data.result.totalNum+"个号码");
            }

        },
        add: function (e, data) {
            data.context = $('<p/>').text('上传中...').appendTo($("#upload-process"));
            data.submit();
        },
        progressall: function (e, data) {//设置上传进度事件的回调函数
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
        }

    });

    $(function(){
        $("#uploadFileId").click(function(){
            $("#type_01").trigger("click");
        });

        $("#phoneTextArea").focus(function(){
            $("#type_02").trigger("click");

        });
    });
</script>