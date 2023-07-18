<?php
/* @var $this CityConfigController */
/* @var $model CityConfig */
$this->setPageTitle('总部e币和皇冠管理');


?>
<?php

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'crowndialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'增加城市配额',
        'autoOpen'=>false,
        'width'=>'800',
        'height'=>'600',
        'modal'=>true,
        'buttons'=>array(
            'Close'=>'js:function(){$("#crowndialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="view_crown_frame" width="80%" height="80%" style="border:0px"></iframe>';

$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php

// echo '<div id="dialogdiv"></div>';
// echo '<iframe id="view_message_frame" width="100%" height="100%" style="border:0px"></iframe>';

// // $this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h1><?php echo $this->pageTitle; ?></h1>
<div>本月运营总配额：<?php echo $all_emoney; ?>e <span style='padding:10px'>总部总配额：<?php echo number_format(DriverWealthLog::ALL_EMONEY_MANAGER); ?>e</span></div>
</br>
<div>本月总部运营总余额：<?php echo $left_emoney; ?>e 
    <span style='padding:10px'>补贴分公司：<?php echo $reward_emoney; ?>e</span>
    <span style='padding:10px'>恶劣天气补贴司机：<?php echo $weather_reward; ?>e</span></div>

<a href='<?php echo Yii::app()->createUrl('/driver/weather');?>' target='_blank'>发放恶劣天气奖励</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'score-driver-grid',
    'cssFile' => SP_URL_CSS . 'table.css',
    'dataProvider' => $dataProvider,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
//	'filter'=>$model,
    'columns' => array(

        array (
            'name'=>'城市ID',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->city_id',
        ),
        array('name'=>'城市名称','value'=>array($this,'getCityName')),
        array (
            'name'=>'级别',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->city_level',
        ),
        array('name'=>'剩余配额','value'=>array($this,'getLeftCrown')),
        array(
            'name'=>'操作',
            'headerHtmlOptions'=>array(
                //'width'=>'80px',
                'nowrap'=>'nowrap'),
            'type'=>'raw',
            'value'=>array($this,'getCrownDeal'),
        ),

    ),
)); ?>


<script>

    function updateCrown(city_id){
        url = '<?php echo Yii::app()->createUrl('/driver/crownUpdate');?>&city_id='+city_id;
        $("#view_crown_frame").attr("src",url);
        $("#crowndialog").dialog("open");
    }
</script>