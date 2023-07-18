
<?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'bonus-library-grid',
            'dataProvider' => $data,
            'itemsCssClass' => 'table table-striped',
            'enableSorting' => FALSE,
            'columns' => array(
                array(
                    'name' => 'creation_date',
                    'header' => '分配时间',
                    'type' => 'raw',
                    'value' => 'substr($data["created"],0,16)',
                ),
                array(
                    'name' => 'channel',
                    'header' => '数量',
                    'type' => 'raw',
                    'value' => '$data["channel"]',
                ),
            ),
        ));
?>

