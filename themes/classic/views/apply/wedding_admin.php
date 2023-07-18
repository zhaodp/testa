<h1>代驾体验申请管理</h1>

<?php
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'wedding-admin-search',
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
        <?php echo CHtml::textField('phone',$model->phone,array('class'=>'input-large','placeholder'=>'联系电话'));?>
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
    'id'=>'wedding-admin-grid',
    'dataProvider'=>$dataProvider,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'columns'=>array(
        array(
            'name'=>'id',
            'value' =>'$data->id'
        ),
        array(
            'name'=>'申请人城市',
            'value' =>'Dict::item("city", $data->city_id)'
        ),
        array(
            'name'=>'name',
            'value' =>'$data->name'
        ),
        array(
            'name'=>'phone',
            'value' =>'$data->phone'
        ),
        array(
            'name'=>'create_time',
            'value' =>'$data->create_time'
        ),
        array(
            'name'=>'宴会类型',
            'value' =>'WeddingApply::$wedding_types[$data->wedding_type]'
        ),
        array(
            'name'=>'举办时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->run_time'
        ),
        array(
            'name'=>'参加人数',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->number'
        ),
        array(
            'name'=>'举办酒店',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->hotels'
        ),
        array(
            'name'=>'宴会详细地址',
            'headerHtmlOptions'=>array (
                'style'=>'width:160px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->detail_site'
        ),
        array(
            'name'=>'mark',
            'headerHtmlOptions'=>array (
                'style'=>'width:200px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'$data->mark'
        ),

    ),
)); ?>
