<?php
$this->setPageTitle('司机e币管理');
Yii::app()->clientScript->registerScript('search', "
 $('.search-button').click(function(){
 	$('.search-form').toggle();
	return false;
 });
 $('.search-form form').submit(function(){
 	$('#ecoin-driver-grid').yiiGridView('update', {
		data: $(this).serialize()
 	});
 	return false;
});
 ");

?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'update_ecoin_dialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '修改司机e币',
        'autoOpen' => false,
        'width' => '600',
        'height' => '400',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#update_ecoin_dialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<iframe id="cru-frame-update-ecoin" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'ecoindialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'修改司机e币',
        'autoOpen'=>false,
        'width'=>'800',
        'height'=>'600',
        'modal'=>true,
        'buttons'=>array(
            '关闭' => 'js:function(){$("#ecoindialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="view_ecoin_frame" width="80%" height="80%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h1><?php echo $this->pageTitle; ?></h1>

<div class="search-form">
    <?php $this->renderPartial('ecoinSearch', array(
        'model' => $model,
        'city_id' => $city_id,
        'driver_id' => $driver_id,
    )); ?>
</div><!-- search-form -->
<input type="button" id="btnBatchUpdateEcoin" value="批量修改司机e币" onclick="batchUpdateEcoin();"/>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'ecoin-driver-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped',
    'columns' => array(
        'driver_id',
        'total_wealth',
        array(
            'name' => '操作',
            'headerHtmlOptions' => array(
                //'width'=>'80px',
                'nowrap' => 'nowrap'),
            'type' => 'raw',
            'value' => array($this, 'getEcoinDeal'),
        ),
    ),
)); ?>

<script>
    function updateEcoinDialogdivInit(href) {
        $("#cru-frame-update-ecoin").attr("src", href);
        $("#update_ecoin_dialog").dialog("open");
        return false;
    }


    function batchUpdateEcoin(){
        url = '<?php echo Yii::app()->createUrl('/driver/ecoinBatchUpdate');?>';
        $("#view_ecoin_frame").attr("src",url);
        $("#ecoindialog").dialog("open");
        return false;
    }
</script>

