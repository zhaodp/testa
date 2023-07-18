
<h1>客户投诉分类统计</h1>
    <div class="search-form">
        <?php
        $form=$this->beginWidget('CActiveForm', array(
            'id'=>'complain-list-search',
            'action'=>Yii::app()->createUrl('complain/kpi'),
            'method'=>'get',
        )); ?>
    <div class="row-fluid">
		<div class="span3">
			<?php echo CHtml::label('城市','city_id');?>
			<?php echo CHtml::dropDownList('city_id',$city_id,$city_list)?>
		</div>
		<div class="span3">
            <?php echo CHtml::label('开始日期','start_time');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'start_time',
                'value'=>$start_time,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"开始日期",
                ),


            ));?>
			</div>
        <div class="span3">
            <?php echo CHtml::label('结束日期','end_time');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'end_time',
                'value'=>$end_time,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),  // jquery plugin options
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"结束日期",
                ),
            ));
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
        'dataProvider'=>$data,
        'columns'=>array (
            array (
                'name'=>'分类',
                'headerHtmlOptions'=>array (
                    'style'=>'width:60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'($data["parent_id"]>0)? "&nbsp;&nbsp;&nbsp;&nbsp;".$data["type_name"] :$data["type_name"]'
            ),

            array (
                'name'=>'投诉次数',
                'headerHtmlOptions'=>array (
                    'style'=>'width:120px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["type_count"]'
            ),

            array (
                'name'=>'加权分数',
                'headerHtmlOptions'=>array (
                    'style'=>'width:60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["weight_num"]'
            ),

            array (
                'name'=>'加权投诉率(万分之)',
                'headerHtmlOptions'=>array (
                    'style'=>'width:80px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["weight_rate"]'
            ),
        )
    ));

    ?>
