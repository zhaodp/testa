<?php
$this->pageTitle='';
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#customer-trans-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>
<h1><?php echo $this->pageTitle;?></h1>
<hr class="divider"/>
<div class="search-form">
<?php $this->renderPartial('_view_invoice_info',array('model'=>$model,)); ?>
</div>
<input type='text' name='totalAmount' id='totalAmount' value='<?php echo $model->total_amount ?>' readonly='true'/>元
<!-- search-form -->

    <?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'do-invoice-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
	'htmlOptions'=>array('style'=>'width:98%'),
        'columns' => array(
            array(
                'name' => '时间',
                'value' => '$data["table_name"]==1?$data["create_time"]:date("Y-m-d H:i:s",$data["create_time"])'
            ),
            array(
                'name' => '订单号',
                'value' =>'$data["order_id"]'
            ),
            array(
                'name' => '司机信息',
		'type'=>'raw',
		'value'=> array($this,'admin_invoiceDriver')
	     ),
            array(
                'name' => '详情',
		'type'=>'raw',
                'value' => array($this,'admin_orderDetail')
            ),
	   array(
		 'name' => '收费',
		 'type' => 'raw',
                 'value'=> array($this,'admin_orderInfo')
		),
	    array(
                 'name' => '可开票金额',
		 'type' => 'raw',
                 'value'=> array($this,'admin_invoiceAmountInfo')
                ),
        ),
    )); ?>
