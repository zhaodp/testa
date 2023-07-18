<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-9
 * Time: 下午10:29
 * auther mengtianxue
 */
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#customer-trade-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>
<?php $this->renderPartial('user/user_nav'); ?>

<div class="row-fluid">
    <?php $this->renderPartial('user/_search_trade',array(
        'model'=>$model,
    )); ?>
</div><!-- search-form -->


<div class="row-fluid">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'customer-trade-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '交易订单号',
                'value' => 'empty($data->trans_order_id) ? "" : $data->trans_order_id'
            ),

            array(
                'name' => '交易卡号',
                'value' => 'empty($data->trans_card) ? "" : $data->trans_card'
            ),

            array(
                'name' => '交易类型',
                'value' => 'CarCustomerTrans::$trans_type[$data->trans_type]'
            ),

            array(
                'name' => '交易金额',
                'value' => '$data->amount'
            ),

            array(
                'name' => '当前余额',
                'value' => '$data->balance'
            ),

            array(
                'name' => '交易类型',
                'value' => 'CarCustomerTrans::$trans_source[$data->source]'
            ),

            array(
                'name' => '创建时间',
                'value' => '$data->create_time'
            ),

            array(
                'name' => '备注',
                'value' => '$data->remark'
            ),
        ),
    )); ?>
</div>