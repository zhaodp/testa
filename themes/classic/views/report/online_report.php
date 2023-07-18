<?php
$this->pageTitle = '司机在线情况';
$year_list = array(
    '2013' => '2013',
    '2014' => '2014',
    '2015' => '2015',
	'2016' => '2016',
);
$month_list = array(
    '1'=>'1',
    '2'=>'2',
    '3'=>'3',
    '4'=>'4',
    '5'=>'5',
    '6'=>'6',
    '7'=>'7',
    '8'=>'8',
    '9'=>'9',
    '10'=>'10',
    '11'=>'11',
    '12'=>'12'
);
$show_number = array(
    '30' => '30',
    '50' => '50',
    '100' => '100',
)
?>
<h1>司机在线情况 <span style="margin-left:60px;"><a class="btn" target="_blank" href="<?php echo Yii::app()->createUrl('report/detail');?>">详细情况</a></span></h1>
    <!--
    <form class="form-horizontal" action="<?php echo Yii::app()->createUrl('report/onlineReport');?>" method="get">
    -->
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl('report/onlineReport'),
        'method'=>'get',
    ));
    ?>
    <div class="row-fluid">

        <div class="span1">
            <label>工号</label>
            <?php echo CHtml::textField('driver_id', $driver_id, array('style'=>'width:80px;'));?>
        </div>

        <div class="input-prepend input-append span3">
            <label>按月选择时间</label>
            <?php echo CHtml::dropDownList('year', $year, $year_list, array('style'=>'width:80px;'));?>
            <span class="add-on">年</span>
            <?php echo CHtml::dropDownList('month', $month, $month_list, array('style'=>'width:80px;'));?>
            <span class="add-on">月</span>
        </div>

        <div class="span2">
            <label>所在城市</label>
            <?php
                $city_list = Dict::items('city');
                $user_city_id = Yii::app()->user->city;
                if ($user_city_id > 0) {
                    $city[$user_city_id] = $city_list[$user_city_id];
                } else {
                    $city = $city_list;
                }
            ?>
            <?php echo CHtml::dropDownList('city_id', $city_id, $city, array('style'=>'width:120px'));?>
        </div>

        <div class="span1">
            <label>显示数量</label>
            <?php echo CHtml::dropDownList('page_size', $page_size, $show_number, array('style'=>'width:80px;'));?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::submitButton('搜索', array('class'=>'btn btn-primary', 'style'=>'margin-top:20px;'))?>
        </div>
    </div>
<?php $this->endWidget(); ?>
<!--
</form>
-->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'online',
    'dataProvider'=>$dataProvider,
    //'cssFile'=>SP_URL_CSS . 'table.css',
    //'itemsCssClass'=>'table  table-condensed',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array(
        array(
            'name'=>'工号',
            'value'=>'$data["driver_id"]'
        ),
        array(
            'name'=>'姓名',
            'type'=>'raw',
            'value'=>'CHtml::link($data["driver_name"],Yii::app()->createUrl("/report/driverOnline", array("driver_id"=>$data["driver_id"], "year"=>$data["year"], "month"=>$data["month"])), array("target"=>"_blank"))',
        ),
        'p_continuous' => array(
            'header'=>'峰值时刻上线天数△',
            'name'=>'p_continuous',
        ),
        'online'=>array(
            'header'=>'上线天数△',
            'name'=>'online',
        ),
        array(
            'name'=>'上线率',
            'value'=>'$data["line_rate"]',
        ),
        'accept_days'=>array(
            'header'=>'接单天数△',
            'name'=>'accept_days',
        ),
        'accept'=>array(
            'header'=>'接单数△',
            'name'=>'accept',
        ),
        'cancel'=>array(
            'header'=>'销单数△',
            'name'=>'cancel',
        ),
        'cancel_rate'=>array(
            'header'=>'消单率△',
            'name'=>'cancel_rate',
        ),
        'complete'=>array(
            'header'=>'报单数△',
            'name'=>'complete',
        ),
        'additional'=>array(
            'header'=>'补单数△',
            'name'=>'additional',
        ),
        'not_online'=>array(
            'header'=>'未上线天数',
            'name'=>'not_online',
        )
    ),
));

?>