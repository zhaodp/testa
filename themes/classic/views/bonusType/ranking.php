<?php
$this->pageTitle = '司机联盟-发卡赚钱排行榜';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-bonus-list-grid', {
		data: $(this).serialize()
	});
	
	
});
");

?>
<h1><?php echo $this->pageTitle; ?></h1>

<?php

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_driver_dialog', 
	'options'=>array (
		'title'=>'查看司机信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

if(Yii::app()->user->city==0)
{	

$dataProviderCity = new CArrayDataProvider($dataCity, array('id'=>'driver-bonus-city-list-data', 
		'keyField'=>'city_id'));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-bonus-city-list-grid',
	'itemsCssClass'=>'table table-striped',
	'dataProvider'=>$dataProviderCity,
	'columns'=>array(
		array (
			'name'=>'城市', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'Dict::item("city", $data["city_id"]);'
		), 
		array (
			'name'=>'绑定总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'$data["bind_count_sum"]'
		),
		array (
			'name'=>'消费总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'$data["used_count_sum"]'
		),
		array (
			'name'=>'收入总金额', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'$data["bonus_sum"]'
		),				
	)
));

?>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button')); ?>
<div class="search-form">
<?php $this->renderPartial('_search_ranking_list',array(
	'model'=>$model,
	'data'=>$data,
)); 
}
?>
</div><!-- search-form -->

<?php

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-bonus-list-grid',
	'itemsCssClass'=>'table table-striped',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name'=>'排名',
					'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
					),		
			'value'=>'$row+1+'.($page-1)*$dataProvider->getPagination()->pageSize
        ),
		array (
			'name'=>'城市', 
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'Dict::item("city", $data["city_id"]);'
		),        		
		array (
			'name'=>'司机', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'$data["name"]'
		), 
		array (
			'name'=>'工号', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>array($this, 'adminDriverInfo')
		),
		array (
			'name'=>'优惠卡号', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'$data["bonus_code"]'
		), 		 		
 		array (
			'name'=>'绑定总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'$data["bind_count"]'
		),
		array (
			'name'=>'消费总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'$data["used_count"]'
		),
		array (
			'name'=>'呼叫中心补单', 
			'headerHtmlOptions'=>array (
				'width'=>'10px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>array($this, 'getDriverBonusUseCountByHandCallCenter')
		),
		array (
			'name'=>'客户呼叫补单', 
			'headerHtmlOptions'=>array (
				'width'=>'10px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>array($this, 'getDriverBonusUseCountByHandClient')
		),
		array (
			'name'=>'异常呼入', 
			'headerHtmlOptions'=>array (
				'width'=>'10px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>array($this, 'getDriverBonusUseCountCallIn')
		),
		array (
			'name'=>'异常呼出', 
			'headerHtmlOptions'=>array (
				'width'=>'10px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>array($this, 'getDriverBonusUseCountCallOut')
		),
		array (
			'name'=>'收入总金额', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'$data["bonus"]'
		),		
		array(
			'name'=>'创建时间',
			'headerHtmlOptions'=>array(
				'width'=>'50px',
				'nowrap'=>'nowrap',
			),
			'type'=>'raw',
			'value'=>'substr($data["created"], 0,10)',
		),	
	)
));

?>
<script type="text/javascript">
<!--
function driverDialogdivInit(src){
	$("#view_driver_frame").attr("src",src);
	$("#view_driver_dialog").dialog("open");
	return false;
}
//-->
</script>
