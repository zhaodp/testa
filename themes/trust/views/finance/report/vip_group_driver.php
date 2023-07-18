<?php
/**
 * 订单详情
 * User: mtx
 * Date: 13-12-4
 * Time: 下午5:37
 * auther mengtianxue
 */
?>

<h1>VIP销费汇总表</h1>
<ul class="nav nav-tabs">
    <li>
        <?php
        $year_2012 = Yii::app()->createUrl('/finance/vip_trade_info',
            array('year' => '2012'));
        ?>
        <a href="<?php echo $year_2012; ?>">2012年Vip汇总</a>
    </li>

    <li>
        <?php
        $year_2013 = Yii::app()->createUrl('/finance/vip_trade_info',
            array('year' => '2013'));
        ?>
        <a href="<?php echo $year_2013; ?>">2013年Vip汇总</a>
    </li>

    <li>
        <?php
        $year_2014 = Yii::app()->createUrl('/finance/vip_trade_info',
            array('year' => '2014'));
        ?>
        <a href="<?php echo $year_2014; ?>">2014年Vip汇总</a>
    </li>
    <li class="active"><a href="javascript:void(0);"><?php echo $month; ?>司机VIP收入</a></li>
</ul>


<div class="tab-content">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'knowledge-grid',
        'dataProvider' => $data,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            'driver_id',
            array(
                'name' => '订单总数',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data->order_id'
            ),
            'amount',
            'cast',
            'insurance',
            'Invoice_money',
            'balance',
            array(
                'name' => '操作',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => 'CHtml::link("销费明细",Yii::app()->createUrl("finance/fs_vip_trade_info",
        array("driver_id" => $data->driver_id, "daily_date" => $data->daily_date)));'
            ),
        ),
    ));

    ?>
</div>




