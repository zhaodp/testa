<h1>官网vip申请管理</h1>

<?php
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'vip-admin-search',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>

<div class="row-fluid">
    <div class="span3">
        <?php echo $form->label($model,'申请城市',array('class'=>'control-label')); ?>
        <?php echo CHtml::dropDownList('city_id',$model->city_id, Dict::items('city'),array('class'=>'input-large','placeholder'=>'申请城市')); ?>
    </div>
    <div class="span3">
        <?php echo $form->label($model,'name',array('class'=>'control-label')); ?>
        <?php echo CHtml::textField('name',$model->name,array('class'=>'input-large','placeholder'=>'申请人姓名'));?>
    </div>
    <div class="span3">
        <?php echo $form->label($model,'phone',array('class'=>'control-label')); ?>
        <?php echo CHtml::textField('phone',$model->phone,array('class'=>'input-large','placeholder'=>'申请人电话'));?>
    </div>
</div>
<div class="row-fluid">
    <div class="span10">
        <button class="btn btn-primary span2" type="submit" name="search">搜索</button>&nbsp;&nbsp;
    </div>

</div>
<?php $this->endWidget(); ?>
<!--上面搜索-->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'vip-admin-grid',
    'dataProvider'=>$dataProvider,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'columns'=>array(
        array(
            'name'=>'id',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->id'
        ),
        array(
            'name'=>'申请人城市',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'Dict::item("city", $data->city_id)'
        ),
        array(
            'name'=>'name',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->name'
        ),
        array(
            'name'=>'phone',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'Common::parseCustomerPhone($data->phone)'
        ),
        array(
            'name'=>'mail',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->mail'
        ),
        array(
            'name'=>'类型',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'VipApply::$apply_type[$data->type]'
        ),
        array(
            'name'=>'公司名',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->company_name'
        ),
        array(
            'name'=>'申请时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->create_time'
        ),
        array(
            'name'=>'充值金额',
            'headerHtmlOptions'=>array (
                'style'=>'width:20px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->book_money'
        ),
        array(
            'name'=>'备注',
            'headerHtmlOptions'=>array (
                'style'=>'width:200px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->mark'
        ),

    ),
)); ?>
