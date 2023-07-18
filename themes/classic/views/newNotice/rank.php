
<?php
switch ($type){
    case DailyDriverOrderReport::TYPE_DAILY:
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'rank-day-list-grid',
            'template'=>"{items}",
            'dataProvider'=>$dataDailyOrderRank,
            'itemsCssClass'=>'table table-striped',
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
    case DailyDriverOrderReport::TYPE_MONTHLY:
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'rank-day-list-grid',
            'template'=>"{items}",
            'dataProvider'=>$dataDailyOrderRank,
            'itemsCssClass'=>'table table-striped',
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
                    'name'=>'总接单天数',
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
?>