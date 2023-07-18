<?php
    $this->pageTitle = 'vip消费月统计 - ' . $this->pageTitle;
?>

<h1>vip消费月统计</h1>

<div class="search-form">
<?php $this->renderPartial('report/_search',array(
    'model'=>$model,
)); ?>
</div>

<?php
$criteria = new CDbCriteria;
$criteria->order = 'month DESC';
$dataProvider = $model->search($criteria, FALSE);

$items = $dataProvider->getData();
?>

<div calss="chart" style="margin-bottom: 180px;">
    <?php
        $this->renderPartial('report/_costReportList_chart',array('items'=>$items));
    ?>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'vip-grid',
    'dataProvider' => $dataProvider,
    'enableSorting' => FALSE,
    'cssFile' => SP_URL_CSS . 'table.css',
    'itemsCssClass' => 'table  table-condensed',
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'columns' => array(
        'month',
        array(
            'name' => 'city_id',
            'header' => '城市',
            'value' => 'Dict::item("city",$data->city_id)'
        ),
        array(
            'name' => 'vip_cost_sum_month',
            'header' => 'vip消费总额<font class="muted">(比例<i title="当月VIP总消费/当月总订单金额" class="icon-question-sign"></i>)</font>',
            'type' => 'raw',
            'value' => '$data->vip_cost_sum_month.($data->all_cost_sum_month > 0 ? ("<font class=\"muted\">(".substr($data->vip_cost_sum_month/$data->all_cost_sum_month*100,0,4)."%".")</font>") : "")',
        ),
        array(
            'name' => 'vip_order_count_month',
            'header' => 'vip订单数量<font class="muted">(比例<i title="当月VIP订单数/总订单数" class="icon-question-sign"></i>)</font>',
            'type' => 'raw',
            'value' => '$data->vip_order_count_month.($data->all_order_count_month > 0 ? ("<font class=\"muted\">(".substr($data->vip_order_count_month/$data->all_order_count_month*100,0,4)."%".")</font>") : "")',
        ),
        array(
            'header' => '客单价',
            'value' => '($data->vip_order_count_month > 0 && $ave = $data->vip_cost_sum_month/$data->vip_order_count_month) ? substr($ave,0,strpos($ave, ".")+3) : 0',
        ),
        array(
            'header' => '月均消费',
            'value' => '($data->vip_count_month > 0 && $ave = $data->vip_cost_sum_month/$data->vip_count_month) ? substr($ave,0,strpos($ave, ".")+3) : 0',
        ),
        array(
            'header' => '月均消费次数',
            'value' => '($data->vip_count_month > 0 && $ave = $data->vip_order_count_month/$data->vip_count_month) ? substr($ave,0,strpos($ave, ".")+5) : 0',
        ),
        'recharge_month',
        'vip_new_count',
        array(
            'name' => 'vip_count_month',
            'header' => 'VIP总数<font class="muted">(比例<i title="当月总VIP数/总客户数" class="icon-question-sign"></i>)</font>',
            'type' => 'raw',
            'value' => '$data->vip_count_month.($data->customer_count_month > 0 ? ("<font class=\"muted\">(".substr($data->vip_count_month/$data->customer_count_month*100,0,4)."%".")</font>") : "")',
        ),
    ),
));