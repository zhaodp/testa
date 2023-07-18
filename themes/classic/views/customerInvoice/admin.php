<?php
$this->pageTitle='发票申请';
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
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'customer_invoice_dialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '发票申请信息',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#customer_invoice_dialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<div id="customer_invoice_dialog_div"></div>';
echo '<iframe id="cru-frame-customer-invoice" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'remark_dialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '备注信息',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#remark_dialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<div id="remark_dialog_div"></div>';
echo '<iframe id="cru-frame-remark" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'finance_dialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '快递单\发票号',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#finance_dialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<div id="finance_dialog_div"></div>';
echo '<iframe id="cru-frame-finance" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
        'id'=>'import_invoice_dialog',
        // additional javascript options for the dialog plugin
        'options'=>array (
            'title'=>'导入信息',
            'autoOpen'=>false,
            'width'=>'480',
            'height'=>'380',
            'modal'=>true,
            'buttons'=>array (
                    '关闭'=>'js:function(){$("#import_invoice_dialog").dialog("close");}'))));
echo '<div id="import_invoice_dialog"></div>';
echo '<iframe id="import_invoice_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
    'id'=>'import_delivery_dialog',
    // additional javascript options for the dialog plugin
    'options'=>array (
        'title'=>'导入信息',
        'autoOpen'=>false,
        'width'=>'480',
        'height'=>'380',
        'modal'=>true,
        'buttons'=>array (
            '关闭'=>'js:function(){$("#import_delivery_dialog").dialog("close");}'))));
echo '<div id="import_delivery_dialog"></div>';
echo '<iframe id="import_delivery_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h1><?php echo $this->pageTitle;?></h1>
<h4>累计发票申请数量:<font color="red"><?php echo $count['total'];?></font>人，已处理：<font color="red"><?php echo $count['dealed'];?></font>,未处理：<font color="red"><?php echo $count['nodeal'];?></font>,<font color="red"> 总开票金额： <?php echo $count['invoiceTotalAmount'];?>元</font></h4>
<h4>昨日400新增发票申请数:<?php echo $apply_data['web'];?>，昨日APP新增发票申请数:<?php echo $apply_data['app'];?>，昨日vip申请生成发票数:<?php echo $apply_data['vip'];?>，昨日客服已确认数:<?php echo $apply_data['confirm'];?>，昨日财务已开票数:<?php echo $apply_data['finance_confirm'];?>，当前客服待确认数:<?php echo $wait_data['not_confirm']-$vip_data;?>，当前财务待开票数:<?php echo $wait_data['finance_not_confirm'];?>，总取消数:<?php echo $wait_data['cancel'];?> </h4>
<div class="search-form">
<?php $this->renderPartial('_search',array('model'=>$model,));?>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'customer-trans-grid',
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
                array(
                  'class' => 'CCheckBoxColumn',
                  'selectableRows' => 2,
                  'value' => '$data->id',
                 ),
                array(
                    'name' => '时间',
                    'headerHtmlOptions'=>array (
                        'style'=>'width:100px',
                        'nowrap'=>'nowrap'
                    ),
                    'value' => 'date("Y-m-d",$data->updatetime)'
                ),
                array(
                    'name' => '客户电话',
                    'value' =>'$data->customer_phone'
                ),
                array(
                    'name' => '发票信息',
                    'value' => '$data->title'
                ),
                array(
                    'name' => '收件人信息',
                    'type'=>'raw',
                    'value' => array($this,'admin_invoiceContact'),
                ),
                array(
                    'name' => '其他',
                    'headerHtmlOptions'=>array (
                        'style'=>'width:100px',
                        'nowrap'=>'nowrap'
                    ),
                    'type'=>'raw',
                    'value' => array($this,'admin_invoiceType'),
                ),
               array(
                    'name' => '开票',
                    'value' => '$data->times==0?"首次开票":"非首次开票"'
                ),
                array(
                    'name' => '导出状态',
                    'value' => '$data->export==0?"未导出":"已导出"'
                ),
               array(
                    'name' => '申请金额',
                    'value' => '$data->client_amount'
               ),
               array(
                    'name' => '实开金额',
                    'value' => '$data->total_amount'
                ),
                array(
                    'name' => '发票号',
                    'value' => '$data->invoice_number'
                ),
               array(
                    'name' => '快递单号',
                    'value' => '$data->delivery_number'
                ),
               array(
                    'name' => '快递公司',
                    'value' => array($this,'admin_delivery')
                ),
               array(
                    'name' => '备注',
                    'value' => '$data->remark'
                ),

               array(
                    'name' => '操作',
                    'headerHtmlOptions'=>array (
                        'style'=>'width:90px',
                        'nowrap'=>'nowrap'
                    ),
                    'value' => array($this, 'showButton')
               ),
        ),
    )); ?>

<script type="text/javascript">
function customerInvoiceDialogdivInit(href) {
    $("#cru-frame-customer-invoice").attr("src", href);
    $("#customer_invoice_dialog").dialog("open");
    return false;
}

function remarkDialogdivInit(href) {
    $("#cru-frame-remark").attr("src", href);
    $("#remark_dialog").dialog("open");
    return false;
}

function financeDialogdivInit(href) {
    $("#cru-frame-finance").attr("src", href);
    $("#finance_dialog").dialog("open");
    return false;
}

function customer_confirm(id,state){
   if(confirm("确认提交？")){
       $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/customerInvoice/confirm');?>',
            'data':{id:id,state:state},
            'type':'get',
            'success':function(data){
                $.fn.yiiGridView.update('customer-trans-grid');
               //window.location.reload();
            },
            'cache':false
       });
   }
}
//财务确认
/**
function finance_confirm(id,phone){
   $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/customerInvoice/financeConfirm');?>',
            'data':{id:id,phone:phone},
            'type':'get',
            'success':function(data){
                $.fn.yiiGridView.update('customer-trans-grid');
               //window.location.reload();
            },
            'cache':false
   });
}**/
$(function() {
    $("#export_sheet").click(function () {
        var id_seclect = $("input[name='customer-trans-grid_c0[]']:checked");
        if (id_seclect.length <= 0) {
            alert("请选择需要导出的数据！");
            return false;
        }
        var id_str = '';
        for (i = 0; i < id_seclect.length; i++) {
            id_str += id_seclect.eq(i).val() + '_';
        }
        $.ajax({
            url: "<?php echo Yii::app()->createUrl("customerInvoice/check"); ?>",
            data: {id_str: id_str},
            cache: false,
            success: function (data) {
                if (data != '') {
                    alert(data);
                } else {
                    var iurl = "<?php echo Yii::app()->createUrl('customerInvoice/exportInvoice'); ?>" + '&id_str=' + id_str;
                    window.open(iurl);
                    var durl = "<?php echo Yii::app()->createUrl('customerInvoice/exportDelivery'); ?>" + '&id_str=' + id_str;
                    window.open(durl);
                }
            }
        });
    })

    $("#confirm_export").click(function () {
        if (!confirm('确认已经导出了快递单和发票单?')) {
            return;
        }
        var id_seclect = $("input[name='customer-trans-grid_c0[]']:checked");
        if (id_seclect.length <= 0) {
            alert("请选择刚才导出的数据！");
            return false;
        }
        var id_str = '';
        for (i = 0; i < id_seclect.length; i++) {
            id_str += id_seclect.eq(i).val() + '_';
        }
        $.ajax({
            url: "<?php echo Yii::app()->createUrl("customerInvoice/confirmExport"); ?>",
            data: {id_str: id_str},
            cache: false,
            success: function (data) {
                if (data > 0) {
                    alert('操作成功');
                    $.fn.yiiGridView.update('customer-trans-grid');
                } else {
                    alert('操作失败,请记录选中数据id及类型并联系技术');
                }
            }
        });
    })

    $("#import_invoice_btn").click(function () {
        var href = "<?php echo Yii::app()->createUrl('/customerInvoice/importInvoiceSheet'); ?>";
        $("#import_invoice_frame").attr("src", href);
        $("#import_invoice_dialog").dialog("open");
        return false;
    })

    $("#import_delivery_btn").click(function(){
        var href = "<?php echo Yii::app()->createUrl('/customerInvoice/importDeliverySheet'); ?>";
        $("#import_delivery_frame").attr("src",href);
        $("#import_delivery_dialog").dialog("open");
        return false;
    })
});
</script>                                      
