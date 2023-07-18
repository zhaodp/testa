<?php
switch ($type) {
	case 0:
		$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'order_table',
			'itemsCssClass'=>'table table-striped',
			'dataProvider'=>$dataProvider,
			//'htmlOptions'=>array('class'=>''),
			'columns'=>array(
			        array(
						'name'=>'排名',
						'headerHtmlOptions'=>array(
							'width'=>'40px',
							'nowrap'=>'nowrap'
						),
						'value' => '$row+1'),
					 array(
						'name'=>'司机姓名',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["name"]'),
					 array(
						'name'=>'司机工号',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["driver_id"]'),
					array(
						'name'=>'总接单量',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["order_count"]'),
					 array(
						'name'=>'呼叫中心派单量',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["app_count"]'),
					array(
						'name'=>'客户直接呼叫量',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["callcenter_count"]'),
					array(
						'name'=>'总收入',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["income"]'),
			     ),
			));
		break;
	case 1:
		$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'order_table',
			'itemsCssClass'=>'table table-striped',
			'dataProvider'=>$dataProvider,
			'pager'=>false,
			'template'=>'{items}',
			'columns'=>array(
					 array(
						'name'=>'排名',
						'headerHtmlOptions'=>array(
							'width'=>'40px',
							'nowrap'=>'nowrap'
						),
						'value' => '$row+1'),
					 array(
						'name'=>'司机姓名',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["name"]'),
					 array(
						'name'=>'司机工号',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["driver_id"]'),
					 array(
						'name'=>'出勤天数',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["created"]'),
					array(
						'name'=>'总接单量',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["order_count"]'),
					 array(
						'name'=>'呼叫中心派单量',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["app_count"]'),
					array(
						'name'=>'客户直接呼叫量',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["callcenter_count"]'),
					array(
						'name'=>'总收入',
						'headerHtmlOptions'=>array(
							'width'=>'80px',
							'nowrap'=>'nowrap'
						),
						'value' => '$data["income"]'),
				),
		));
		break;
}