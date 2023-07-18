<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'notice-create-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'focus' => array($model, 'NewNoticePost'),
));
?>
    <div class="container">
        <?php echo $form->errorSummary($model); ?>
        <div class="row">
            <div class="span10">
                <?php echo $form->labelEx($model,'title'); ?>
                <?php echo $form->textField($model,'title', array('class' => 'span8')); ?>
                <?php echo $form->error($model,'title'); ?>
            </div>
        </div>
        <div class="row">
            <div class="span10">
                <?php echo $form->labelEx($model,'content'); ?>
                <p>图片上传支持格式"jpg","bmp","gif","png"</p>
                <?php
                $this->widget('application.extensions.ckeditor.CKEditor', array(
                    'attribute'=>'content',
                    'name'=>'content',
                    'value'=>$model->content,
                    'id'=>'editor_id_content',
                    'language'=>'zh-cn',
                    'editorTemplate'=>'public',
                    'options' => array(
                        'height' => '300px',
                        'filebrowserImageUploadUrl'=>'index.php?r=image/imgupload&base_path=notice&type=img',
                    ),
                ));
                ?>
            </div>
        </div>
        <br/>
        <input type="hidden" name="re" value="<?php echo isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:''?>"/>
        <div class="row">
                <?php echo CHtml::submitButton($model->isNewRecord ? '完成' : '保存', array('class' => 'btn-large btn btn-info')); ?>
        </div>
    </div>
<?php $this->endWidget(); ?>
