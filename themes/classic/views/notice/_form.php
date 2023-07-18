<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'notice-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>
<div class="grid-view">
	<p class="note">标识 <span class="required">*</span>的项目必填。</p>

	<label>发布内容</label>
	<?php echo $form->dropDownList($model,'category',array('0'=>'公告','1'=>'培训教材')); ?>
	
	<?php
		$city_id = Yii::app()->user->city;
		if ($city_id == 0){
			?>
			<div id='city_div'>
			<?php 
		} else {
			?>
			<div id='city_div' style='display:none;'>
			<?php 
		}
	?>
	
	<label>城市</label>
	<input type="checkbox" name="all" id="che_all" value="1">&nbsp;&nbsp;全选
	<br><br>
	<?php
		$city = explode(',', $model->city_id);
		$citys = Dict::items('city');
		unset($citys[0]);
		foreach ($citys as $key=>$item){
					echo CHtml::checkBox("city[]",false,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
		}
		
	?>
	
	
	</div>
	<br/>
	<br/>
	<div id='issue'>
		<label>公告分类</label>
		<?php echo $form->dropDownList($model,'class',array('0'=>'全部','1'=>'培训','2'=>'制度','3'=>'奖惩','4'=>'通知'))?>
		
		<label>是否置顶</label>
		<?php echo $form->dropDownList($model,'is_top',array('0'=>'否','1'=>'是'));?>
	</div>
	
	<?php
		if ($model->is_top == '0' || !isset($model->is_top)){
			?>
	<div id='hiden_div' style='display:none;'>		
	<?php 
		} else {
			?>
	<div id='hiden_div'>		
	<?php
		}
	?>
		<label>置顶有效期至</label>
		<input type='radio' name='valid' checked='checked' value='3'/>3天&nbsp;&nbsp;
		<input type='radio' name='valid' value='7'/>7天
	</div>
	<br/>
	<label>标题</label>
	<?php echo $form->textField($model,'title',array('maxlength'=>100,'class'=>'span12')); ?>
	
	<div id='validity'>
	<label>公告有效期至</label>
	<?php
		Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
		$this->widget('CJuiDateTimePicker', array (
			'name'=>'Notice[deadline]',
			'model'=>$model,
			'value'=>substr($model->deadline, 0,10), 
			'mode'=>'date',  //use "time","date" or "datetime" (default)
			'options'=>array (
				'dateFormat'=>'yy-mm-dd'
			),  // jquery plugin options
			'language'=>'zh'
		));
	?>
	</div>
	<label>内容</label>
    <p>图片上传支持格式"jpg","bmp","gif","png"</p>
	<?php $this->widget('application.extensions.ckeditor.CKEditor', array(
			    'model'=>$model,
			    'attribute'=>'content',
			    'language'=>'zh-cn',
			    'editorTemplate'=>'public',
			    'options' => array(
			    	'height' => '300px',
					'filebrowserImageUploadUrl'=>'index.php?r=image/imgupload&type=img&base_path=notice',
					),   
				));
	?>

	<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存',array('class'=>'btn-large')); ?>
	<?php echo CHtml::Button('取消',array('onclick'=>'window.open("'.Yii::app()->createUrl('Notice/admin').'","_self","param")','class'=>'btn-large'));?>
	
<?php $this->endWidget(); ?>
</div>
</div>
<script language='javascript' type='text/javascript'>
$(document).ready(function(){
	var city = '<?php echo $model->city_id;?>';
	var city_arr = city.split(',');
	//alert(city);
	if(city_arr.length>0&&city!=0){
		
		for(i=0;i<city_arr.length;i++){
			$(".city_id").eq(city_arr[i]-1).attr("checked","true");
		}
		if(city=='1,2,3,4,5,6,7,8,9,10,11,12,13'){
			$("#che_all").attr("checked","true");
		}
	}

	
	var user_city = '<?php echo Yii::app()->user->city;?>';
	$('#Notice_is_top').change(function(){
		var select_val = $('#Notice_is_top').val();
		if(select_val == '0'){
			$('#hiden_div').hide();
		}else{
			$('#hiden_div').show();
		}
	});
	$('#Notice_category').change(function(){
		var sel_val = $('#Notice_category').val();
		if(sel_val == '1'){
			$('#issue').hide();
			$('#city_div').hide();
			$('#validity').hide();
		}else{
			$('#issue').show();
			if(user_city=='0'){
				$('#city_div').show();
			}
			$('#validity').show();
		}
	});
	$('input[type="submit"]').click(function(){
		var reg = /^\d{4}\-\d{2}-\d{2}$/;
		if($("#Notice_deadline").val()==''){
			alert('公告有效期必填');
			return false;
		}else if(!reg.test($("#Notice_deadline").val())){
			alert("请输入正确的日期");
			return false;
		}
        $('#notice-form').submit();
        $('input[type="submit"]').attr('disabled',true);
	});

})
$('#che_all').click(function(){
	if($(this).attr("checked")){
		$("input[name='city[]']").each(function(){
				$(this).attr("checked","true")
		});
	}else{
		$("input[name='city[]']").each(function(){
				$(this).removeAttr("checked");
		});
	}
	
	
})
</script>