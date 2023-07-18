<?php
$this->pageTitle = '管理充值卡';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('vip-card-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo $this->pageTitle; ?></h1>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php 
$this->renderPartial('_search_vip_card',array(
	'model'=>$model,
)); 
?>
</div><!-- search-form -->
<?php 
$total = VipCard::model()->getSaledTotal();
foreach ($total as $data){
	echo '<br />' . $data['money'] . '元，' . $data['status'] . '， 共' . $data['count'] . '张<br />';
}
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vip-card-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'itemsCssClass'=>'table table-condensed',
	'rowCssClassExpression'=>array($this,'cardStatusCss'),
	'htmlOptions'=>array('class'=>'row span11'),
	'columns'=>array(
		array (
			'name'=>'id', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
		), 
		array (
			'name'=>'money', 
			'headerHtmlOptions'=>array (
				'width'=>'150px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'status', 
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'cardStatus')
		), 
		array (
			'name'=>'saled_by', 
			'headerHtmlOptions'=>array (
				'width'=>'100px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'activated_by', 
			'headerHtmlOptions'=>array (
				'width'=>'100px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'atime', 
			'headerHtmlOptions'=>array (
				'width'=>'100px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'($data->atime == 0) ? "" : date("m-d H:i",$data->atime)'
		),
		array (
			'name'=>'激活时间', 
			'headerHtmlOptions'=>array (
				'width'=>'100px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'activatedTime')
		),
		array (
			'name'=>'操作', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'operateVipCard')
		),
	),
)); ?>
<script>
function dialogMarkSaled(id, status){
	if (status == <?php echo VipCard::STATUS_SALED;?>){
		if(!confirm('确认该卡已售出？')) return false;
	} 
	if (status == <?php echo VipCard::STATUS_CREATED;?>){
		if(!confirm('确认该卡未售出？')) return false;
	} 
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/vip/docardmark');?>',
		'data':{'id':id, 'status':status},
		'type':'get',
		'success':function(data){
			$.fn.yiiGridView.update('vip-card-grid');
		},
		'cache':false		
	});
}
</script>