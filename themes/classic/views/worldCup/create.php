<?php
$this->pageTitle='比赛设置';
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#customer-trans-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>
<div class="row-fluid">
    <?php $this->renderPartial('_add_form'); ?>
</div><!-- search-form -->

<div class="row-fluid">
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'worldcup-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
            array(
                'name' => '国家一',
                'value' => 'WorldCup::$country[$data->country_1]'
            ),
            array(
                'name' => '国家二',
                'value' => 'WorldCup::$country[$data->country_2]'
            ),
            array(
                'name' => '时间',
                'value' => '$data->begin_time'
            ),
            array(
                  'class'=>'CButtonColumn',
		  'header' => '操作',   
    		  'template'=>'{delete}',    
             ),
	  
      
        ),
    )); ?>
</div>
