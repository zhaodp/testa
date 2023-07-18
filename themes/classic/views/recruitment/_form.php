<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */
/* @var $form CActiveForm */


// $gender = array( '女', '男');
// $marry = array( '已婚', '未婚');
// $political_status = array('群众', '无党派人士', '民主党派 ', '团员', '中共党员(含预备党员)');
// $edu = array('大专','本科','硕士','博士','MBA','EMBA','中专','中技','高中','初中','其他');
// $driver_type = array('A1','A2','A3','B1','B2','C1');
// $status = array('全部','已报名', '已通知培训', '已培训考核', '已签约');
// $arrCars = array(
// '1'=>'微/小型车',
// '2'=>'普通轿车',
// '3'=>'高档轿车',
// '4'=>'商务/大型车辆',
// '5'=>'MPV/SUV等'
// );
$gender = Dict::items('gender');
unset($gender[0]);
$marry = Dict::items('marry');
$political_status = Dict::items('political');
$edu = Dict::items('edu');
$driver_type = Dict::items('driver_type');
$status = Dict::items('driver_status');
$arrCars = Dict::items('car_type');


$dataZhaopin = $model->attributes;


?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-zhaopin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>

<?php echo $form->errorSummary($model); ?>

<section id="basicinfo" class="basicinfo">
    <div class="row-fluid">
	    <div class="span12">
			<div class="page-header">
				<h2>1. 基本信息（必填）</h2>
			</div>
		</div>
	</div>
		    <div class="row-fluid">
			    <div class="span4">
					<?php echo $form->labelEx($model,'city_id'); ?>
					<?php 
						$citys = Dict::items('city');
						$citys[0] = '--请选择城市--';
						
						echo $form->dropDownList($model,
									'city_id',
									$citys,
						array(
							'ajax' => array(
							'type'=>'POST', //request type
							'url'=>Yii::app()->createUrl('recruitment/district'),
							'update'=>'#DriverRecruitment_district_id', //selector to update
							'data'=>array('city_id'=>'js:$("#DriverRecruitment_city_id").val()')
							))
						);
						echo $form->error($model, 'city_id');						 
					?>
				</div>
				<div class="span4">
					<?php echo $form->labelEx($model,'district_id'); ?>
					<?php 
						$districts = District::model()->findAll('city_id=:city_id', array(':city_id' => $dataZhaopin['city_id']));
						$districts = CHtml::listData($districts,'id','name');
						$districts[0] = '--请选择区域--';
						ksort($districts);						
						echo $form->dropDownList($model,
									'district_id',
									$districts,
						array()
						);
						echo $form->error($model, 'district_id');							 
					?>
				</div>
				<div class="span4">
					<?php echo $form->labelEx($model,'work_type'); ?>
					<?php 
						$work_type = Dict::items('work_type');				
						echo $form->dropDownList($model,
									'work_type',
									$work_type,
						array()
					);
					echo $form->error($model, 'work_type');						 
					?>
				</div>
		    </div>
		    <div class="row-fluid">
			    <div class="span6">
				<?php echo $form->labelEx($model,'address'); ?>
				<?php echo $form->textField($model,'address',array('size'=>50,'maxlength'=>50, 'style'=>"width:500px;")); ?>							    
			    <?php echo $form->error($model, 'address'); ?>
			    </div>
			</div>		    
</section>
<section id="driverinfo" class="driverinfo">
    <div class="row-fluid">
	    <div class="span12">
		<div class="page-header">
		<h2>2. 个人信息（必填）</h2>
		</div>
	</div>
	</div>
		<div class="row-fluid">
			<div class="span4">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
			<?php echo $form->error($model,'name',array('class','alert alert-error')); ?>
			</div>
		
			<div class="span4">
				<?php echo $form->labelEx($model,'gender'); ?>
				<?php echo $form->dropDownList($model,
								'gender',
								$gender,
					array()
				); ?>
				<?php echo $form->error($model,'gender'); ?>
			</div>		
	
			<div class="span4">
				<?php echo $form->labelEx($model,'age'); ?>
				<?php echo $form->textField($model,'age',array('size'=>20,'maxlength'=>20)); ?>
				<?php echo $form->error($model,'age'); ?>
			</div>
		</div>
		<div class="row-fluid">
		<div class="span4">
			<?php echo $form->labelEx($model,'domicile'); ?>
			<?php echo $form->textField($model,'domicile',array('size'=>30,'maxlength'=>30)); ?>
			<?php echo $form->error($model,'domicile'); ?>			
			<label for="assure">			
			<?php echo $form->checkBox($model,'assure'); ?>
			我有担保人
			</label>			
		</div>
	
		<div class="span4">
			<?php echo $form->labelEx($model,'mobile'); ?>
			<?php echo $form->textField($model,'mobile',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'mobile'); ?>
		</div>
	
		<div class="span4">
			<?php echo $form->labelEx($model,'id_card'); ?>
			<?php echo $form->textField($model,'id_card',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'id_card'); ?>
		</div>
		</div>
		<div class="row-fluid">					
		<div class="span4">
			<?php echo $form->labelEx($model,'marry'); ?>
			<?php echo $form->dropDownList($model,
							'marry',
							$marry, array()); ?>
			<?php echo $form->error($model,'marry'); ?>

		</div>
	
		<div class="span4">
			<?php echo $form->labelEx($model,'political_status'); ?>
			<?php echo $form->dropDownList($model,
							'political_status',
							$political_status, array()); ?>
			<?php echo $form->error($model,'political_status'); ?>
		</div>		

		<div class="span4">
			<?php echo $form->labelEx($model,'edu'); ?>
			<?php echo $form->dropDownList($model,
							'edu',
							$edu, array()); ?>
			<?php echo $form->error($model,'edu'); ?>
		</div>	
		</div>
		<div class="row-fluid">								
		<div class="span12">
			<?php echo $form->labelEx($model,'pro'); ?>
			<?php echo $form->textField($model,'pro',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'pro'); ?>
		</div>	
		</div>	
		
		<div class="row-fluid">								
		<div class="span12">
			<?php echo $form->labelEx($model,'src'); ?>
			<?php 
			$src = Dict::items('recruitment_src');
			
			ksort($src);
			echo $form->dropDownList($model,
							'src',
							$src, array()); ?>
			<?php echo $form->error($model,'src'); ?>
			<?php echo $form->textField($model,'other_src',array('size'=>20,'maxlength'=>20,'style'=>'display:none'));?>
			<?php echo $form->error($model,'other_src'); ?>
		</div>	
		</div>			
</section>
<section id="certificateinfo" class="certificateinfo">
	<div class="row-fluid">								
	<div class="span12">
	<div class="page-header">
		<h2>3. 资格信息（必填）</h2>
	</div>
	</div>
	</div>
	<div class="row-fluid">								
	<div class="span4">
		<?php echo $form->labelEx($model,'driver_type'); ?>
		<?php echo $form->dropDownList($model,
						'driver_type',
						$driver_type, array()); ?>
		<?php echo $form->error($model,'driver_type',array('class','alert alert-error')); ?>
	</div>

	<div class="span4">
			<?php echo $form->labelEx($model,'driver_card'); ?>
			<?php echo $form->textField($model,'driver_card',array('size'=>50,'maxlength'=>50)); ?>
			<?php echo $form->error($model,'driver_card'); ?>
		</div>	
	
	<div class="span4">
	<?php echo $form->labelEx($model,'driver_year'); ?>	
<?php
	$driver_year = $dataZhaopin['driver_year'] ? $dataZhaopin['driver_year'] : '';
	Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
	$this->widget('CJuiDateTimePicker', array (
		'name'=>'DriverRecruitment[driver_year]', 
//		'model'=>$model,  //Model object
		'value'=>$driver_year, 
		'mode'=>'date',  //use "time","date" or "datetime" (default)
		'options'=>array (
			'dateFormat'=>'yy-mm-dd'
		),  // jquery plugin options
		'language'=>'zh',
	));
?>
			<?php echo $form->error($model,'driver_year'); ?>
	</div>
	</div>
	<div class="row-fluid">					
	<div class="span12">
			<div >熟练驾驶车型*</div>
<?php
echo $form->checkBoxList($model,'driver_cars',$arrCars,array('separator'=>'','template'=>'<div  style="float:left">{input} {label}</div>','labelOptions'=>array('style'=>'display:inline;')), array());
?>
<?php
echo CHtml::checkBox('checkallcars','', array ("class" => "checkallcars"));
?>
<label style='display:inline;' for='checkallcars'>全选</label>
<script language="javascript">

  $(document).ready(function(){
    // powerful jquery ! Clicking on the checkbox 'checkAll' change the state of all checkbox  
    $('.checkallcars').click(function () {
      $("input[id^='DriverRecruitment_driver_cars']:not([disabled='disabled'])").attr('checked', this.checked);
    });
  });
</script>
</div>
</div>
</section>
<section id="otherinfo" class="otherinfo">
	<div class="row-fluid">	
	<div class="span12">
	<div class="page-header">
		<h2>4.其他信息（必填）</h2>
	</div>
	</div>
	</div>
	<div class="row-fluid">	
		<div class="span4">
			<?php echo $form->labelEx($model,'contact'); ?>
			<?php echo $form->textField($model,'contact',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'contact'); ?>	
		</div>
		<div class="span4">
			<?php echo $form->labelEx($model,'contact_phone'); ?>
			<?php echo $form->textField($model,'contact_phone',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'contact_phone'); ?>	
		</div>
		<div class="span4">
			<?php echo $form->labelEx($model,'contact_relate'); ?>
			<?php echo $form->textField($model,'contact_relate',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'contact_relate'); ?>	
		</div>				
	</div>
</section>
<section id="experience" class="experience">
	<div class="row-fluid">
	<div class="span12">	
	<div class="page-header">
		<h2>5.代驾经验（选填）</h2>
	</div>
	</div>
	</div>		
	<div class="row-fluid">	
		<div class="span12">
			<?php echo $form->labelEx($model,'experience'); ?>
			<?php echo $form->textArea($model,'experience', array('class'=>'span12','style' => 'height: 200px;')); ?>
			<?php echo $form->error($model,'experience'); ?>	
		</div>			
	</div>
</section>
<section id="submit" class="submit">
	<div style='margin:0 auto;width:400px'>
		<?php echo CHtml::submitButton('修改报名表',array('class'=>'span3 btn-large btn-success btn-block')); ?>
	</div>
</section>
<?php $this->endWidget(); ?>
<script language="javascript">
	$(function(){
		<?php if($model->src=='8'){?>
			$("#DriverRecruitment_other_src").show();
		<?php }?>
		
		$("#DriverRecruitment_src").change(function(){
			
			$("#DriverRecruitment_other_src").hide();
			
			if($("#DriverRecruitment_src").val()=='8')
			{
				$("#DriverRecruitment_other_src").show();
			}else{
				$("#DriverRecruitment_other_src").hide();
			}
		});
	});
</script>