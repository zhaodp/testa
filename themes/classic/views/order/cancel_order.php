<?php
$this->breadcrumbs=array(
        'cancel_order'=>array('index'),
        'Update',
);
?>

<h1>销单原因</h1>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'cancel-order-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>
    <div class="row-fluid">
        <div class="span4">
	    <?php echo CHtml::hiddenField('id',$id);  ?>
            <div>
                <?php if($status == '2' || $status == '3'){
		          echo CHtml::radioButtonList('cancel_type', '1',
                            array('1' => '用户取消'),
                            array('separator'=> '',
                                  'labelOptions' => array('style' => 'display:inline')));
		      }else{
			echo CHtml::radioButtonList('cancel_type', '1',
                            array('1' => '用户取消','2' => '司机取消'),
                            array('separator'=> '&nbsp;&nbsp;&nbsp;',
                                  'labelOptions' => array('style' => 'display:inline')));
		      }
			
		?>
		<?php echo CHtml::textArea('cancel_desc','',array('class'=>'input-xlarge','rows'=>'6','style'=>'width: 360px;'));?>
            </div>
           <div class="span2">
              <?php echo CHtml::submitButton('确认', array('class' => 'btn btn-success btn-block')); ?>
           </div>
    </div>

    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$('#cancel-order-form').submit(function(){    
    if($('#cancel_desc').val() == ''){
	alert('请输入销单原因');
	return false;
    }else{
      var order_id = $("#id").val();
      $.ajax({
        'url':'<?php echo Yii::app()->createUrl('/order/judgeCancel');?>',
        'data':'id='+order_id,
        'type':'get',
        'success':function(data){
            if(data != ''){
                var code = data.split(":")[0];
                var mess = data.split(":")[1];
                if(code != '0'){//不允许销单
                    alert(mess);
		    return false;
                }else{
		    return true;
                }
            }
         },
        'cache':false
      });
    }
});

</script>                    
