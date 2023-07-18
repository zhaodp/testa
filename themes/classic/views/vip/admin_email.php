<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs = array(
    'Vips' => array('index'),
    'Manage',
);


$this->pageTitle = 'VIP 发送邮件记录';
?>

    <h1>VIP 发送邮件记录</h1>

    <div class="row-fault well" style="height: 100px;">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        )); ?>
        <div class="span12">
            <div class="span3">
                <?php echo $form->label($model, 'vipcard'); ?>
                <?php echo $form->textField($model, 'vipcard'); ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'type'); ?>
                <?php echo $form->dropDownList($model, 'type', array('' => '全部', '0' => '日账单', '1' => '月账单')); ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'status'); ?>
                <?php echo $form->dropDownList($model, 'status', array('' => '全部', '0' => '未发送', '1' => '已发送')); ?>
            </div>
            <div class="span3">
                <?php echo $form->label($model, '&nbsp;'); ?>
                <?php echo CHtml::submitButton('搜 索', array('class' => 'btn')); ?>
                <?php echo CHtml::link('补发邮件',Yii::app()->createUrl("/vip/create_email"), array('class' => 'btn')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
        <!-- search-form -->
    </div>



<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'VipEmailLog-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'table',
    'columns' => array(
        'id',
        'email',
        'vipcard',
        array(
            'name' => 'type',
            'type' => 'raw',
            'value' => '($data->type == 1) ? "月账单" : "日账单"'
        ),
        'vip_bill_time',
        'send_time',
        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => '($data->status == 1) ? "已发送" : "未发送"'
        ),
        'remarks',
        array(
            'name' => '重新发送',
            'type' => 'raw',
            'value' => 'CHtml::link("重新发送", array("vip/create_email", "id"=>$data->id))'
        ),

    ),
));
?>