<?php
/* @var $model VipSingleWeekTrend */
$this->pageTitle = 'vip消费统计 - ' . $this->pageTitle;
?>

<h1></h1>

<p></p>
<?php
$criteria = new CDbCriteria;
$criteria->order = 'start_time DESC';
$dataProvider = $model->search($criteria, 0);

$items = $dataProvider->getData();
?>


<div calss="chart">
    <?php
    $this->renderPartial('record/_costWeekList_chart', array('items' => $items));
    ?>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'vip-single-week-trend-grid',
    'dataProvider' => $dataProvider,
    'enableSorting' => FALSE,
    'ajaxUpdate' => FALSE,
    'cssFile' => SP_URL_CSS . 'table.css',
    'itemsCssClass' => 'table  table-condensed',
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'columns' => array(
        array(
            'header' => '时间',
            'type' => 'raw',
            'value' => 'date("Y.m.d",$data->start_time) . "-<br>" . date("Y.m.d",$data->end_time-1)',
        ),
        array(
            'header' => '平均周消费(金额/单数)',
            'type' => 'raw',
            'value' => '$data->ave_cost."(".$data->ave_count.")"',
        ),
        array(
            'header' => '本周消费(金额/单数)',
            'type' => 'raw',
            'value' => '$data->week_order_price."(".$data->week_order_count.")"',
        ),
        array(
            'header' => '变化量(金额/单数)',
            'type' => 'raw',
            'value' => '"<input value=".$data->week_order_price." type=\"hidden\" id=\"week_order_price_".$row."\">".'
            . '"<input value=\"".$data->week_order_count."\" type=\"hidden\" id=\"week_order_count_".$row."\">".'
            . '"<span row=\"".$row."\" class=\"changed\" id=\"week_order_price_change_".$row."\"></span>"',
        ),
        array(
            'header' => '变化率(金额/单数)',
            'type' => 'raw',
            'value' => '"<span row=\"".$row."\" class=\"changed_ave\" id=\"week_order_price_change_ave_".$row."\"></span>"',
        ),
    ),
));
?>

<script>
    //计算变化量和变化率
    $().ready(function() {
        $.each($('.changed'), function(i, n) {
            $row = $(n).attr('row');
            $nextRow = parseInt($row) + 1;
            $price = $('#week_order_price_' + $row).val();
            $price = Number(parseInt($price));
            $count = $('#week_order_count_' + $row).val();
            $count = Number(parseInt($count));
            $nextPrice = $('#week_order_price_' + $nextRow).val();
            if ($nextPrice) {
                $nextCount = $('#week_order_count_' + $nextRow).val();
                $nextCount = Number(parseInt($nextCount));
                $nextPrice = Number(parseInt($nextPrice));
                $priceChanged = $price - $nextPrice;
                $countChanged = $count - $nextCount;
                $priceChangedAve = $priceChanged == 0 ? '0' : ($nextPrice == 0 ? '100' : $priceChanged / $nextPrice * 100);
                $countChangedAve = $countChanged == 0 ? '0' : ($nextCount == 0 ? '100' : $countChanged / $nextCount * 100);
                $changed = $priceChanged + '(' + $countChanged + ')';
                $pointIndex = String($priceChangedAve).indexOf('.');
                $lastIndex = $pointIndex == -1 ? String($priceChangedAve).length + 1 : $pointIndex + 3;
                $changedAve = String($priceChangedAve).slice(0, $lastIndex) + '%(' + String($countChangedAve).slice(0, $lastIndex) + '%)';
            } else {
                $changed = '--(--)';
                $changedAve = '--(--)';
            }
            $(n).text($changed);
            $('#week_order_price_change_ave_' + $row).text($changedAve);
        });
    });
</script>
