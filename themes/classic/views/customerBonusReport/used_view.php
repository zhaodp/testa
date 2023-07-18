<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-6-21
 * Time: 下午1:49
 */
?>

<h1>优惠劵使用信息</h1>

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
        'name',
        'driver_id',
        'order_id',
        'bonus_sn',
        array(
            'name' => 'report_time',
            'value' => 'date("Y-m-d",strtotime($data->report_time))'
        ),
        'created'
    ),
)); ?>
