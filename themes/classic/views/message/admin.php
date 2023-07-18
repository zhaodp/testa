<?php
$this->pageTitle = '消息内容管理';
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('order-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<h1><?php echo $this->pageTitle;?></h1>

<hr class="divider"/>
<?php
//title	content	contact address	zipcode	telephone	status	description	created
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid', 
	'dataProvider'=>$dataProvider, 
	'itemsCssClass'=>'table table-striped',
	'columns'=>array (
		array (
			'name' => '序号',
			'value' => '$row + 1'
		),
		array (
			'name' => '类型',
			'value' => 'MessageText::$type[MessageText::$messageDesc[$data->code]["type"]]'
		),
		array (
			'name' => '说明',
			'value' => 'MessageText::$messageDesc[$data->code]["desc"]'
		),		
		array (
			'name' => '内容',
			'value' => '$data->name'
		),
		array(
			'class'=>'CButtonColumn',
            'header'=>'操作',
            'template'=>'{update}'
		)
	)
));
