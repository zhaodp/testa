<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/4/22
 * Time: 下午3:51
 */
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

<?php
$this->pageTitle = '物流订单导出记录';
echo "<h1>".$this->pageTitle."</h1><br />";
?>
<div>
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'score-driver-grid',
        'cssFile' => SP_URL_CSS . 'table.css',
        'dataProvider' => $dataProvider,
        'ajaxUpdate' => false,
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'itemsCssClass'=>'table table-striped',
        'columns' => array(

            array (
                'name'=>'时间',
                'headerHtmlOptions'=>array (
                    'style'=>'width:30px',
                    'nowrap'=>'nowrap'
                ), 'type'=>'raw',
                'value'=>'$data->order_time',
            ),
            array (
                'name'=>'导出订单数量',
                'headerHtmlOptions'=>array (
                    'style'=>'width:30px',
                    'nowrap'=>'nowrap'
                ), 'type'=>'raw',
                'value'=>'$data->total',
            ),
            array (
                'name'=>'操作',
                'headerHtmlOptions'=>array (
                    'style'=>'width:30px',
                    'nowrap'=>'nowrap'
                ), 'type'=>'raw',
                'value'=>array($this,'opt_record')
            )
        ),
    )); ?>
</div>


