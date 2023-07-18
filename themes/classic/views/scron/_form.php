<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'crontab-form',
        'enableAjaxValidation'=>false,
		'errorMessageCssClass'=>'alert alert-error'
)); ?>
		<p class="note">带 <span class="text-error">*</span> 是必填的.</p>
		<?php echo $form->errorSummary($model); ?>

	    <div class="row-fluid">
	        <div class="span3">
				<?php echo $form->labelEx($model,'task'); ?>
				<?php echo $form->textField($model,'task',array('size'=>60,'maxlength'=>255)); ?>
				<?php echo $form->error($model,'task'); ?>
			</div>

            <div class="span2">
                <?php echo $form->labelEx($model,'host'); ?>
                <?php //echo $form->textField($model,'host',array('size'=>60,'maxlength'=>255)); ?>
                <?php echo $form->dropDownList($model,'host',ScronHost::model()->getStartHost())?>
                <?php echo $form->error($model,'host'); ?>
            </div>

            <div class="span2">
                <?php echo $form->labelEx($model,'owner'); ?>
                <?php echo $form->textField($model,'owner',array('size'=>60,'maxlength'=>128)); ?>
                <?php echo $form->error($model,'owner'); ?>
            </div>
		</div>


    <div class="row-fluid">
        <div class="span1">
            <?php echo $form->labelEx($model,'min');?>
            <?php echo $form->textField($model,'min'); ?>
            <?php echo $form->error($model,'min'); ?>
        </div>

        <div class="span1">
            <?php echo $form->labelEx($model,'hour');?>
            <?php echo $form->textField($model,'hour'); ?>
            <?php echo $form->error($model,'hour'); ?>
        </div>

        <div class="span1">
            <?php echo $form->labelEx($model,'day');?>
            <?php echo $form->textField($model,'day'); ?>
            <?php echo $form->error($model,'day'); ?>
        </div>


        <div class="span1">
            <?php echo $form->labelEx($model,'month');?>
            <?php echo $form->textField($model,'month'); ?>
            <?php echo $form->error($model,'month'); ?>
        </div>

        <div class="span1">
            <?php echo $form->labelEx($model,'week');?>
            <?php echo $form->textField($model,'week'); ?>
            <?php echo $form->error($model,'week'); ?>
        </div>


    </div>

	    <div class="row-fluid">

            <div class="span4">
                <?php echo $form->labelEx($model,'command'); ?>
                <?php echo $form->textField($model,'command',array('size'=>100,'maxlength'=>255,'style'=>"width:400px")); ?>
                <?php echo $form->error($model,'command'); ?>
            </div>

	        <div class="span8">
				<?php echo $form->labelEx($model,'logFile'); ?>
				<?php echo $form->textField($model,'logFile',array('size'=>100,'maxlength'=>255,'style'=>"width:300px")); ?>
				<?php echo $form->error($model,'logFile'); ?>
				符合操作系统的文件名，请不要带路径，如果不填写，将以cronId做为文件名
			</div>
		</div>


        <div class="row-fluid">

            <div class="span2">
                <?php echo $form->labelEx($model,'params'); ?>
                <?php echo $form->textField($model,'params',array('size'=>60,'maxlength'=>255))?>
                <?php echo $form->error($model,'params'); ?>
            </div>

            <div class="span4">
                <?php echo $form->labelEx($model,'callback'); ?>
                <?php echo $form->textField($model,'callback',array('style'=>'width:400px'))?>
                <?php echo $form->error($model,'callback'); ?>
            </div>

            <div class="span4">
                <?php echo $form->labelEx($model,'timeout'); ?>
                <?php echo $form->textField($model,'timeout')?>
                <?php echo $form->error($model,'timeout'); ?>
                0为不超时
            </div>
        </div>



		<div class="row-fluid">
			<div class="span3">
				<?php echo $form->labelEx($model,'process'); ?>
				<?php echo $form->textField($model,'process',array('size'=>60,'maxlength'=>255))?>
				<?php echo $form->error($model,'process'); ?>
			</div>

            <div class="span3">
                <?php echo $form->labelEx($model,'isQueue'); ?>
                <?php echo $form->dropDownList($model,'isQueue',array('0'=>'不是队列','1'=>'队列'))?>
                <?php echo $form->error($model,'isQueue'); ?>
            </div>


            <div class="span4">
                <?php echo $form->labelEx($model,'user'); ?>
                <?php echo $form->textField($model,'user',array('value'=>'root'))?>
                <?php echo $form->error($model,'user'); ?>
            </div>

		</div>	

        <div class="row-fluid text-center">
             <div class="margin:0 auto;width:400px;">
			    <?php echo CHtml::submitButton('提交',array('class'=>'btn btn-primary','style'=>'width:200px;height:50px')); ?>
			    <input type="button" class="btn btn-primary" onclick="window.history.back(); return false;" value="返  回" hidefocus style="width:200px;height:50px"/>
		    </div>
       </div>
  <?php $this->endWidget(); ?>

<?php
$model->restDbConnection();
?>
</div>
