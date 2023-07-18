<?php
/* @var $this CityConfigController */
/* @var $model CityConfig */
$this->setPageTitle('司机扣分管理');

//fix bug 2301 aiguoxin 2014-06-25

// Yii::app()->clientScript->registerScript('search', "
// $('.search-button').click(function(){
// 	$('.search-form').toggle();
// 	return false;
// });
// $('.search-form form').submit(function(){
// 	$('#score-driver-grid').yiiGridView('update', {
// 		data: $(this).serialize()
// 	});
// 	return false;
// });
// ");

?>
<?php

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'scoredialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'修改司机代驾分',
        'autoOpen'=>false,
        'width'=>'800',
        'height'=>'600',
        'modal'=>true,
        'buttons'=>array(
            'Close'=>'js:function(){$("#scoredialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="view_score_frame" width="80%" height="80%" style="border:0px"></iframe>';

$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'短信提醒信息',
        'autoOpen'=>false,
        'width'=>'800',
        'height'=>'600',
        'modal'=>true,
        'buttons'=>array(
            'Close'=>'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="view_message_frame" width="100%" height="100%" style="border:0px"></iframe>';

$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h1><?php echo $this->pageTitle; ?></h1>

<div class="search-form">
    <?php $this->renderPartial('scoreSearch', array(
        'model' => $model,
        'city_id'=>$city_id,
        'train'=>$train,
        'score'=>$score,
        'driver_id'=>$driver_id,
    )); ?>
</div><!-- search-form -->
<input type="button" id="btnBatchUpdateScore" value="批量修改司机分" onclick="batchUpdateScore();"/>
<!--<a href="/v2/index.php?r=driver/scoreBatchUpdate">批量修改司机分数</a>-->
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

        'driver_id',
        'score',
        'start_score_time',
        array('name'=>'train','value'=>'$data->train == 0 ? "否" : "是"'),
        'year_driver_count',

        array(
            'name'=>'操作',
            'headerHtmlOptions'=>array(
                //'width'=>'80px',
                'nowrap'=>'nowrap'),
            'type'=>'raw',
            'value'=>array($this,'getScoreDeal'),
        ),
    ),
)); ?>

<script>
    function sendMsgSingle(id) {
        url = '<?php echo Yii::app()->createUrl('/driver/scoreStudyNotice');?>&driver_id='+id;
        $("#view_message_frame").attr("src",url);
        $("#mydialog").dialog("open");
    }

    function hasStudy(driver_id){
        $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/driver/scoreStudyOver');?>',
            'data':'driver_id='+driver_id+'&type=0',
            'type':'post',
            'success':function(data){
                if(data==1){
                    alert("操作成功");
                    location.reload();
                }else{
                    alert('操作失败，请确定您有操作权限');
                }
            },
            'cache':false
        });
    }

    function batchUpdateScore(){
        url = '<?php echo Yii::app()->createUrl('/driver/scoreBatchUpdate');?>';
        $("#view_score_frame").attr("src",url);
        $("#scoredialog").dialog("open");
    }

    function updateScore(driver_id){
        url = '<?php echo Yii::app()->createUrl('/driver/scoreUpdate');?>&driver_id='+driver_id;
        $("#view_score_frame").attr("src",url);
        $("#scoredialog").dialog("open");
    }
</script>

