<?php
$this->pageTitle = '管理优惠券';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('bonus-area-static-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<h1><?php echo $this->pageTitle; ?></h1>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'bonus-area-static-grid',
	'dataProvider'=>$model->search(20),
	'itemsCssClass'=>'table table-striped',
// 	'filter'=>$model,
	'columns'=>array(
		array (
					'name'=>'id',
					'headerHtmlOptions'=>array (
							'width'=>'20px',
							'nowrap'=>'nowrap'
					),
			),
			array (
					'name'=>'bonus_type_id',
					'headerHtmlOptions'=>array (
							'width'=>'60px',
							'nowrap'=>'nowrap'
					),
					'value'=>'Yii::app()->controller->getBonusByType($data->bonus_type_id)',
			),
			array (
					'name'=>'bonus_sn',
					'headerHtmlOptions'=>array (
							'width'=>'60px',
							'nowrap'=>'nowrap'
					),
			),
			array (
					'name'=>'operator',
					'headerHtmlOptions'=>array (
							'width'=>'60px',
							'nowrap'=>'nowrap'
					),
			),
		array (
					'name'=>'created',
					'headerHtmlOptions'=>array (
							'width'=>'60px',
							'nowrap'=>'nowrap'
					),
					'value'=>'date("Y-m-d H:i:s",$data->created)'
			),
	),
)); ?>
<script>
$(function(){

	$("#down_excel_btn").click(function(){
		if($("#BonusAreaStatic_bonus_type_id").val()==''){
				alert("请先选择优惠码类型");
				return false;
		}
		bonus_type_id = $("#BonusAreaStatic_bonus_type_id").val();
		bonus_sn = $("#BonusAreaStatic_bonus_sn").val();
		//新页面打开开始下载
		url = '<?php echo Yii::app()->createUrl('/bonusAreaStatic/download')?>&bonus_type_id='+bonus_type_id+'&bonus_sn='+bonus_sn;
		window.open(url);
	});
	
});
</script>