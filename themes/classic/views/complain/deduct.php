<h1>司机扣分管理</h1>
<div class="search-form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'complain-deduct-search',
        'action'=>Yii::app()->createUrl('complain/deduct'),
        'method'=>'get',
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('城市','city_id');?>
            <?php echo CHtml::dropDownList('city_id',$city_id, Dict::items('city'),array('class'=>'info')); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('统计月份','create_time');?>
            <?php
            $monthArray = array();
            $last_month = date('m',strtotime('+1 month'));
            $last_year = date('Y');
            $last_month_string = date('Ym');
            while ($last_month_string >= 201305){
                $last_month_time = mktime(0, 0, 0, $last_month -1, 1, $last_year);
                $last_month = date('m', $last_month_time);
                $last_year = date('Y', $last_month_time);
                $last_month_string = date('Ym', $last_month_time);
                $monthArray[$last_month_string] = $last_month_string;

            }
            echo CHtml::dropDownList('datetime',$date_time,$monthArray);
            ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span10">
            <button class="btn btn-primary span2" type="submit" name="search">搜索</button>
        </div>
    </div>


    <?php $this->endWidget(); ?>
</div>
<?php

//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'complain-grid',
    'ajaxUpdate' => false,
    'dataProvider'=>$dataProvider,
    'columns'=>array (
        array (
            'name'=>'城市',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'Dict::item("city",$data["city_id"])'
        ),
        array (
            'name'=>'司机工号',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["driver_id"]."(".CustomerComplain::getDriverCount($data["driver_id"]).")"'
        ),
        array (
            'name'=>'姓名',
        'headerHtmlOptions'=>array (
            'style'=>'width:100px',
            'nowrap'=>'nowrap'
        ),
        'value'=>'$data["driver_name"]'
        ),
        array (
            'name'=>'当前状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["status"]'
        ),
        array (
            'name'=>'分数',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["mark"]'
        ),
        array (
            'name'=>'司机投诉率（百分之）',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["point"]'
        ),
    )
));

?>