<div class="span12">
<?php
$form = $this->beginWidget ( 'CActiveForm', array ('action' => Yii::app ()->createUrl ( $this->route ), 'method' => 'post' ) );
$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
$starType = isset($_GET['starType']) ? $_GET['starType'] : 0;
$starTypeArr = array(0=>'请选择',1=>'大于等于',2=>'小于等于',3=>'等于');
$star = isset($_GET['star']) ? $_GET['star'] : '';
$starArr = array(''=>'请选择',0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>5);
$orderStatus = isset($_GET['orderStatus']) ? $_GET['orderStatus'] : '';
$orderStatusArr = array(''=>'全部',0=>'报单',1=>'消单');
?>

<div class="row-fluid">
	<div class="span3"><label>城市</label>
		<?php
		echo $form->dropDownList ( $model, 'uuid', Dict::items ( 'city' ) );
		?>
	</div>

	<div class="span3"><label>司机工号</label>		
		<?php
		echo $form->textField ( $model, 'employee_id' );
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
		echo $form->textField ( $model, 'name' );
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
</div>

	
	
	
<div class="row-fluid">
	<?php
	echo CHtml::submitButton ( 'Search', array ('class' => 'btn span2' ) );
	?>
</div>
<?php
$this->endWidget ();
?>

</div>
<!-- search-form -->