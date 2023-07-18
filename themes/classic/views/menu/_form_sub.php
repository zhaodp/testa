<?php
/* @var $this MenuController */
/* @var $model Menu */
/* @var $form CActiveForm */
?>

<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'menu-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'is_show'); ?>
        <?php echo $form->dropDownList($model,'is_show', array('1'=>'是','0'=>'否'));?>
		<?php echo $form->error($model,'is_show'); ?>

	</div>
    <div class="row">
        <?php echo $form->labelEx($model,'is_target'); ?>
        <?php echo $form->dropDownList($model,'is_target', array('1'=>'是','0'=>'否'));?>
        <?php echo $form->error($model,'is_target'); ?>

    </div>
    <?php
    if(!empty($parents)){
        echo '<div class="row">';
        echo $form->labelEx($model,'parentid');
        echo $form->dropDownList($model,'parentid', $parents);
        echo $form->error($model,'parentid');
        echo '</div>';
    }

    ?>


	<div class="row">
		<?php echo $form->labelEx($model,'position'); ?>
		<?php echo $form->textField($model,'position'); ?>
		<?php echo $form->error($model,'position'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '新建菜单' : '保存菜单'); ?>
	</div>
    <?php ?>
    <div class="row">
        <h4>功能模块列表：</h4>
        <?php

        foreach( $action_info as $g => $item_mods )
        {
            //echo '<legend>'.CHtml::label($g, null, array ( 'style'=>'display:inline')).'</legend>';
            $all_select = " ";
            echo '<legend><div style="margin-bottom:0px;width:400px;padding:4px 0px 0px 8px" class="alert alert-success">'.CHtml::label($g.$all_select, null)." </div></legend>";
            echo "<div id = '{$g}' >";

            $i=0;
            foreach($item_mods as $item) {
                if($i==0){
                    echo "&nbsp;&nbsp;";
                    $i++;
                }
                echo '<label class="checkbox inline">';
                echo CHtml::radioButton('AdminAction[id]',  $item['id']==$model->roles_id, array (
                    'id'=>'AdminGroup_mods_' . $item['id'],
                    'value'=>$item['id'],
                    'name'=>$item['controller']."_".$item['action'],
                    'separator'=>''));
                echo $item['name']."({$item['controller']}/{$item['action']})".'</label>';
            }
            echo "</div>";
        }

        ?>

    </div>
<?php $this->endWidget(); ?>

</div><!-- form -->
