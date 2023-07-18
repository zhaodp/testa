<?php
$this->pageTitle = '发票管理';
?>
<h1><?php
echo $this->pageTitle;
?></h1>
<?php
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid', 
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	//	'summaryText'=>$dataProvider->itemCount,
	'rowCssClassExpression'=>array($this,'invoiceRow'),
	'columns'=>array (
		array (
                        'name'=>'单号',
                        'headerHtmlOptions'=>array (
                                'width'=>'60px',
                                'nowrap'=>'nowrap'
                        ),
                        'type'=>'raw',
                        'value'=>'CHtml::link($data["order_number"], array("order/view", "id"=>$data["order_id"]), array("target"=>"_blank"))'
                ),
                array (
                        'name'=>'发票信息',
                        'type'=>'raw',
                        'value'=>array($this,'invoiceContent')
                ),  
                array (
                        'name'=>'收件人地址',
                        'type'=>'raw',
                        'value'=>array($this,'invoiceContact')
                ),
                array(
                        'name'=>'日期',
                        'value'=>array($this,'invoiceCreated')
                ),
                array(
                        'name'=>'处理状态',
                        'value'=>'($data["status"] == 0) ? "未开发票" : "发票已开"'
                ) 	
	)
));
?>
