<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$create_time_begin = isset($param['create_time_begin'])?$param['create_time_begin']:'';
$create_time_end = isset($param['create_time_end'])?$param['create_time_end']:'';
$feec_time_begin = isset($param['feec_time_begin'])?$param['feec_time_begin']:'';
$feec_time_end = isset($param['feec_time_end'])?$param['feec_time_end']:'';
$feed_time_begin = isset($param['feed_time_begin'])?$param['feed_time_begin']:'';
$feed_time_end = isset($param['feed_time_end'])?$param['feed_time_end']:'';
?>
<div class="well span12" style="border:0px">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'search-form',
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>

    <div class="span12">
	<div class='span3'>
	    <label>工单ID</label>
	   <?php echo CHtml::textField('support_ticket_id',isset($param['support_ticket_id'])?$param['support_ticket_id']:''); ?>
	</div>

        <div class='span3'>
          <label>司机工号</label>
	  <?php echo CHtml::textField('driver_id',isset($param['driver_id'])?$param['driver_id']:''); ?>
	</div>

	 <div class='span3'>
	    <label>城市</label>
	    <?php 
		echo CHtml::dropDownList('city_id',!empty($param['city_id'])?$param['city_id']:0,Dict::items('city'));
	    ?>
        </div> 
	
	<div class='span3'>
            <label>状态</label>
            <?php
		echo CHtml::dropDownList('status',!empty($param['status'])?$param['status']:0,array(0=>'全部',1=>'未处理',2=>'已处理',));
	    ?>
       </div>
  </div>
  <div class="span12">
       <div class='span3'>
           <label>创建人</label>
	   <?php echo CHtml::textField('create_user',isset($param['create_user'])?$param['create_user']:''); ?>
       </div>
       <div class='span3'>
           <label>处理人</label>
	   <?php echo CHtml::textField('deal_user',isset($param['deal_user'])?$param['deal_user']:''); ?>
       </div>
       <div class='span3'>
           <label>工单创建开始时间</label>
	  <?php
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'create_time_begin',
                'value' => $create_time_begin,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));
	 ?>
       </div>
	<div class='span3'>
           <label>工单创建结束时间</label>
          <?php
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'create_time_end',
                'value' => $create_time_end,
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
   <div class='span12'>
	<div class='span3'>
           <label>补偿创建开始时间</label>
          <?php
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'feec_time_begin',
                'value' => $feec_time_begin,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));
         ?>
       </div>
       <div class='span3'>
           <label>补偿创建结束时间</label>
          <?php
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'feec_time_end',
                'value' => $feec_time_end,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));
         ?>
       </div>
       <div class='span3'>
           <label>补偿处理开始时间</label>
          <?php
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'feed_time_begin',
                'value' => $feed_time_begin,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('class' => "span9")
            ));
         ?>
       </div>
       <div class='span3'>
           <label>补偿处理结束时间</label>
          <?php
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'feed_time_end',
                'value' => $feed_time_end,
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
        <span class='span2'>
		<?php echo CHtml::submitButton(' 搜索 ',array('class'=>'btn btn-primary','id'=>'search-button')); ?>
        </span>
        <span class='span2'>
		<?php
        		//获取搜索的参数
				 $params=(isset($_GET['support_ticket_id'])?'&support_ticket_id='.$_GET['support_ticket_id']:'').
                			(isset($_GET['driver_id'])?'&driver_id='.$_GET['driver_id']:'').
                			(isset($_GET['city_id'])?'&city_id='.$_GET['city_id']:'').
                			(isset($_GET['status'])?'&status='.$_GET['status']:'').
                			(isset($_GET['create_user'])?'&create_user='.$_GET['create_user']:'').
                			(isset($_GET['deal_user'])?'&deal_user='.$_GET['deal_user']:'').
                			(isset($_GET['create_time_begin'])?'&create_time_begin='.$_GET['create_time_begin']:'').
		 			(isset($_GET['create_time_end'])?'&create_time_end='.$_GET['create_time_end']:'').
                			(isset($_GET['feec_time_begin'])?'&feec_time_begin='.$_GET['feec_time_begin']:'').
		 			(isset($_GET['feec_time_end'])?'&feec_time_end='.$_GET['feec_time_end']:'').
                			(isset($_GET['feed_time_begin'])?'&feed_time_begin='.$_GET['feed_time_begin']:'').
		 			(isset($_GET['feed_time_end'])?'&feed_time_end='.$_GET['feed_time_end']:'');
        			echo CHtml::link('导出excel',Yii::app()->createUrl('/crm/export'.$params),array('class' => 'btn-primary btn')).'&nbsp;';
                ?>
        </span>
    </div>
<?php $this->endWidget(); ?>

</div><!-- search-form -->
