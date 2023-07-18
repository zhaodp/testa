
<h1>地址池管理</h1>
<?php
echo CHtml::link('新建地址',Yii::app()->createUrl('addressPool/map'),array('target'=>'_blank'));
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'address-pool-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'columns'=>array(
		'id',
		array (
			'name'=>'city_id',
			'header'=>'城市',
			'filter'=>Dict::items('city'),
			'type'=>'raw',
			'value'=>'Dict::item("city", $data->city_id)'
		),
		//'hashkey',
		array (
			'name'=>'address',
			'header'=>'地址名',
			'type'=>'raw',
		),
		array (
			'name'=>'lng',
			'header'=>'经度',
			'type'=>'raw',
		),
		array (
			'name'=>'lat',
			'header'=>'纬度',
			'type'=>'raw',
		),
		array (
			'name'=>'times',
			'header'=>'使用次数',
			'type'=>'raw',
		),
		array (
			'name'=>'created',
			'header'=>'创建时间',
			'type'=>'raw',
		),
        array(
            'class'=>'CButtonColumn',
            'header'=>'操作',
            'updateButtonUrl'=>'Yii::app()->createUrl("addressPool/map",array("id"=>$data->primaryKey))',
            'updateButtonOptions'=>array('class'=>'update','target'=>'_blank'),
            'template'=>'{update} {delete}',
        ),
	),
)); ?>
