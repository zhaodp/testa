<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
    var title = $(this).text() == '收起搜索' ? '展开搜索' : '收起搜索';
    $(this).text(title);
	return false;
});
$('.search-form form').submit(function(){
	$('#bonus-code-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>微信关注列表</h1>

<?php echo CHtml::link('展开搜索', '#', array('class' => 'btn search-button')); ?>

<div class="search-form" style="display:none">
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
        'dateStart'=>$dateStart,
        'dateEnd'=>$dateEnd
    ));
    ?>
</div><!-- search-form -->

<?php
$gridId = 'bonus-code-grid';
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => $gridId,
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
//	'filter'=>$model,
    'columns' => array(
        'day',
        array(
            'name' => 'subscribe_type',
            'header' => '总关注数',
            'type' => 'raw',
            'value' => '$data->subscribe_type',
        ),
        array(
            'name' => 'event_key',
            'header' => '通过推广渠道关注数',
            'type' => 'raw',
            'value' => '$data->event_key',
        ),
    ),
));

?>
