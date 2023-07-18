<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl($this->route),'method' => 'post',)); ?>
    <div class="span12">
        <div class="span3">
            <label>合作商家名称</label>
            <?php echo CHtml::textField('name',isset($name)?$name:''); ?>
        </div>

        <div class="span3">
            <?php echo $form->labelEx($model, 'shop_type'); ?>
            <?php $channelList = Dict::items('bonus_shop_type');
            $channelList = array('99' => '请选择') + $channelList;
            echo $form->dropDownList($model, 'shop_type', $channelList); ?>
            <?php echo $form->error($model, 'shop_type'); ?>
        </div>
        <div class="span3">
	    <label>&nbsp;</label>
            <?php echo CHtml::submitButton('搜索', array('class'=>'btn btn-primary','id'=>'search-button')); ?>
        </div>
  	<div class="span3">
             <label>&nbsp;</label>
		<?php  echo CHtml::link('新增商家', 'javaScript:void(0);', array('class' => 'btn btn-primary','onClick' => 'createDialogdivInit(\'' . Yii::app()->createUrl("bonusMerchants/create"). '\')')); ?>
          </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
