<div class="row-fluid">
	<?php
	$this->widget('zii.widgets.grid.CGridView',
		array(
			'id' => 'customer-main-grid',
			'dataProvider' => $dataProvider,
			'itemsCssClass' => 'table table-striped',
			'columns' => array(
				array(
					'name' => '日期',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => '$data[\'date\']'
				),
				array(
					'name' => '司机电话',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => '$data[\'phone\']'
				),
				array(
					'name' => '是否成单',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => '($data[\'success\'] == 1 ? \'是\' :  \'否\')'
				),
				array(
					'name' => '城市名',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => '$data[\'city\']'
				),

				array(
					'name' => '客户电话',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => '$data[\'customerPhone\']'
				),
				array(
					'name' => '是否新客',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => '($data[\'isNew\'] == 1? \'是\' :  \'否\')'
				),
				array(
					'name' => '订单详情',
					'headerHtmlOptions' => array(
						'nowrap' => 'nowrap'
					),
					'type' => 'raw',
					'value' => array($this,'getOrderLinkFroThirdCallCenter'),
				),

			)
		)); ?>
</div>
