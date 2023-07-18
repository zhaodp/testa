<?php
$this->pageTitle = '司机账单查询';
?>

<h1><?php echo $this->pageTitle; ?></h1>

<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});

");
?>

<div class="search-form">
    <?php
    $this->renderPartial('_search_recharge', array(
        'model' => $model));
    ?>
</div>


<h4>总计：<?php echo $total->cast; ?> 元</h4>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'recharge-grid',
    'dataProvider' => $model->search(),
    'cssFile' => SP_URL_CSS . 'table.css',
    'itemsCssClass' => 'table table-condensed',
    'htmlOptions' => array('class' => 'row-fault'),
    'columns' => array(
        array(
            'name' => 'user',
            'headerHtmlOptions' => array(
                'width' => '20px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->user'
        ),
        array(
            'name' => 'cast',
            'headerHtmlOptions' => array(
                'width' => '25px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->cast'
        ),
        array(
            'name' => 'balance',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '($data->type == 0) ? "---------" : $data->balance'
        ),
        array(
            'name' => 'comment',
            'headerHtmlOptions' => array(
                'width' => '200px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->comment'
        ),
        array(
            'name' => 'created',
            'headerHtmlOptions' => array(
                'width' => '50px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'date("Y-m-d H:i", $data->created)'
        ),

    ),
)); ?>

