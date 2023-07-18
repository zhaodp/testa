<?php
if(Yii::app()->user->hasFlash('error')) {
    echo Yii::app()->user->getFlash('error');
}
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'sms-template-form',
    'enableAjaxValidation'=>false,
)); ?>
    <div class="span12">
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="control-group">
                    <div class="controls" style="margin-left:180px;">
                        <?php echo $form->errorSummary($model); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'name',array('class'=>'control-label span2')) ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'name',array('class'=>'input-xlarge info','placeholder'=>'短信模板名称')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'subject',array('class'=>'control-label span2')) ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'subject',array('class'=>'input-xlarge info','placeholder'=>'模板英文名称')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'receive',array('class'=>'control-label span2')) ?>
                    <div class="controls">
                        <?php echo CHtml::activedropDownList($model,'receive',SmsTemplate::$recerves,array('class'=>'info','placeholder'=>'接受者')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'channel',array('class'=>'control-label span2')) ?>
                    <div class="controls">
                        <?php echo CHtml::activedropDownList($model,'channel',SmsTemplate::$channels,array('class'=>'info','placeholder'=>'短信通道')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'type',array('class'=>'control-label span2')) ?>
                    <div class="controls">
                        <?php echo CHtml::activedropDownList($model,'type',SmsTemplate::$types,array('class'=>' info','placeholder'=>'短信类型')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'content',array('class'=>'control-label span2')) ?>
                    <div class="controls">
                        <?php echo CHtml::activeTextArea($model,'content',array('raws'=>16,'style'=>'width:340px;height:100px;','class'=>'info content_id','placeholder'=>'短信内容,可输入变量$hello$，字数限制100内','maxlength'=>100)); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label span2">　</label>
                    <div class="controls">
                        <span style="color:red;" id="span_error_id">可输入变量$hello$，字数限制100个字</span>
                    </div>
                </div>
                <input type="hidden" name="re" value="<?php echo $_SERVER['HTTP_REFERER']?>"/>
                <div class="control-group">
                    <div class="controls">
                        <?php echo CHtml::submitButton($model->isNewRecord ? '添 加' : '保 存',array('class'=>'btn btn-large  btn-success')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--</div>-->
<?php $this->endWidget(); ?>
<script type="text/javascript">
    $('.content_id').keydown(function(){
        $('#span_error_id').html('还可以输入'+(100-$('.content_id').val().length)+'个字');
    });
</script>