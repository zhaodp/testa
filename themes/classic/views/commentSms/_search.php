<div class="span12">
<?php
$form = $this->beginWidget ( 'CActiveForm', array ('action' => Yii::app ()->createUrl ( $this->route ), 'method' => 'post' ) );
$sender = isset($_GET['sender']) ? $_GET['sender'] : '';
$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
$starType = isset($_GET['starType']) ? $_GET['starType'] : 0;
$starTypeArr = array(0=>'请选择',1=>'大于等于',2=>'小于等于',3=>'等于');
$star = isset($_GET['star']) ? $_GET['star'] : '';
$starArr = array(''=>'请选择',0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>5);
$orderStatus = isset($_GET['orderStatus']) ? $_GET['orderStatus'] : '';
$orderStatusArr = array(''=>'全部',0=>'报单',1=>'消单');
$city = isset($_GET['city_id']) ? $_GET['city_id'] : 0;
$driver_id = isset($_GET['driver_id']) ? $_GET['driver_id'] : '';
$driver_name = isset($_GET['driver_name']) ? trim($_GET['driver_name']) : '';
$sms_type = isset($_GET['sms_type']) ? $_GET['sms_type'] : '';
$typeArr = array(''=>'请选择',0=>'服务评价',1=>'价格核实');
?>

<div class="row-fluid">
	<div class="span3"><label>城市</label>
		<?php
		echo CHtml::dropDownList ('city',$city, Dict::items ( 'city' ) );
		?>
	</div>

	<div class="span3"><label>司机工号</label>		
		<?php
		echo CHtml::textField('driver_id',$driver_id)
		?>
	</div>
	<div class="span3"><label>司机手机号</label>		
			<input type="text"   name="mobile" value="<?php echo $mobile;?>" />
	</div>
	<div class="span3"><label>评价星级</label>		
			<?php	echo CHtml::dropDownList('starType',$starType,$starTypeArr,array('style'=>'width:120px;'));?>
			<?php	echo CHtml::dropDownList('star',$star,$starArr,array('style'=>'width:120px;'));?>
	</div>

	
</div>
<div class="row-fluid">
	<div class="span3"><label>客人手机号</label>		
		<?php
		echo CHtml::textField('sender',$sender)
		?>
	</div>
	<div class="span3"><label>开始时间</label>		
		<?php
		Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
		$this->widget('CJuiDateTimePicker', array (
			'name'=>'startTime',
			'model'=>'',
			'value'=>'', 
			'mode'=>'date',  //use "time","date" or "datetime" (default)
			'options'=>array (
				'dateFormat'=>'yy-mm-dd'
			),  // jquery plugin options
			'language'=>'zh'
		));
	?>
	</div>
	<div class="span3"><label>结束时间</label>		
		<?php
		Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
		$this->widget('CJuiDateTimePicker', array (
			'name'=>'endTime',
			'model'=>'',
			'value'=>'', 
			'mode'=>'date',  //use "time","date" or "datetime" (default)
			'options'=>array (
				'dateFormat'=>'yy-mm-dd'
			),  // jquery plugin options
			'language'=>'zh'
		));
	?>
	</div>
	<div class="span3"><label>处理情况</label>	
		<?php
		echo $form->dropDownList ( $model, 'status', array (''=>'全部','0' => '未处理', '1' => '已处理' ) );
		?>
	</div>
</div>
<div class="row-fluid">
<div class="span3"><label>报单/销单</label>	
		<?php	
		
		echo CHtml::dropDownList('orderStatus',$orderStatus,$orderStatusArr,array('style'=>'width:120px;'));?>
	</div>
	
	<div class="span3"><label>短信类型</label>	
		<?php	
		echo CHtml::dropDownList('sms_type',$sms_type,$typeArr,array('style'=>'width:120px;'));?>
	</div>
	
	<div class="span3"><label>司机姓名</label>	
		<?php	
		echo CHtml::textField('driver_name',$driver_id);
		?>
	</div>
    <div class="span3"><label>&nbsp;</label>
        <?php
        echo CHtml::submitButton ( '搜索', array ('class' => 'btn btn-success span5' ) );
        ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span10">
        <h3>  <?php echo $statusStr; ?></h3>
    </div>
</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->