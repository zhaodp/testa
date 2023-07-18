<div class="well span12">

<?php 
$citys = array();
if(Yii::app()->user->city==0)
{
	$citys = Dict::items('city');
	$citys[0] = '--请选择城市--';
	
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
		'id'=>'DriverBonusRankearch'
	));
	
	if (isset($_GET['DriverBonusRank']))
	{
		$param = $_GET['DriverBonusRank'];
	}

			
?>
	<div class="row-fluid">
	<div class="span3">
			<?php echo $form->label($model,'city_id'); ?>
			<?php 
				echo $form->dropDownList($model,
							'city_id',
							$citys,
							array()
						);
			?>			
	</div>
		<div class="span3">
				<label for="DriverBonusRank_create">开始时间</label>
				<?php
					$start_time = isset($data['start_time']) ? substr($data['start_time'], 0,10) : date('Y-m-d',strtotime("-1 day"));
					Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
					$this->widget('CJuiDateTimePicker', array ( 
						'name'=>'DriverBonusRank[created]',
						'model'=>$model,
						'value'=>$start_time, 
						'mode'=>'date',
						'options'=>array (
							'dateFormat'=>'yy-mm-dd',
						),  // jquery plugin options
						'language'=>'zh'
					));
				?>
				</div>
				
				<div class="span3">
				<label for="DriverBonusRank_create">结束时间</label>
				<?php
					$end_time = isset($data['end_time']) ? substr($data['end_time'], 0,10) : date('Y-m-d',time());
					Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
					$this->widget('CJuiDateTimePicker', array ( 
						'name'=>'DriverBonusRank[creates]',
						'model'=>$model,
						'value'=>$end_time, 
						'mode'=>'date',
						'options'=>array (
							'dateFormat'=>'yy-mm-dd'
						),  // jquery plugin options
						'language'=>'zh'
					));
				?>
</div>
<div class="span3">
<label for="DriverBonusRank_create">&nbsp;</label>
			<?php echo CHtml::submitButton('搜索'); ?>
		</div>
	</div>
 

<?php 
	$this->endWidget(); 
}
?>

</div><!-- search-form -->
