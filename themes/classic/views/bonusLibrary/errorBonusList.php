<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bonus-library-grid',
    'dataProvider' => $repdp,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        array(
            'name' => 'number',
            'header' => '实体卷编号',
            'type' => 'raw',
            'value' => '$data["number"]',
        ),
        array(
            'name' => 'bonus_id',
            'header' => '原因',
            'type' => 'raw',
            'value' => '$data["bonus_id"]',
        ),
    ),
));
?>