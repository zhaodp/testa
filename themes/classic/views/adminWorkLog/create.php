<?php
    if(Yii::app()->user->hasFlash('hasCreate')){
?>
<div class="alert alert-danger fade in">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <?php echo Yii::app()->user->getFlash('hasCreate'); ?>
</div>
<?php
    }
?>
<h1>提交工作日志</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<script type='text/javascript'>
$('#admin-work-log-form').submit(function(){
    var date = $('#AdminWorkLog_work_date').val();
    if(date == ''){
	alert('请选择工作日期');
	return false;
    }
    return true;
})

</script>
