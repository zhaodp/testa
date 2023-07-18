<style>
.row{ margin-left:0px;}
</style>
<?php
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0; 
// echo $id;exit;
?>
<h1>修改司机状态</h1>
<br>
状态信息
<hr>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-recruitment-complete_driver-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
    <input type='hidden' name='id' value='<?php echo $id ?>'> 
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('readonly'=>true)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

    <div class="row">
		<?php
            $status = DriverRecruitment::$status_dict;
            echo CHtml::label('状态选择','status');
            echo CHtml::dropDownList('status',
                        $model->status,
                        $status,
                array('class'=>'width:120px')
            ); 
        ?>  
	</div>
    <div class="row">
        <?php
        $road_new = DriverRecruitment::$road_dict;
        echo CHtml::label('路考状态road_new','road_new');
        echo CHtml::dropDownList('road_new',
            $model->road_new,
            $road_new,
            array('class'=>'width:120px')
        );
        ?>
    </div>
    <div class="row">
        <?php
        $exam = DriverRecruitment::$exam_dict;
        echo CHtml::label('在线考核exam','exam');
        echo CHtml::dropDownList('exam',
            $model->exam,
            $exam,
            array('class'=>'width:120px')
        );
        ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('保存',array('name' => 'save')); ?>
		<?php //echo CHtml::submitButton('保存并签约', array('name' => 'saveEntry')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
			
			