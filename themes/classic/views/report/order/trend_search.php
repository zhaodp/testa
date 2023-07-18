<div class="span11">
    <ul class="nav nav-pills">
        <?php
        $data_source_arr = OrderStat::$data_source;
        $default_source=$condition['data_source'];

        foreach ($data_source_arr as $k=>$v) {
            $light='';
            if($default_source==$k)
                $light='class="active"';
            echo "<li ".$light."><a href='".Yii::app()->createUrl('report/trend',array('data_source'=>$k))."'>".$v."</a></li>";
        }
        ?>
    </ul>
<hr>
</div>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span10">
            <!-- chart -->
            <div id="container" style="min-width: 400px; height: 420px; margin: 0 auto;"></div>
        </div>
        <div class="span2">
            <div class="search-form">
                <?php  $form=$this->beginWidget('CActiveForm', array(
                    'action'=>Yii::app()->createUrl($this->route),
                    'method'=>'get',
                )); ?>
                <div class="span12">
                    <label>开始日期</label>
                    <?php Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                        $this->widget('CJuiDateTimePicker', array(
                            'id' => 'start_time',
                            'name' => 'start_time',
                            'value' => $condition['start_time'],
                            'mode' => 'date',
                            'options' => array(
                                'dateFormat' => 'yy-mm-dd'
                            ),
                            'htmlOptions' => array(
                                'style' => 'width:110px;'
                            ),
                            'language' => 'zh'
                    ));?>
                </div>
                <div class="span12">
                    <label>结束日期</label>
                    <?php $this->widget('CJuiDateTimePicker', array(
                        'name' => 'end_time',
                        'value' => $condition['end_time'],
                        'mode' => 'date',
                        'options' => array(
                            'dateFormat' => 'yy-mm-dd'
                        ),
                        'htmlOptions' => array(
                            'style' => 'width:110px;'
                        ),
                        'language' => 'zh'
                    ));?>
                </div>
                <div class="span12">
                    <label>城市</label>
                    <?php echo CHtml::dropDownList('city_id', $condition['city_id'], Common::getOpenCity(), array('style' => 'width:110px;')); ?>
                </div>
                <div class="span12">
                    <label>时间段</label>
                    <?php echo CHtml::dropDownList('time_part', $condition['time_part'],
                        array(
                            '' => '全部',
                            '7' => '7-22点',
                            '22' => '22-23点',
                            '23' => '23-24点',
                            '24' => '24-7点'),
                        array('style' => 'width:110px;')); ?>
                </div>
                <div class="span12">
                    <label>距离范围</label>
                    <?php echo CHtml::dropDownList('distance_area', $condition['distance_area'],
                        array(
                            '' => '全部',
                            '5' => '5公里',
                            '10' => '10公里',
                            '20' => '20公里',
                            '30' => '30公里',
                            '9999' => '30公里以上'),
                        array('style' => 'width:110px;')); ?>
                </div>
                <div class="span12">
                    <?php echo CHtml::hiddenField('data_source', $condition['data_source']) ?>
                    <?php echo CHtml::submitButton('搜索', array('class' => 'btn btn-success span7')); ?>
                </div>
                <?php $this->endWidget();?>
            </div>
        </div>
    </div>
</div>