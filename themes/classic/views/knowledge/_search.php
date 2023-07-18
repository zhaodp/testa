<?php
/* @var $this KnowledgeController */
/* @var $model Knowledge */
/* @var $form CActiveForm */
?>

<div class="well form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
    <div class="row-fluid">

        <div class="span3">
            <?php echo $form->label($model, 'title'); ?>
            <?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 100)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'keywords'); ?>
            <?php echo $form->textField($model, 'keywords', array('size' => 40, 'maxlength' => 40)); ?>
        </div>

        <div class="span3">
            <?php
            $type = Dict::items('knowledge_type');
            array_unshift($type,'全部');
            echo $form->label($model, 'typeid'); ?>
            <?php echo $form->dropDownList($model, 'typeid',$type); ?>
        </div>


        <div class="span3">
<!--            --><?php
//            $cat = Dict::items('knowledge_cat');
//            array_unshift($cat, '全部');
//            echo $form->label($model, 'catid'); ?>
<!--            --><?php //echo $form->dropDownList($model, 'catid', $cat); ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model, 'status'); ?>
            <?php echo $form->dropDownList($model, 'status', array('0' => '全部', '1' => '待审核', '2' => '已审核')); ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'city_id'); ?>
            <?php
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
                'name' => 'Knowledge[city_id]',
                'value' => $city_id,
                'type' => 'modal',
                'htmlOptions' => array(
                    'style' => 'width: 85px; cursor: pointer;',
                    'readonly' => 'readonly',
                ),
                'defaultText'=>'全国',
            ));
            ?>
        </div>

        <div class="span3 buttons">
            <?php echo $form->label($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton('Search',array('class' => 'btn')); ?>
        </div>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->