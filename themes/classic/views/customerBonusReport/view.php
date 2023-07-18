<?php
/* @var $this CustomerBonusReportController */
/* @var $model CustomerBonusReport */

?>

<h1>绑定信息</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'customer-bonus',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table',
    'pagerCssClass' => 'pagination text-right',
    'pager' => Yii::app()->params['formatGridPage'],
    'columns' => array(
        array(
            'name' => '序号',
            'value' => '$this->grid->dataProvider->getPagination()->getOffset() + ($row + 1)'
        ),
        'customer_phone',
        'bonus_sn',
        array(
            'name' => 'created',
            'value' => 'date("Y-m-d",$data->created)'
        ),
        array(
            'name' => 'used',
            'value' => '($data->used == 0) ? "" : date("Y-m-d",$data->used)',
        ),
    ),
)); ?>
