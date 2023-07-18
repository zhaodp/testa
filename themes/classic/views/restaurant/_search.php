<?php
/* @var $this RestaurantController */
/* @var $model Restaurant */
/* @var $form CActiveForm */
?>

<div class="well span12">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>

    <div class="span3 row">
        <?php echo $form->label($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
    </div>

    <div class="span2 row" style="margin-right:-120px;">
        <?php echo $form->label($model,'cost',array('for'=>'Restaurant_cost_min')); ?>
        <?php echo $form->textField($model,'cost_min',array('style'=>'width:70px;')); ?>
    </div>

    <div class="span2 row" style="margin-right:20px;">
        <?php echo CHtml::label('&nbsp;','Restaurant_cost_max'); ?>
        --<?php echo $form->textField($model,'cost_max',array('style'=>'width:70px;')); ?>
    </div>

    <div class="span2 row" style="margin-right:-120px;">
        <?php echo $form->label($model,'tables',array('for'=>'Restaurant_table_min')); ?>
        <?php echo $form->textField($model,'table_min',array('style'=>'width:70px;')); ?>
    </div>

    <div class="span2 row" style="margin-right:20px;">
        <?php echo CHtml::label('&nbsp;','Restaurant_table_max'); ?>
        --<?php echo $form->textField($model,'table_max',array('style'=>'width:70px;')); ?>
    </div>

    <div class="span3 row">
        <?php echo $form->label($model,'city'); ?>
        <?php echo $form->dropDownList($model,'city', Restaurant::model()->getCities(), array('empty'=>'请选择','ajax'=>array(
                        'type'=>'get',
                        'url'=>Yii::app()->createUrl('restaurant/admin'),
                        'update'=>'#Restaurant_district',
                        'data'=>array('areaCity'=>'js:this.value','areaAjax'=>'1'),
                    ),'onchange'=>'js:$("#Restaurant_zone").html($zoneOptions)'));
        ?>
    </div>

    <div class="span3 row">
        <?php echo $form->label($model,'district'); ?>
        <?php echo $form->dropDownList($model,'district', array(), array('empty'=>'请选择','ajax'=>array(
                        'type'=>'get',
                        'url'=>Yii::app()->createUrl('restaurant/admin'),
                        'update'=>'#Restaurant_zone',
                        'data'=>array('areaCity'=>'js:$("#Restaurant_city").val()','areaDistrict'=>'js:this.value','areaAjax'=>'1'),
                    )));
        ?>
    </div>

    <div class="span3 row">
        <?php echo $form->label($model,'zone'); ?>
        <?php echo $form->dropDownList($model,'zone',array(), array('empty'=>'请选择')); ?>
        <script>var $zoneOptions = $('#Restaurant_zone').html();</script>
    </div>

    <div class="span3 row">
        <?php echo $form->label($model,'user_id'); ?>
        <?php echo $form->textField($model,'user_id'); ?>
    </div>

    <div class="span3 row">
        <?php echo CHtml::label('有无竞品','Restaurant_competition'); ?>
        <?php echo $form->dropDownList($model,'competition',RestaurantAttr::$has_competition,array('empty'=>'请选择')); ?>
    </div>

    <div class="span3 row">
        <?php echo CHtml::label('是否已进店','Restaurant_materials'); ?>
        <?php echo $form->dropDownList($model,'materials',RestaurantAttr::$has_materials,array('empty'=>'请选择')); ?>
    </div>

    <div class="span3 row buttons">
        <br>
        <?php echo CHtml::submitButton('搜索',array('class'=>'btn span3')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->