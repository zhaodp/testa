<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl("crm/createTicketClass"),'method' => 'get',)); ?>
    <div class="span12">
	
	<div class="span3">
          <label>工单类别</label> 
         <?php  
                $cates = Dict::items('ticket_category');
                echo $form->dropDownList($model,'code',$cates,array());
         ?> 
       </div>

       <div class="span3">
            <label>工单分类</label>
            <?php echo CHtml::textField('class'); ?>
       </div>

       <div class="span3">
	     <label>&nbsp;</label>
	      <a class="btn btn-info" href="javascript:;" onclick='checkClass()'>保存</a>
       </div>

     </div>
    <?php $this->endWidget(); ?>

</div>
