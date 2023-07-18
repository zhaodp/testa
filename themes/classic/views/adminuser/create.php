<?php
$this->pageTitle = '新建管理员用户';
?>

<h1>新建管理员用户</h1>
<hr class="divider"/>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<script>
$('body').on('blur','#AdminUser_name',function(){
	var name = $('#AdminUser_name').val();
	if(name){
		var pars = 'name='+ name + '&format=json';
		$.ajax({
			type: 'get',
			url: '<?php echo Yii::app()->createUrl('/adminuser/checkname');?>',
			data: pars,
			dataType : 'json',
			success: function(json){
				if(json == 1){
					alert('此用户名已经存在');
					$('#AdminUser_name').focus();
				};
		}});
	}
});
</script>