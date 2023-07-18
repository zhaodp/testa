<?php
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'orderqueue-grid', 
	'dataProvider'=>$dataProvider, 
	'cssFile'=>SP_URL_CSS.'table.css', 
	'itemsCssClass'=>'table table-condensed', 
	'columns'=>array (
		array (
			'name'=>'客户电话', 
			'headerHtmlOptions'=>array (
				'width'=>'30px', 
				'nowrap'=>'nowrap'),
            'type'=>'raw',
			'value'=>'"呼叫:".$data->phone."<br/>联系:".$data->contact_phone'
		),
		array (
			'name'=>'call_time', 
			'headerHtmlOptions'=>array (
				'width'=>'75px', 
				'nowrap'=>'nowrap'), 
			'value'=>'date("m-d H:i",$data->call_time)'), 
		
		array (
			'name'=>'booking_time', 
			'headerHtmlOptions'=>array (
				'width'=>'75px', 
				'nowrap'=>'nowrap'), 
			'value'=>'date("m-d H:i",$data->booking_time)'), 
		array (
			'name'=>'location_start', 
			'headerHtmlOptions'=>array (
				'width'=>'50px', 
				'nowrap'=>'nowrap')), 
		array (
			'name'=>'状态', 
			'headerHtmlOptions'=>array (
				'width'=>'5px', 
				'nowrap'=>'nowrap'),
			'value'=>'($data->status)==0?"未报":(($data->status)==1?"已报":"销单")'
				
		),
		array (
			'name'=>'操作', 
			'headerHtmlOptions'=>array (
				'width'=>'20px', 
				'nowrap'=>'nowrap'),
			'type'=>'raw',
			'value'=>array($this , 'getPushButton')),)));

?>
<script type="text/javascript">
function PushAgain(queue_id , driver_id) {
	if(confirm("您确定再次给司机推送订单详情么？")){
        $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/order/PushAgain');?>',
            'data':'queue_id='+queue_id+'&driver_id='+driver_id,
            'type':'get',
            'success':function(data){
                if(data == 1){
					alert("推送成功");
				}else{
					alert("推送失败");
				}
            },
            'cache':false
        });
    }
}
</script>