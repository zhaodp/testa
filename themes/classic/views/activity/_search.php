<?php
?>
<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl($this->route),'method' => 'post',)); ?>
	<div class="span12"></div>
    <div class="span12">
        <div class="span3">
            <label>活动标题</label>
            <?php echo CHtml::textField('MarketingActivity[title]',isset($model['title'])?$model['title']:''); ?>
        </div>
	<div class="span3">
            <label>开始时间</label>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'MarketingActivity[begintime]',
                'value' => $model['begintime'],
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));
            ?>
        </div>
        <div class="span3">
            <label>结束时间</label>
            <?php  $this->widget('CJuiDateTimePicker', array(
                'name' => 'MarketingActivity[endtime]',
		'value' => $model['endtime'],
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));?>
        </div>
        <div class="span3">
            <label>适用地区</label>
            <?php
		 echo CHtml::dropDownList('city_id',!empty($param['city_id'])?$param['city_id']:0,Dict::items('city'));
	    ?>
        </div>
    </div>
<div class="span12">
	<div class="span3">
            <label>新老客限制</label>
            <?php
                echo CHtml::dropDownList('MarketingActivity[customer]',!empty($model['customer'])?$model['customer']:0,array(0 =>'所有',3=>'不限',1=>'新客户',2=>'老客户'));
            ?>
        </div>
	<div class="span3">
            <label>适用平台</label>
            <?php
                echo CHtml::dropDownList('MarketingActivity[platform]',!empty($model['platform'])?$model['platform']:0,array(0=>'所有',3=>'不限',1=>'IOS',2=>'Andriod'));
            ?>
        </div>
	<div class="span3">
            <label>活动状态</label>
            <?php
                echo CHtml::dropDownList('status',!empty($param['status'])?$param['status']:0,array(0=>'全部',1=>'排队中',2=>'进行中',3=>'已结束'));
            ?>
        </div>
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->

