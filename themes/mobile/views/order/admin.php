<?php
$this->pageTitle = '订单管理';

$yesterday = date('Y-m-d', time() - 24 * 3600) . ' 09:00';
$today = date('Y-m-d H:i', strtotime($yesterday) + 24 * 3600); 

?>

<h3><?php echo $this->pageTitle;?></h3>


<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div>
<?php
$criteria = new CDbCriteria(array(
	'order'=>'booking_time desc',
));

$dataProvider = $model->search($criteria);
$this->widget('zii.widgets.CListView', array (
	'id'=>'order-grid', 
	'dataProvider'=>$dataProvider, 
	'itemsCssClass'=>'table table-stripe',
	'itemView'=>'_order_admin'
));
?>