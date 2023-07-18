<div class="well span12">
	<div class="row">
		<div class="span3">
			
			<label  >请输入删除理由</label>
			<textarea id="recycleReason" name=""recycleReason"" style="height: 200px;" class="span12"></textarea>
			<input type="hidden" value="<?php echo $ids_str;?>" name="id" id="id"/>
		</div>
	</div>

	<div class="row buttons"><center>
		<?php echo CHtml::button('确定',array("id"=>"submit")); ?>
		<?php echo CHtml::button("取消",array("id"=>"reset"))?></center>
	</div>

</div>
<script>
$(function(){
	$("#reset").click(function(){
		$(window.parent.document).find(".ui-dialog-buttonset button").click();
	});
	$("#submit").click(function(){
		id = $("#id").val();
		recycleReason = $("#recycleReason").val();
		if(id==''||id==0||recycleReason==''){
			alert("参数错误，请刷新页面！");return false;
		}
		//AJAX
		$.ajax({
			'url':'<?php echo Yii::app()->createUrl('/recruitment/batchdelete');?>',
			'data':'ids_str='+id+'&recycleReason='+recycleReason,
			'type':'get',
			'beforeSend':function(){
				$("#submit").css("disabled","disabled");
				$("#reset").css("disabled","disabled");
			},
			'success':function(data){
				if(data==1){
					alert("操作成功。");
					$(window.parent.document).find(".ui-dialog-buttonset button").click();
				}else{
					alert("参数错误，请刷新页面！");
				}				
			},
			'cache':false		
		});
		//AJAX END
	});
});
</script>