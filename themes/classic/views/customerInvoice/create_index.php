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
<?php 
if(isset($model)){
	$this->renderPartial('_create_invoice_info',array('model'=>$model, 'wealth'=>$wealth)); 
}else{
	$this->renderPartial('_create_invoice_info'); 
}
?>
</div>
<?php if(isset($_GET['flag'])){ ?>
<form name='trans_form' id='trans_form' action='<?php echo Yii::app()->createUrl("/customerInvoice/invoice")    ?>' method='post'>
<input type='text' name='totalAmount' id='totalAmount' value='<?php echo 0 ?>' readonly='true'/>元
<input type='hidden' name='invoiceId' id='invoiceId' readonly='true'/>

<input type='hidden' name='totalAmount2' id='totalAmount2' value='<?php echo $totalAmount ?>'/>
<input type='hidden' name='trans_id' id='trans_id' value=''/>
<?php echo CHtml::button('全选',array('class'=>'btn btn-success', 'onclick'=>'jQuery(":checkbox").attr("checked", true);jQuery("#totalAmount").val(jQuery("#totalAmount2").val())')); ?>


<!-- search-form -->

    <?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'do-invoice-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
	'htmlOptions'=>array('style'=>'width:98%'),
        'columns' => array(

            array(
		'name'=>'<input type="checkbox"/>',
                'type' => 'raw',
                'value' =>'CHtml::checkBox("cb_".$data["id"]."_".$data["table_name"]."_".$data["booking_year"],false, array("class"=>"cb", "func"=>"updateAmount"))'
            ),

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
</form>
<?php } ?>
<script>
function getTransList(){
	var customer_phone=$("#customer_phone").val();
	if(customer_phone==''){
		alert('请输入手机号查询');
		return;
	}
	window.location.href='index.php?r=customerInvoice/createIndex&customer_phone='+customer_phone+'&flag=1'
	/** $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/customerInvoice/createIndex');?>',
            'data':{customer_phone:customer_phone},
            'type':'get',
            'success':function(data){
                //$.fn.yiiGridView.update('order-grid');
               // window.location.reload();
		alert('111');
            },
            'cache':false
        });
	document.trans_form.submit();**/
}
function saveInvoice(){
	var customer_phone=$("#customer_phone").val();
	var title=$("#title").val();
	var contact=$("#contact").val();
	var telephone=$("#telephone").val();
	var address=$("#address").val();
	var type=$("#type").val();
	var pay_type = $("#pay_type").val();
	var remark = $("#remark").val();
	var client_amount = $("#client_amount").val();
	if(customer_phone==''){alert('请输入客户电话');return;}
	if(title==''){alert('请输入抬头');return;}
	if(contact==''){alert('请输入收件人');return;}
	if(telephone==''){alert('请输入收件人电话');return;}
	if(address==''){alert('请输入地址');return;}
	if(client_amount==''){alert('请输入客户开票总金额');return;}
	if(client_amount< 19){
	    alert('最小申请金额不能小于19元');return;
	}
	var max_amount = $("#totalAmount2").val();
	if(parseInt(client_amount)>parseInt(max_amount)){
	    alert('最大可开票金额为'+max_amount+'元');
	    return;
	}	
	if($("td[class='empty']").length>0){
	    alert('该客户没有订单或交易记录');return;
	}
	if(searchSelectedCheckBox()==''){
	    alert('请选择开票数据');return;
	}
	var select_value = $("#totalAmount").val();
	if(parseInt(select_value)<parseInt(client_amount)){
	    alert('勾选金额不能小于申请金额');return;
	}	

	var pay_type = $("#pay_type").val();
	if(pay_type == 4){//满500免邮
	    var amount = $("#totalAmount").val();
	    if(amount<500){
		alert('开票金额不足500元,请选择其他支付方式');
		return;
	    }
	}
	<?php if(isset($_GET['flag'])){ ?>
	if(pay_type == 1){
	    if(<?php echo $wealth; ?> < 500){
		alert('e币不足500,请选择其他支付方式');
                return;
	    }
	}
	<?php } ?>

	if(!confirm('确认提交?')){
	    return;
	}
	$.ajax({
            'url':'<?php echo Yii::app()->createUrl('/customerInvoice/saveInvoice');?>',
            'data':{customer_phone:customer_phone,title:title,contact:contact,telephone:telephone,address:address,type:type,pay_type:pay_type,remark:remark,client_amount:client_amount},
            'type':'get',
            'success':function(data){
		if(data == '' || data == '0'){
                    alert('开票失败,请重新开票');
                    return;
                }
		$("#invoiceId").val(data);
		jQuery("#trans_id").val(searchSelectedCheckBox());
		document.trans_form.submit();
            },
            'cache':false
        });
}

function searchSelectedCheckBox() {
            var id_list = '';
            jQuery(".cb").each(function() {
                if (jQuery(this).attr('checked') == 'checked') {
                    var id = jQuery(this).attr('id').replace('cb_','');
                    if (id) {
                        id_list=id_list+id+',';
                    }
                }
            });
            return id_list;
        }

jQuery(document).ready(function() {
        jQuery('[func="updateAmount"]').live('click', function(){
                var id = jQuery(this).attr("id");
                var d=jQuery(this).parent().parent().children().last().html();
                var old=jQuery("#totalAmount").val();
                var fd=Number(d);
                if(!isNaN(fd)){
                        var fold=Number(old);
                        if(jQuery(this).attr("checked")){
                                //jQuery("#totalAmount").val(jQuery("#totalAmount").val()+d);
                                jQuery("#totalAmount").val(fold+fd);
                        }else{
                                jQuery("#totalAmount").val(fold-fd);
                        }
                }
        });

})

</script>

