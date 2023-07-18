<?php
$this->pageTitle = '域名解析管理';
?>
<?php if(Yii::app()->user->hasFlash('DomainSetting')){ ?>
<script>
    alert('<?php echo Yii::app()->user->getFlash('DomainSetting'); ?>');
</script>
<?php } ?>
<h1>域名解析管理</h1>
<?php echo $model->isNewRecord ? '' : CHtml::link('添加域名解析', array('system/domainAdmin'), array('class'=>'btn', 'style'=>'position: absolute;margin-top: -45px;margin-left: 200px;')); ?>

<div class="form span11 well">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'dict-form',
    'enableAjaxValidation'=>false,
)); ?>

    <p class="note"></p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row span3">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>32,'maxlength'=>32)); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'ip'); ?>
        <?php echo $form->textField($model,'ip',array('size'=>32,'maxlength'=>32)); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model,'active'); ?>
        <?php echo $form->dropDownList($model,'active',array(0=>'未激活', 1=>'已激活')); ?>
    </div>

    <div class="row span12">
        <?php echo $form->labelEx($model,'remark'); ?>
        <?php echo $form->textArea($model,'remark',array('style'=>'width:250px;height:100px;')); ?>
    </div>

    <div class="row buttons span12">
        <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '修改', array('class'=>'btn '.($model->isNewRecord ? 'btn-success' : 'btn-primary'))); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<div class="span12">
    <h2></h2>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'domainSetting-grid',
        'dataProvider'=>$searchModel,
        'htmlOptions'=>array('class'=>'table'),
        'rowCssClassExpression'=>'($row%2==1 ? "odd " : "even ").($data->useable == 0 ? "error " : ($data->useable == 1 ? "success " : ($data->useable == -1 ? "warning " : " ")))',
        'columns'=>array(
            'name',
            'ip',
            array(
                'name'=>'useable',
                'type'=>'raw',
                'value'=>'$data->useable == 0 ? "<span style=\"color:red;\">不可用</span>" : ($data->useable == 1 ? "<span style=\"color:green;\">可用</span>" : ($data->useable == -1 ? "<span style=\"color:gray;\">未知</span>" : ""))',
            ),
            array(
                'name'=>'active',
                'type'=>'raw',
                'value'=>'$data->active == 0 ? "<span style=\"color:red;\">未激活</span>" : ($data->active == 1 ? "<span style=\"color:green;\">已激活</span>" : "")',
            ),
            array(
                'name'=>'create_time',
                'value'=>'date("Y-m-d H:i:s", $data->create_time)',
            ),
            array(
                'name'=>'update_time',
                'value'=>'date("Y-m-d H:i:s", $data->update_time)',
            ),
            array(
                'name'=>'remark',
                'type'=>'raw',
                'value'=>'"<span title=\"".CHtml::encode($data->remark)."\">".substr(CHtml::encode($data->remark), 0, 32)."</span>"',
            ),
            array(
                'header'=>'操作',
                'class'=>'CButtonColumn',
                'template'=>'{update}{delete}',
                'updateButtonUrl'=>'Yii::app()->createUrl("system/domainAdmin", array("id"=>$data->id))',
                'deleteButtonUrl'=>'Yii::app()->createUrl("system/domainAdmin", array("id"=>$data->id, "operation"=>"delete"))',
            ),
        ),
    )); ?>
</div>