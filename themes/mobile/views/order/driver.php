<?php
$this->pageTitle = '订单管理';
?>
<h3><?php echo $this->pageTitle;?></h3>
<style>
.alert-ready {
    background-color: #efefef;
    border-color: #000000;
    color: #000000;
}
</style>
<?php
$this->widget('zii.widgets.CListView', array (
	'id'=>'well', 
	'dataProvider'=>$dataProvider, 
//	'summaryText'=>$dataProvider->itemCount,
//	'rowCssClassExpression'=>array($this,'orderStatus'),
	'itemsCssClass'=>'table table-stripe',
	'itemView'=>'_order'
));
?>

<div>*列表中没有的订单请点击<a href="<?php echo Yii::app()->createUrl('/order/create'); ?>">订单补录</a></div>
