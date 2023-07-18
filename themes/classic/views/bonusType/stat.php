<?php
$this->pageTitle = '优惠券使用情况';
?>

<h1><?php echo $this->pageTitle; ?></h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'bonus-type-stat-grid',
	'dataProvider'=>$model->search(20),
	'itemsCssClass'=>'table table-striped',
	//'filter'=>$model,
	'columns'=>array(
		array (
			'name'=>'id', 
			'headerHtmlOptions'=>array (
				'width'=>'10px',
				'nowrap'=>'nowrap'
			),
		), 
		array (
			'name'=>'name', 
			'headerHtmlOptions'=>array (
				'width'=>'200px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'channel', 
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("bonus_channel", $data->channel)'
		),		
		array (
			'name'=>'money', 
			'headerHtmlOptions'=>array (
				'width'=>'20px',
				'nowrap'=>'nowrap'
			),
		),
	
		array (
			'name'=>'sn_start', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'sn_end', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'issued', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this, 'getIssuedCount')			
		),		
		array(
			'name' => '绑定个数',
			'type' => 'raw',
			'value' => array($this, 'getBindCount')
		),
		
		array(
			'name' => '消费个数',
			'type' => 'raw',
			'value' => array($this, 'getUsedCount')
		),
		array (
					'name'=>'remark',
					'headerHtmlOptions'=>array (
							'width'=>'60px',
							'nowrap'=>'nowrap'
					),
					'type'=>'raw',
					'value'=>'$data->remark'
			),
	),
)); ?>
