<?php
$this->pageTitle = $month.'月对账单明细';

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_driver_dialog',
	'options'=>array (
		'title'=>'查看信息',
		'autoOpen'=>false,
		'width'=>'820',
		'height'=>'500',
		'modal'=>true,
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_driver_frame").attr("src","");$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h1><?php echo $this->pageTitle;?></h1>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table historyAccount">

    <tr class="alert alert-success">
        <td>您当前信息费余额为：
            <?php
            if ($month == date('Y-m')){
                echo $balance;
            }else{
                echo $settle['total'];
            }
            ?>元</td>
        <td colspan=3></td>
    </tr>

	<tr class="alert alert-info">
		<td>信息费充值：<?php echo (isset($settle['t5']) && $settle['t5'] != '0')?CHtml::link($settle["t5"], Yii::app()->createUrl('account/driverhistorydetail',array('month' => $month,'type' => 5))):'0.00'?>元</td>
		<td>现金收入：<?php echo (isset($settle['t0']) && $settle['t0'] != '0')?CHtml::link($settle["t0"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 0))):'0.00'?>元</td>
		<td>VIP订单：<?php echo (isset($settle['t3']) && $settle['t3'] != '0')?CHtml::link($settle["t3"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 3))):'0.00'?>元</td>
		<td></td>
	</tr>
	<tr class="alert alert-info">
		<td>抵扣转账：<?php echo (isset($settle['t7']) && $settle['t7'] != '0')?CHtml::link($settle["t7"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 7))):'0.00'?>元</td>
		<td>优惠券返现：<?php echo (isset($settle['t8']) && $settle['t8'] != '0')?CHtml::link($settle["t8"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 8))):'0.00'?>元</td>
		<td>司机发卡返现：<?php echo (isset($settle['t9']) && $settle['t9'] != '0')?CHtml::link($settle["t9"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 9))):'0.00'?>元</td>
		<td>优惠券补偿：<?php echo (isset($settle['t10']) && $settle['t10'] != '0')?CHtml::link($settle["t10"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 10))):'0.00'?>元</td>
	</tr>
	<tr class="alert alert-error">
		<td>信息费：<?php echo (isset($settle['t1']) && $settle['t1'] != '0')?CHtml::link($settle["t1"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 1))):'0.00'?>元</td>
		<td>发票扣税：<?php echo (isset($settle['t2']) && $settle['t2'] != '0')?CHtml::link($settle["t2"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 2))):'0.00'?>元</td>
		<td>罚金扣费：<?php echo (isset($settle['t4']) && $settle['t4'] != '0')?CHtml::link($settle["t4"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 4))):'0.00'?>元</td>
		<td></td>
	</tr>
	<tr class="alert alert-error">
		<td>保险费：<?php echo (isset($settle['t6']) && $settle['t6'] != '0')?CHtml::link($settle["t6"], Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month, 'type' => 6))):'0.00'?>元</td>
		<td colspan=3></td>
	</tr>
	<tr>
		<td>
            <?php echo CHtml::link('返回对账单汇总', array("account/driverhistory"));?>
        </td>
        <td>
            <?php echo CHtml::link('全部账单', Yii::app()->createUrl('account/driverhistorydetail', array('month' => $month))) ?>
        </td>
		<td colspan=3></td>
	</tr>
</table>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'silver_table',
	'itemsCssClass'=>'table historyAccount',
	'dataProvider'=>$dataProvider,
    'ajaxUpdate' => false,
	'columns'=>array(
        array(
            'name' => '流水号',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->order_id',
        ),
        array(
            'name' => '类型',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '($data->type == 5 &&  $data->order_id != 0) ? "订单重结返现" : Dict::item("account_type",$data->type)',
        ),
        array(
            'name' => '充值/扣款',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->cast',
        ),
        array(
            'name' => '信息费余额',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap',
            ),
            'type' => 'raw',
            'value' => '($data->type != 0) ? $data->balance : "--"',
        ),
        array(
            'name' => '备注',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->comment',
        ),
        array(
            'name' => '结账时间',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'date("Y-m-d H:i:s", $data->created)'
        ),

	)

));
?>
<script type="text/javascript">
    //司机账单列表按单分颜色
    jQuery(function ($) {
        var lenth = $("#silver_table tbody tr").length;
        var order_id = $("#silver_table tbody tr").eq(0).find("td").first().html();
        var num = 0;
        for(var i = 0; i< lenth; i++){
            var id = $("#silver_table tbody tr").eq(i).find("td").first().html();
            if(order_id != id){
                num ++;
                order_id = id;
            }
            if(num % 2 != 0){
               var css = '#f1f1f1';
            }else{
               var css = '#ffffff';
            }
            $("#silver_table tbody tr").eq(i).css('background', css);
        }
    });
</script>