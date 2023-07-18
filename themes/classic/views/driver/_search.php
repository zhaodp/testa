<div class="well span12" style="border:0px">
<?php 
$rank = isset($_REQUEST['rank'])?$_REQUEST['rank']:'';
?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions'=>array('class'=>'form-inline'),
)); ?>
	<div class="controls controls-row">

		<div class="span2">
		<?php echo $form->label($model,'city_id'); ?>
		<?php
            /*
            $city_list = Dict::items('city');
            $user_city_id = Yii::app()->user->city;
            if ($user_city_id != 0) {
                $city_list = array(
                    $user_city_id => $city_list[$user_city_id]
                );
            }
            echo $form->dropDownList($model,'city_id',$city_list,array('class'=>'span12'));
            */
            $user_city_id = Yii::app()->user->city;

            if ($user_city_id != 0) {
                $city_list = array(
                    '城市' => array(
                        $user_city_id => Dict::item('city', $user_city_id)
                    )
                );
                $city_id = $user_city_id;
            } else {
                $city_id = $model->city_id;
                $city_list = CityTools::cityPinYinSort();
            }
            $this->widget("application.widgets.common.DropDownCity", array(
                'cityList' => $city_list,
                'name' => 'Driver[city_id]',
                'value' => $city_id,
                'type' => 'modal',
                'htmlOptions' => array(
                    'style' => 'width: 134px; cursor: pointer;',
                    'readonly' => 'readonly',
                )
            ));
        ?>
		</div>
		
		<div class="span2">
			<?php echo $form->label($model,'mark'); ?>
			<?php echo $form->dropDownList($model,
						'mark',
						array(
							'' =>'全部',
							'0'=>'正常',
							'1'=>'已屏蔽',
							'3'=>'已解约',
						),
			array('class'=>"span12")
		); ?>
		</div>
		<div class="span2">
			<?php echo $form->label($model,'level'); ?>
			<?php echo $form->dropDownList($model,'level',array('' =>'全部','0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'),array('class'=>"span12")); ?>
		</div>
		
		<div class="span2">
			<?php echo $form->label($model,'year'); ?>
			<?php echo $form->textField($model,'year', array('class'=>"span12")); ?>
		</div>
		<div class="span2">
			<?php echo $form->label($model,'ext_phone'); ?>
			<?php echo $form->textField($model,'ext_phone',array('size'=>60,'maxlength'=>255,'class'=>"span12")); ?>
		</div>		
	</div>
		
	<div class="controls controls-row">
		<div class="span2">
			<?php echo $form->label($model,'user'); ?>
			<?php echo $form->textField($model,'user',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
		</div>
		<div class="span2">
			<?php echo $form->label($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>10,'maxlength'=>255,'class'=>"span12")); ?>
		</div>
		
		<div class="span2">
			<?php echo $form->label($model,'phone'); ?>
			<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20,'class'=>"span12")); ?>
		</div>
	
		<div class="span2">
			<?php echo $form->label($model,'id_card'); ?>
			<?php echo $form->textField($model,'id_card',array('size'=>20,'maxlength'=>20,'class'=>"span12")); ?>
		</div>
		<div class="span2">
			<?php echo $form->label($model,'imei'); ?>
			<?php echo $form->textField($model,'imei',array('size'=>60,'maxlength'=>255,'class'=>"span12")); ?>
		</div>
	
	</div>
	
	<div class="controls controls-row">
		<div class="span2">
			<?php echo $form->label($model,'gender'); ?>
			<?php 
				$genders = Dict::items('gender');
				$gender = array(''=>'请选择');
				foreach($genders as $key=>$value){
					$gender[$key] = $value;
				}
				echo $form->dropDownList($model,
						'gender',
						$gender,
			array('class'=>"span12")
		); ?>
		</div>
		<div class="span2">
			<?php echo $form->label($model,'is_andriod'); ?>
			<?php echo $form->dropDownList($model,
						'car_card',
						array(
							'0' =>'全部',
							'1'=>'MTK',
							'2'=>'Andriod',
						),
			array('class'=>"span12")
		); ?>
		</div>
		<div class="span2">
			<?php echo $form->label($model,'rank'); ?>
			<?php 
			$ranks = array(''=>'请选择司机等级','A'=>'A','B'=>'B','C'=>'C');
			echo $form->dropDownList($model,'rank',
						$ranks,
				array('class'=>'span12')
			); 
			?>
		</div>

                <div class="span2">
        <!--工作方式-->
            <?php echo $form->labelEx($model,'work_type'); ?>
            <?php
            $work_type = Dict::items('work_type');
            echo $form->dropDownList($model,
                'work_type',
                $work_type,
                array("class"=>"span12")
                //array('style'=>'width:135px;')
            );
            echo $form->error($model, 'work_type');
            ?>
        </div>

        <div class="span2">
        <!--担保状态-->
            <?php echo $form->labelEx($model, 'assure');?>
            <?php
                echo $form->dropDownList($model, 'assure', array(''=>'全部')+Driver::$assure_dict, array("class"=>"span12"));
            ?>
        </div>

	</div>

    <div class="controls controls-row">

        <div class="span2" style="width:30%">
            <p style="margin: 0px;"><?php echo $form->label($model,'created'); ?></p>
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>"created_start",
                'options'=>array(
                    'showAnim'=>'fold',
                    'showOn'=>'both',
                    //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                    'buttonImageOnly'=>true,
                    //'minDate'=>'new Date()',
                    'dateFormat'=>'yy-mm-dd',
                    'changeYear'=>true,
                    'changeMonth'=> true,
                ),
                'htmlOptions'=>array(
                    'style'=>'width:123px',
                ),
            ));
            ?>
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>"created_end",
                'options'=>array(
                    'showAnim'=>'fold',
                    'showOn'=>'both',
                    //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                    'buttonImageOnly'=>true,
                    //'minDate'=>'new Date()',
                    'dateFormat'=>'yy-mm-dd',
                    'changeYear'=>true,
                    'changeMonth'=> true,
                ),
                'htmlOptions'=>array(
                    'style'=>'width:123px',
                ),
            ));
            ?>
        </div>
    </div>

	<div class="controls controls-row">
		<?php echo CHtml::submitButton('搜索',array('class'=>'btn span2','style'=>'margin-top:15px;')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->

<script>
    window.onload = function(){
        jQuery('.ui-datepicker-trigger').remove();
    };
</script>