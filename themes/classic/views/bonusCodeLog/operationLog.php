<h1>优惠券操作记录</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'bonus-code-log-grid',
	'dataProvider'=>$model->search(),
    'itemsCssClass' => 'table',
//	'filter'=>$model,
	'columns'=>array(
		'remark',
		'operation',
		'operator',
		'created',
	),
)); ?>
