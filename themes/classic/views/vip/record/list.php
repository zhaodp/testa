<?php $this->pageTitle = '跟进记录历史 - ' . $this->pageTitle; ?>
<h1>跟进记录历史</h1>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'vip-record-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'table',
    'columns' => array(
        array(
            'name' => 'create_time',
            'type' => 'raw',
            'htmlOptions' => array(
                'style' => 'width:300px;'
            ),
            'value' => '"<font title=\"$data->mark_content\">".Common::wsubstr($data->mark_content, 0, 100) ."</font>"',
        ),
        'operator_id',
        array(
            'name' => 'create_time',
            'value' => 'date("Y-m-d H:i:s", $data->create_time)',
        ),
        array(
            'header' => '平均周消费',
            'value' => '($ext = json_decode($data->ext_info_cost, true)) ? (int)$ext["ave_cost"] : "--"',
        ),
        array(
            'header' => '上上周消费',
            'value' => '($ext = json_decode($data->ext_info_cost, true)) ? (int)$ext["last_second_week_cost"] : "--"',
        ),
        array(
            'header' => '上周消费',
            'value' => '($ext = json_decode($data->ext_info_cost, true)) ? (int)$ext["last_week_cost"] : "--"',
        ),
        array(
            'header' => '变化额',
            'value' => '($ext = json_decode($data->ext_info_cost, true)) ? (int)$ext["change_cost"] : "--"',
        ),
        array(
            'header' => '变化率',
            'value' => '($ext = json_decode($data->ext_info_cost, true)) ? ((int)$ext["change_rate_cost"]."%") : "--"',
        ),
    ),
));