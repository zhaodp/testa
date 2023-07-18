<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('#search-form').submit(function(){
	$('#support-ticket-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");

?>


<h3>客户端市场活动数据</h3>
<div class="search-form" style="display:block">
 <?php $this->renderPartial('_search',array('model'=>$model,'param'=>$param,)); ?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'tc-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
	    array(
                'name' => '活动标题',
                'value' =>'$data->title'
            ),
            array(
                'name' => '活动状态',
                'value' => array($this,'getActivityStatus')
            ),
	    array(
                'name' => '总点击量',
                'value' => '0'
            ),
	   array(
                'name' => '点击量详情',
                'value' => ''
            ),
	    array(
                'name' => '开始时间',
                'value' =>'$data->begintime'
            ),
		array(
                'name' => '结束时间',
                'value' =>'$data->endtime'
            ),
		array(
		'name' => '适用地区',
                'headerHtmlOptions'=>array (
                                'width'=>'80px',
                                'nowrap'=>'nowrap'
                 ),
                'type'=>'raw',
                'value'=>array($this,'getCityName')
            ),
		array(
                'name' => '新老客户限制',
                'value' =>'MarketingActivity::$customers[$data->customer]'
            ),
		array(
                'name' => '适用平台',
                'value' =>'MarketingActivity::$platforms[$data->platform]'
            ),
		array(
                'name' => '页面预览',
		'type'=>'raw',
                'value'=>array($this,'getUrl')
            ),
        ),
    )); ?>
