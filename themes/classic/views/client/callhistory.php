<?php
$this->pageTitle = '司机通话历史';

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('call-history-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo $this->pageTitle;?></h1>
<hr />
<div class="search-form span12">
	<?php $this->renderPartial('_search',array(
        'model'=>$model,
    ));
	?>
</div><!-- search-form -->
<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'call-history-grid',
	'itemsCssClass'=>'table table-striped',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'dataProvider'=>$dataProvider,
	'columns'=>array(
			array(
					'name'=>'司机姓名',
					'headerHtmlOptions'=>array (
							'width'=>'50px',
							'nowrap'=>'nowrap'
					),
					'value'=>array($this,'getDriverName')
			),
			array(
					'name'=>'司机工号',
					'headerHtmlOptions'=>array (
							'width'=>'50px',
							'nowrap'=>'nowrap'
					),
					'value'=>array($this,'getDriverUserId')
			),
			array(
					'name'=>'司机电话',
					'headerHtmlOptions'=>array (
							'width'=>'50px',
							'nowrap'=>'nowrap'
					),
					'value'=>array($this,'getDriverPhone')
			),
			array(
					'name'=>'呼入电话',
					'headerHtmlOptions'=>array (
							'width'=>'50px',
							'nowrap'=>'nowrap'
					),
					'value'=>'$data->type == 0 ? $data->phone : ""'
			),
		array(
			'name'=>'呼出电话',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'$data->type == 1 ?  $data->phone : ""'
		),
		array(
					'name'=>'未接电话',
					'headerHtmlOptions'=>array (
							'width'=>'50px',
							'nowrap'=>'nowrap'
					),
					'value'=>'$data->type == 2 ?  $data->phone : ""'
			),
		array(
			'name'=>'来电时间',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			), 
			'value'=>array($this,'getCallTime')
		),
		array(
			'name'=>'通话时间',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type' => 'raw',
			'value'=>'($data->duration)? (($data->duration>60)?date("i分s秒",$data->duration):date("s秒",$data->duration)):"未接"'
		),
		
	)
)); 
?>
