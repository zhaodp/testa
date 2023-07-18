<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */

$this->breadcrumbs=array(
	'Driver version'=>array('index'),
	'Create',
);

?>

<h3>创建新的头像效果</h3>

<div class="form span11">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-version-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation' => true,
    'clientOptions' => array(
    'validateOnSubmit' => true  //在这个位置做验证
    ),


)); ?>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'act_name'); ?>
		<?php echo $form->textField($model,'act_name',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'act_name'); ?>
	</div>
	<div class="row">
		 <?php echo "设置城市" ?>
            <input type="checkbox" name="che_all" id="che_all" value="1">&nbsp;全选&nbsp;&nbsp;<input type="checkbox" name="unche_all" id="unche_all" value="1">&nbsp;反选
            <br><br>
            <?php
            $citys = RCityList::model()->getOpenCityList();
            foreach ($citys as $key=>$item){
                $checked = false;
                echo CHtml::checkBox("city[]",$checked,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
            }

            ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'start_time'); ?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'ClientDriverBackground[start_time]', 
				'model'=>$model,  //Model object
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'value'=>'',
				'options'=>array (
					'dateFormat'=>'yy-mm-dd 00:00:00'
				),  // jquery plugin options
				'language'=>'zh',
				'htmlOptions'=>	array('size'=>60,'maxlength'=>100)
			));
		?>
				<?php echo $form->error($model,'start_time'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'end_time'); ?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'ClientDriverBackground[end_time]', 
				'model'=>$model,  //Model object
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'value'=>'',
				'options'=>array (
					'dateFormat'=>'yy-mm-dd 23:59:59'
				),  // jquery plugin options
				'language'=>'zh',
				'htmlOptions'=>	array('size'=>60,'maxlength'=>100)
			));
		?>
				<?php echo $form->error($model,'end_time'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'background_image'); ?>
		<?php echo $form->textField($model,'background_image',array('size'=>255,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'background_image'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'hightlight_image'); ?>
		<?php echo $form->textField($model,'hightlight_image',array('size'=>255,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'hightlight_image'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'normal_image'); ?>
		<?php echo $form->textField($model,'normal_image',array('size'=>255,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'normal_image'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'half_star_image'); ?>
		<?php echo $form->textField($model,'half_star_image',array('size'=>255,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'half_star_image'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'font_color'); ?>
		<?php echo $form->textField($model,'font_color',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'font_color'); ?>
	</div>
	

	<div class="row buttons">
		<?php echo CHtml::submitButton('保存',array('name' => 'save','class'=>'btn')); ?>
	</div>

	<h3>客户端首页司机头像背景效果列表</h3>
<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'tc-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'columns' => array(
        	array(
                'name' => '活动名称',
                'value' => '$data->act_name'
            ),
            array(
                'name' => '状态',
                'value' => '$data->status==0?"正常":"已删除"'
            ),
           array('name'=>'城市','value'=>array($this,'getCityName')),

	    array(
                'name' => '生效时间',
                'value' =>'$data->start_time."~".$data->end_time'
            ),
		array(
                'name' => '背景图片',
                'value' =>'$data->background_image'
            ),
	    array(
                'name' => '星级评价1',
                'value'=>'$data->hightlight_image'
            ),
	    array(
                'name' => '星级评价2',
                'value' =>'$data->normal_image'
            ),
	    array(
                'name' => '半个星级评价',
                'value' =>'$data->half_star_image'
            ),
	    array(
                'name' => '字体颜色',
                'value' =>'$data->font_color'
            ),
	     array(
                'name' => '发布人',
                'value' =>'$data->operator'
            ),
            array(
                'name' => '发布时间',
                'value' =>'$data->create_time'
            ),
	    array(
            	'name' => '操作',
            	'value' => array($this, 'backgroundOpt')
            ),
       ),
    ));?>
<?php $this->endWidget(); ?>
</div><!-- form -->

<script>

$('#che_all').click(function(){
	if($(this).attr("checked")){

	$("input:enabled[name='city[]']").each(function(){
	    $(this).attr("checked","true");
	    $('#unche_all').removeAttr("checked");
	    });
	}//else{
	//       $("input[name='city[]']").each(function(){
	//            $(this).removeAttr("checked");
	//   });
	//   }
	});

$('#unche_all').click(function(){ 
	if($(this).attr("checked")){
	$("input:enabled[name='city[]']").each(function(){
	    if($(this).attr("checked")){
	    $(this).removeAttr("checked");
	    $('#che_all').removeAttr("checked");
	    }else{
	    $(this).attr("checked","true")
	    }
	    }); 
	}//else{
	//     $("input[name='city[]']").each(function(){
	//          $(this).removeAttr("checked");
	// }); 
	// }
	});

$('.city_id').click(function(){
	if(this.checked==false){
	$('#che_all').attr('checked',false);
	}else if($(".city_id:checked").size()==$('.city_id').length){
	$('#che_all').attr('checked',true);
	}
	});


</script>