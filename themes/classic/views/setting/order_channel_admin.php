<?php
$this->pageTitle = '销售渠道';
?>
<?php if(Yii::app()->user->hasFlash('saveChannel')){ ?>
<script>
    alert('<?php echo Yii::app()->user->getFlash('saveChannel'); ?>');
</script>
<?php } ?>
<h1>销售渠道</h1>
<?php echo CHtml::link('添加渠道', array('setting/orderChannelAdmin'), array('class'=>'btn', 'style'=>'position: absolute;margin-top: -45px;margin-left: 100px;')); ?>

<div class="form span12">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'dict-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note"></p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'code'); ?>
        <?php echo $form->textField($model,'code',array('size'=>20,'maxlength'=>20)); ?>
        <?php echo $form->error($model,'code'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'name',array('label'=>'渠道名称')); ?>
        <?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '修改', array('class'=>'btn '.($model->isNewRecord ? 'btn-success' : 'btn-primary'))); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<div class="span12">
    <h2></h2>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'dict-grid',
        'dataProvider'=>$searchModel->search(array('order'=>'id DESC')),
        'htmlOptions'=>array('class'=>'table'),
        'columns'=>array(
            'code',
            'name',
            array(
                'class'=>'CButtonColumn',
                'template'=>'{update}',
                'updateButtonUrl'=>'Yii::app()->createUrl("setting/orderChannelAdmin", array("id"=>$data->id))',
            ),
        ),
    )); ?>
</div>