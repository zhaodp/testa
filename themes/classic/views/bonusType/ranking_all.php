<?php
$this->pageTitle = '司机联盟-发卡赚钱汇总';

?>
<h1><?php echo $this->pageTitle; ?></h1>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'driver-bonus-list-grid',
    'itemsCssClass' => 'table table-striped',
    'dataProvider' => $dataProvider,
    'columns' => array(
        array(
            'name' => '排名',
            'headerHtmlOptions' => array(
                'width' => '20px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$row+1'
        ),
        array(
            'name' => '城市',
            'headerHtmlOptions' => array(
                'width' => '30px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("city", $data["city_id"]);'
        ),
        array(
            'name' => '司机',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'isset($data["name"]) ? $data["name"] : ""'
        ),
        array(
            'name' => '工号',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'isset($data["driver_id"]) ? $data["driver_id"] : ""',
        ),

        array(
            'name' => '绑定总数',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data["bonus_count"]'
        ),
        array(
            'name' => '消费总数',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data["used_count"]'
        ),

        array(
            'name' => '收入总金额',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data["amount"]'
        ),
    )
));

?>