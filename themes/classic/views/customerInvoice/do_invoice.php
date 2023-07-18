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
<?php $this->renderPartial('_do_invoice_info',array('model'=>$model,)); ?>
</div>
<form name='trans_form' id='trans_form' action='<?php echo Yii::app()->createUrl("/customerInvoice/invoice")    ?>' method='post'>
<input type='hidden' name='from' id='from' value='1'/>
<!--<input type='button' name='totalAmount' id='totalAmount' value='<?php echo $totalAmount ?>元'/>-->
<input type='text' name='totalAmount' id='totalAmount' value='<?php echo 0 ?>' readonly='true'/>元
<input type='hidden' name='totalAmount2' id='totalAmount2' value='<?php echo $totalAmount ?>'/>
<input type='hidden' name='trans_id' id='trans_id' value=''/>
<?php echo CHtml::button('全选',array('class'=>'btn btn-success', 'onclick'=>'jQuery(":checkbox").attr("checked", true);jQuery("#totalAmount").val(jQuery("#totalAmount2").val())')); ?>
<input type='hidden' name='invoiceId' id='invoiceId' value='<?php echo $_GET['id'] ?>' readonly='true'/>
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
                 'value'=>array($this,'admin_invoiceAmountInfo')
             ),
        ),
    )); ?>
</form>
<script>
jQuery(document).ready(function() {
	jQuery('[func="updateAmount"]').live('click', function(){
		var id = jQuery(this).attr("id");
		var d=jQuery(this).parent().parent().children().last().html();
		var old=jQuery("#totalAmount").val();
		var fd=Number(d);
		if(!isNaN(fd)){
			var fold=Number(old);
			if(jQuery(this).attr("checked")){
				jQuery("#totalAmount").val(fold+fd);
			}else{
				jQuery("#totalAmount").val(fold-fd);
			}
		}
	});

})

function invoice(){
	if (!confirm('确认开票？')) {
            return false;
        }
	jQuery("#trans_id").val(searchSelectedCheckBox());
	document.trans_form.submit();
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

</script>

