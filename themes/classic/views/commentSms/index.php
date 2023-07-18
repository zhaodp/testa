<?php
$this->pageTitle = Yii::app()->name . ' - 司机评价';
?>

<h1>司机评价</h1>

<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid',
	'dataProvider'=>$dataProvider, 
	'itemsCssClass'=>'table table-striped',
	'htmlOptions'=>array('style'=>'width:98%'),
	'columns'=>array (

		array (
			'name'=>'level', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data->level'
		), 
		array (
			'name'=>'content', 
			'headerHtmlOptions'=>array (
				'width'=>'250px', 
				'nowrap'=>'nowrap'
			)
		), 
		array (
			'name'=>'created', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			)
		)
	)
)
);

?>
