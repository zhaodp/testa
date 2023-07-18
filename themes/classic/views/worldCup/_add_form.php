<?php
?>
<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'post',
    )); ?>
    <div class="span12">
        <div class="span3">
            <label>国家一</label>
            <?php echo CHtml::dropDownList('country_1',isset($model['country_1'])?$model['country_1']:0,WorldCup::$country); ?>
        </div>
	 <div class="span3">
            <label>国家二</label>
            <?php echo CHtml::dropDownList('country_2',isset($model['country_2'])?$model['country_2']:0,WorldCup::$country); ?>
        </div>

	<div class="span3">
            <label>比赛开始时间</label>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'begin_time',
                'value' => '',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));
            ?>
         </div>
	</div>
	<div class="span12">
	 <div class="span3"> 
	 	<?php echo CHtml::submitButton('创建', array('class' => 'btn btn-success btn-block')); ?>
	 </div>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- search-form -->
