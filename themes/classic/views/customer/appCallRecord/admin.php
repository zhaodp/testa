<?php
$this->breadcrumbs = array(
    'App Call Records' => array('index'),
    'Manage',
);

$this->menu = array(
    array('label' => 'List AppCallRecord', 'url' => array('index')),
    array('label' => 'Create AppCallRecord', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
        var title = $(this).text() == '收起搜索' ? '展开搜索' : '收起搜索';
        $(this).text(title);
	return false;
});

");
?>
<!--$('.search-form form').submit(function(){-->
<!--$.fn.yiiGridView.update('app-call-record-grid', {-->
<!--data: $(this).serialize()-->
<!--});-->
<!--return false;-->
<!--});-->

<h1>app呼叫记录</h1>

<?php echo CHtml::link('收起搜索', '#', array('class' => 'search-button')); ?>
<div class="search-form">
    <?php
    $this->renderPartial('appCallRecord/_search', array(
        'model' => $model,
        'stime'=>$stime,
        'etime'=>$etime,
        'os_arr'=>$os_arr
    ));
    ?>
</div><!-- search-form -->

<div class="row-fluid">
    <div class="span10">
        <h4>  <?php echo  '累计呼叫：'.$status_str; ?></h4>
        <h4>  <?php echo  '当前统计：'.$count_str; ?></h4>
    </div>
</div>


<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'app-call-record-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table',
    'enableSorting' => FALSE,
    'columns' => array(
        'id',
        array(
            'name' => 'driverID',
            'type' => 'raw',
            'value' => '
                ($data->driverID <= 0 ? CHtml::link($data->driverID, array("driver/archives", "id"=>$data->driverID), array("target"=>"_blank")) : $data->driverID)
            ',
        ),
        array(
            'name' => '订单号',
            'type' => 'raw',
            'value' => array($this,'getOrderLink'),
        ),
        array(
            'name' => 'longitude',
            'header' => '客户位置',
            'type' => 'raw',
            'value' => '$data->phone.($data->phone || $data->driverID <= 0 ? "<br>" : "").($data->longitude + $data->latitude > 10 ? GPS::model()->getStreetByBaiduGPS($data->longitude, $data->latitude) : "")',
        ),
        array(
            'name' => 'call_time',
            'value' => 'date("Y-m-d H:i:s",$data->call_time)',
        ),
        'device',
        'os',
        'version',
    ),
));
?>

<script>
	jQuery(function ($) {
		$('#export_btn').click(function () {
			var url = '<?php
						echo Yii::app()->createUrl('/customer/exportExcel',
					 	array('startTime'=>$stime, 'endTime'=>$etime, 'os'=>$model->os));?>';
			location.href = url;
		});
	})
</script>
