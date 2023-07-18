<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */

$this->breadcrumbs=array(
	'Driver Recommands'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#driver-recommand-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2>司机皇冠管理</h2>

<?php echo CHtml::link('高级搜索','#',array('class'=>'btn search-button')); ?>&nbsp;
<?php echo CHtml::link('添加',Yii::app()->createUrl('DriverRecommand/create'),array('class'=>'btn')); ?>
<div class="search-form span11" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-recommand-grid',
	'itemsCssClass'=>'table table-striped', 
	'dataProvider'=>$model->search(),
	'columns'=>array(
        array (
            'name'=>'序号',
            'headerHtmlOptions'=>array (
                'width'=>'40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->id'
        ),
        array (
            'name'=>'司机工号',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->driver_id'
        ),
        array (
            'name'=>'项目',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>array($this,'getProDisplay')
        ),

        array (
            'name'=>'开始时间',
            'headerHtmlOptions'=>array (
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->type==1?date("Y-m-d H:i", strtotime($data->begin_time)):""'
        ),
        array (
            'name'=>'结束时间',
            'headerHtmlOptions'=>array (
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->type==1?date("Y-m-d H:i", strtotime($data->end_time)):""'
        ),
        array (
            'name'=>'理由',
            'headerHtmlOptions'=>array (
                'width'=>'120px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->reason'
        ),
        array (
            'name'=>'添加时间',
            'headerHtmlOptions'=>array (
                'width'=>'80px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->created'
        ),
        array (
            'name'=>'操作人',
            'headerHtmlOptions'=>array (
                'width'=>'40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->operator'
        ),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{delete}',
            'buttons'=>array(
                'delete'=>array(
                    'label'=>'删除',
                    'url'=>'$this->grid->controller->createUrl("driverRecommand/delete",array("id"=>$data->id));',
                    'options' => array('target'=>'parent'),
                    'visible'=>'$data->type==1?1:0'
                ),
            ),
		),
	),
)); ?>
