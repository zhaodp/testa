<?php
/* @var $this QuestionnaireController */
/* @var $model Questionnaire */

$this->breadcrumbs=array(
	'Questionnaires'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);
?>
<div style="width:700px; margin:0 auto;">
<hr/>
<p>
	客户姓名：<?php echo $model->name;?>&nbsp;&nbsp;&nbsp;
	客户电话：<?php echo $model->phone;?>&nbsp;&nbsp;&nbsp;
	<?php if(isset($e_info)){?>最后使用日期：<?php echo date('Y-m-d H:i',$e_info->booking_time);?>&nbsp;&nbsp;&nbsp;
	代驾次数：<?php echo $e_info->charge;?>&nbsp;&nbsp;&nbsp;
	消费金额：<?php echo $e_info->income;?>&nbsp;&nbsp;&nbsp;
	<?php }?>
</p>
<p>
	<?php if(isset($e_info)){?>
	代驾司机：<?php echo $e_info->driver;?>&nbsp;&nbsp;&nbsp;
	使用代驾地区：<?php echo Dict::item('city', $e_info->city_id);?>&nbsp;&nbsp;&nbsp;
	代驾地点：<?php echo $e_info->location_start;?>&nbsp;&nbsp;&nbsp;
	<?php }?>
	<?php if(isset($criteria_m_info)){?>
	最后评论:<?php echo $criteria_m_info->comments;?>
	<?php }?>
	
	<?php echo CHtml::link("空号", "javascript:void(0);", array("id"=>"customer_$model->id","onclick"=>"{ajaxMissed($model->id,5);}"));?>
	<?php echo CHtml::link("未接通", "javascript:void(0);", array("id"=>"customer_$model->id","onclick"=>"{ajaxMissed($model->id,4);}"));?>
</p>
	<?php if(isset($e_infoMsg)){?>
<p>======</p>
<p>
该地区最后使用日期：<?php echo date('Y-m-d H:i',$e_infoMsg->booking_time);?>&nbsp;&nbsp;&nbsp;
	代驾次数：<?php echo $e_infoMsg->charge;?>&nbsp;&nbsp;&nbsp;
	消费金额：<?php echo $e_infoMsg->income;?>&nbsp;&nbsp;&nbsp;
</p>
<p>
	代驾司机：<?php echo $e_infoMsg->driver;?>&nbsp;&nbsp;&nbsp;
	代驾地区：<?php echo Dict::item('city', $e_infoMsg->city_id);?>&nbsp;&nbsp;&nbsp;
	代驾地点：<?php echo $e_infoMsg->location_start;?>
</p>
	<?php }?>
<hr/>

<div style="width:660px; margin:0 auto;">
<?php echo $this->renderPartial('_form', array('model'=>$model,'customer_list'=>$customer_list)); ?>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		showorhide('0');
		$(".status").change(function(){
			var selectestatus = $("input[name='CustomerVisit[status]']:checked").val();
			showorhide(selectestatus);
		});
		$(".status_date").change(function(){
			var status_date = $("input[name='CustomerVisit[date]']:checked").val();
			if(status_date == '1'){
				$("#date_div").hide();
				$("#Questionnaire_again_time").val("");
			}else
				$("#date_div").show();
		});
		$(".item1").change(function(){
			var item0 = $("input[name='item1']:checked").val();
			if(item0 == '1'){
				 $("#item2").hide();
				 $("#item3").show();
			}else{
				$("#item2").show();
				$("#item3").show();
			}
		});
		$(".item2").change(function(){
			var item2 = $("input[name='item2']:checked").val();
			item2 == '4'?$("#item2txt").show():$("#item2txt").hide();
		});
		$(".item3").change(function(){
			var item2 = $("input[name='item3']:checked").val();
			if(item2 <= '1'){
				 $("#item4").show();
				 $("#item5").hide();
			}else{
				$("#item4").hide();
				$("#item5").show();
			}
		});
		$(".item4").change(function(){
			var item3 = $("input[name='item4']:checked").val();
			if(item3 == '3'){
				 $("#item5").show();
				 $("#item4txt").hide();
			}else if(item3 == '4'){
				$("#item4txt").show();
				$("#item5").hide();
			}else{
				$("#item5").hide();
				$("#item4txt").hide();
			}
		});
		$(".item5").change(function(){
			var item5 = $("input[name='item5']:checked").val();
			item5 == '7'?$("#item5txt").show():$("#item5txt").hide();
		});
	});
	function showorhide(status){
		switch (status){
			case '3':	
				$("#hf").show();
				$("#list_hf").hide();
				break;
			case '2':
				$("#hf").show();
				$("#list_hf").hide();
				break;
			default:
				$("#hf").hide();
				$("#list_hf").show();
		}
	}
	function ajaxMissed(id,status){
		var data = 'id='+id+'&status='+status;
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/customer/ajaxMissed');?>',
			'data':data,
			'type':'get',
			'success':function(data){
				parent.closedDialogAjax("update_exam_dialog");
			}		
		});
	}
</script>