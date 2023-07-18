<?php
/* @var $this CrmController */
/* @var $model SupportTicket */
/* @var $form CActiveForm */
?>


<div class="well span12" style="border:0px">
<?php $form=$this->beginWidget('CActiveForm', array(
	    'id'=>'search-form',
	    'action'=>Yii::app()->createUrl($this->route),
	    'method'=>'get',
	    )); ?>

<div class="controls controls-row">

<span class='span2'>
<?php echo $form->label($model,'id'); ?>
<?php echo $form->textField($model,'id',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
</span>

<span class='span2'>
<?php echo $form->label($model,'driver_id'); ?>
<?php echo $form->textField($model,'driver_id',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
</span>

<span class='span2'>
<?php echo $form->label($model,'follow_user'); ?>
<?php echo $form->textField($model,'follow_user',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
</span>
<span class='span2'>
<?php echo $form->label($model,'device'); ?>
<?php echo $form->textField($model,'device',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
</span>
<span class='span2'>
<?php echo $form->label($model,'os'); ?>
<?php echo $form->textField($model,'os',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
</span>

<span class='span2'>
<?php echo $form->label($model,'status'); ?>
<?php
echo $form->dropDownList($model,
	'status',
	SupportTicket::$statusList,
	array('empty'=>array(''=>'全部'),'class'=>"span12"));
?>
</span>
</div>
<div class="controls controls-row">
<span class='span2'>
<?php echo $form->label($model,'type'); ?>
<?php
$cates = Dict::items('ticket_category');
echo $form->dropDownList($model,
	'type',
	$cates,
	array('empty'=>array(''=>'全部'),
	    'ajax'=>array(
		'url'=>Yii::app()->createUrl('crm/dynamicClass'),
		'data'=>array('type_id'=>'js:this.value','from'=>'search'),
		'update'=>'#SupportTicket_class',
		),
	    'class'=>"span12"
	    )
	);
?>
</span>
<span class='span2'>
<?php echo $form->label($model,'class'); ?>
<?php
echo $form->dropDownList($model,
	'class',
	SupportTicketClass::model()->getClasses($model->type,'search'),
	array('empty'=>array(''=>'全部'),
	    'class'=>"span12"
	    )
	);
?>
</span>


<span class='span2'>
<?php echo $form->label($model,'group'); ?>
<?php
echo $form->dropDownList($model,'group',Dict::items('support_ticket_group'),array('empty'=>array(''=>'全部'),'class'=>"span12"));
?>
</span>

<div class="span2">
<?php echo $form->label($model,'city_id'); ?>
<?php
/*
   $city_list = Dict::items('city');
   $user_city_id = Yii::app()->user->city;
   if ($user_city_id != 0) {
   $city_list = array(
   $user_city_id => $city_list[$user_city_id]
   );
   }
   echo $form->dropDownList($model,'city_id',$city_list,array('class'=>'span12'));
 */
$user_city_id = Yii::app()->user->city;

if ($user_city_id != 0) {
    $city_list = array(
	    '城市' => array(
		$user_city_id => Dict::item('city', $user_city_id)
		)
	    );
    $city_id = $user_city_id;
} else {
    $city_id = $model->city_id;
    $city_list = CityTools::cityPinYinSort();
}
$this->widget("application.widgets.common.DropDownCity", array(
	    'cityList' => $city_list,
	    'name' => 'SupportTicket[city_id]',
	    'value' => $city_id,
	    'type' => 'modal',
	    'htmlOptions' => array(
		'style' => 'width: 134px; cursor: pointer;',
		'readonly' => 'readonly',
		)
	    ));
?>
</div>
<span class='span2'>
<?php echo $form->label($model,'last_reply_user'); ?>
<?php echo $form->textField($model,'last_reply_user',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
</span>
<span class='span2'>
<?php echo $form->label($model,'content'); ?>
<?php echo $form->textField($model,'content',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
</span>


</div>
<div class="row-fluid">
<div class="span3">
<p style="margin: 0px;"><?php echo $form->label($model,'create_time'); ?></p>
<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array (
	    'name'=>'search[create_time_start]',
	    'value'=>'',
	    'mode'=>'datetime',  //use "time","date" or "datetime" (default)
	    'options'=>array (
		'dateFormat'=>'yy-mm-dd'
		),
	    'language'=>'zh',
	    'htmlOptions'=>array(
		'placeholder'=>"开始",
		),


	    ));?>

<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array (
	    'name'=>'search[create_time_end]',
	    'value'=>'',
	    'mode'=>'datetime',  //use "time","date" or "datetime" (default)
	    'options'=>array (
		'dateFormat'=>'yy-mm-dd'
		),
	    'language'=>'zh',
	    'htmlOptions'=>array(
		'placeholder'=>"结束",
		),


	    ));?>

</div>
<div class="row-fluid">
<div class="span3">            <p style="margin: 0px;"><?php echo $form->label($model,'last_reply_time'); ?></p>

<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array (
	    'name'=>'search[last_reply_time_start]',
	    'value'=>'',
	    'mode'=>'datetime',  //use "time","date" or "datetime" (default)
	    'options'=>array (
		'dateFormat'=>'yy-mm-dd'
		),
	    'language'=>'zh',
	    'htmlOptions'=>array(
		'placeholder'=>"开始",
		),


	    ));?>

<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array (
	    'name'=>'search[last_reply_time_end]',
	    'value'=>'',
	    'mode'=>'datetime',  //use "time","date" or "datetime" (default)
	    'options'=>array (
		'dateFormat'=>'yy-mm-dd'
		),
	    'language'=>'zh',
	    'htmlOptions'=>array(
		'placeholder'=>"结束",
		),


	    ));?>


</div>
<div class="row-fluid">
<div class="span3">
<p style="margin: 0px;"><?php echo $form->label($model,'close_time'); ?></p>
<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array (
	    'name'=>'search[close_time_start]',
	    'value'=>'',
	    'mode'=>'datetime',  //use "time","date" or "datetime" (default)
	    'options'=>array (
		'dateFormat'=>'yy-mm-dd'
		),
	    'language'=>'zh',
	    'htmlOptions'=>array(
		'placeholder'=>"开始",
		),


	    ));?>

<?php
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$this->widget('CJuiDateTimePicker', array (
	    'name'=>'search[close_time_end]',
	    'value'=>'',
	    'mode'=>'datetime',  //use "time","date" or "datetime" (default)
	    'options'=>array (
		'dateFormat'=>'yy-mm-dd'
		),
	    'language'=>'zh',
	    'htmlOptions'=>array(
		'placeholder'=>"结束",
		),


	    ));?>

</div>
<div class="row-fluid">
<div class="span3">           <?php echo $form->label($model,'version'); ?>
<?php echo $form->textField($model,'version',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
</div>
</div>
<div class="controls controls-row">


<span class='span2' style="padding-top:25px;">
<?php echo CHtml::submitButton(' 搜索 ',array('class'=>'btn btn-primary','id'=>'search-button')); ?>
</span>
<span class='span2' style="padding-top:25px;">
<?php echo CHtml::link('工单分类设置', array('crm/ticketClassList'),array('class'=>'btn-primary btn'));?>
</span>
    <span class='span2' style="padding-top:25px;">
        <?php
            //获取搜索的参数
            $params=(isset($_GET['SupportTicket']['id'])?'&id='.$_GET['SupportTicket']['id']:'').
            (isset($_GET['SupportTicket']['driver_id'])?'&driver_id='.$_GET['SupportTicket']['driver_id']:'').
            (isset($_GET['SupportTicket']['follow_user'])?'&follow_user='.$_GET['SupportTicket']['follow_user']:'').
            (isset($_GET['SupportTicket']['device'])?'&device='.$_GET['SupportTicket']['device']:'').
            (isset($_GET['SupportTicket']['os'])?'&os='.$_GET['SupportTicket']['os']:'').
            (isset($_GET['SupportTicket']['status'])&&!empty($_GET['SupportTicket']['status'])?'&status='.$_GET['SupportTicket']['status']:'').
            (isset($_GET['SupportTicket']['type'])&&!empty($_GET['SupportTicket']['type'])?'&type='.$_GET['SupportTicket']['type']:'').
            (isset($_GET['SupportTicket']['class'])&&!empty($_GET['SupportTicket']['class'])?'&class='.$_GET['SupportTicket']['class']:'').
            (isset($_GET['SupportTicket']['group'])&&!empty($_GET['SupportTicket']['group'])?'&group='.$_GET['SupportTicket']['group']:'').
            (isset($_GET['SupportTicket']['city_id'])?'&city_id='.$_GET['SupportTicket']['city_id']:'').
            (isset($_GET['SupportTicket']['last_reply_user'])?'&last_reply_user='.$_GET['SupportTicket']['last_reply_user']:'').
            (isset($_GET['SupportTicket']['content'])?'&content='.$_GET['SupportTicket']['content']:'').
            (isset($_GET['search']['create_time_start'])?'&create_time_start='.$_GET['search']['create_time_start']:'').
            (isset($_GET['search']['create_time_end'])?'&create_time_end='.$_GET['search']['create_time_end']:'').
            (isset($_GET['search']['last_reply_time_start'])?'&last_reply_time_start='.$_GET['search']['last_reply_time_start']:'').
            (isset($_GET['search']['last_reply_time_end'])?'&last_reply_time_end='.$_GET['search']['last_reply_time_end']:'').
            (isset($_GET['search']['close_time_start'])?'&close_time_start='.$_GET['search']['close_time_start']:'').
            (isset($_GET['search']['close_time_end'])?'&close_time_end='.$_GET['search']['close_time_end']:'').
            (isset($_GET['SupportTicket']['version'])?'&version='.$_GET['SupportTicket']['version']:'');
            $url = '/crm/exportTickets'.$params;
            echo CHtml::link('导出excel',Yii::app()->createUrl($url),array('class' => 'btn-primary btn')).'&nbsp;';
        ?>
    </span>
</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
