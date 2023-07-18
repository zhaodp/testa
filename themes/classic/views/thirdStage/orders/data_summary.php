<style>
    label{display:inline-block}
</style>

<?php
$this->pageTitle = '订单数据查看 ' . $dateStart . ' -- ' . $dateEnd;
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#customer-main-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

<div >

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => "index.php?r=thirdStage/ViewOrderSummary",
        'method' => 'get',
    )); ?>
    <?php
    $dateList = array(
        'yesterday' => '昨日',
        'last_week' => '前一周',
        'last_month' => '前一月',
    );
    $format = '<input type="radio" name="type" id="inlineRadio1" value="%s" %s> %s';
    foreach($dateList as $k => $v){
        echo '<label class="radio-inline">';
        $tmpChecked = '';
        if($k ==  $checked){
            $tmpChecked = 'checked';
        }
        echo sprintf($format, $k, $tmpChecked, $v);
        echo '</label>';
    }
    $tmpFormat = '渠道号:<input type="text" name="channel" value="%s"/>';
    echo sprintf($tmpFormat, $channel);
    ?>



        <?php echo CHtml::submitButton('查询', array('class' => 'btn')); ?>

    <?php $this->endWidget(); ?>
</div>

<div class="row-fluid">
    <?php
    $this->widget('zii.widgets.grid.CGridView',
        array(
            'id' => 'customer-main-grid',
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
                array(
                    'name' => '渠道名',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => array($this, 'channel'),
                ),
                array(
                    'name' => '订单数',
                    'type' => 'raw',
                    'value' => '$data["orderCount"]',
                ),
                array(
                    'name' => '用户数',
                    'type' => 'raw',
                    'value' => '$data["customerCount"]',
                ),
                array(
                    'name' => '新客数',
                    'type' => 'raw',
                    'value' => '$data["inviteCount"]',

                ),
                array(
                    'name' => '订单环比增长',
                    'type' => 'raw',
                    'value' => '$data["order_rate"]."%"',
                ),

            ),
        )
    ); ?>
</div>

