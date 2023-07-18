<?php
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'callcenter-error-list-search',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>



<h3>错误记录</h3>
    <div class="row-fluid">
        <div class="span3">
            <label>时间</label>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'start_time',
                'value'=>$model->start_time,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"开始",
                ),
            ));

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'end_time',
                'value'=>$model->end_time,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),  // jquery plugin options
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"结束",
                ),
            ));

            ?>
        </div>
        <div class="span3">
            <label>错误类型</label>
            <?php
                echo CHtml::dropDownList('error_type',$model->error_type,array('0'=>'全部')+CallcenterError::$errorArr);
            ?>
            <br/>
            <?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-primary span8')); ?>
        </div>

        <div class="span3">
            <label>坐席</label>
            <?php echo CHtml::textField('agent_id',$model->agent_id,array('class'=>'input-large','placeholder'=>'客服坐席'));?>

        </div>

    </div>




<?php $this->endWidget(); ?>