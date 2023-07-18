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
			'name'=>'comments', 
			'headerHtmlOptions'=>array (
				'width'=>'250px', 
				'nowrap'=>'nowrap'
			)
		), 
		array (
			'name'=>'insert_time', 
			'headerHtmlOptions'=>array (
				'width'=>'60px', 
				'nowrap'=>'nowrap'
			)
		)
	)
)
);

?>
