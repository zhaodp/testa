<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'admin-user-form',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'method'=>'post',
)); ?>
    <ul class="thumbnails">
        <li class="span5" >
            <div class="thumbnail">
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo CHtml::label('脚本','sql');?>
                        <?php echo CHtml::textArea('textsql','',array('style'=>'width: 500px; height: 160px;')); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo CHtml::submitButton('RUN',array('class'=>'btn btn-large btn-primary')); ?>
                    </div>
                </div>
            </div>
        </li>
        <li class="span5" >
            <?php print_r($result); ?>
        </li>

    </ul>
    <?php $this->endWidget(); ?>

</div>