<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'third-user-form',
    'enableAjaxValidation' => false,
));
?>
<?php
Yii::app()->clientScript->registerScriptFile(SP_URL_JS . 'PCASClass.js');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS . 'vue.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(SP_URL_JS . 'select.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile(SP_URL_CSS . 'select.css');
?>

<?php echo $form->errorSummary($model); ?>
    <div class="span3">
        <div class="row-fluid">
            <?php echo $form->labelEx($model, 'name'); ?>
            <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 60)); ?>
            <?php echo $form->error($model, 'name'); ?>
        </div>

        <div class="row-fluid">
            <?php echo $form->labelEx($model, 'initPassword'); ?>
            <?php echo $form->textField($model, 'initPassword', array('size' => 60, 'maxlength' => 60)); ?>
            <?php echo $form->error($model, 'initPassword'); ?>
        </div>

        <div class="row-fluid">
            <?php echo $form->labelEx($model, 'contactName'); ?>
            <?php echo $form->textField($model, 'contactName'); ?>
            <?php echo $form->error($model, 'contactName'); ?>
        </div>

        <div class="row-fluid">
            <?php echo $form->labelEx($model, 'contactPhone'); ?>
            <?php echo $form->textField($model, 'contactPhone'); ?>
            <?php echo $form->error($model, 'contactPhone'); ?>
        </div>

        <div class="row-fluid">
            <?php echo $form->labelEx($model, 'email'); ?>
            <?php echo $form->textField($model, 'email'); ?>
            <?php echo $form->error($model, 'email'); ?>
        </div>

        <div class="row-fluid">
            <select name="province" ></select>
            <select name="city"></select>
            <select name="area" ></select>
            <?php
                $street = isset($model['street']) ? $model['street'] : '';
                $format = '<input type="text" name="street" placeholder="请填写详细地址" value="%s"/>';
                echo sprintf($format, $street);
            ?>

        </div>
        <?php echo $this->renderPartial('select', array('billInstance' => $billInstance)); ?>
        <script>
            (function () {
                <?php
                        $format = 'new PCAS("province", "city", "area", "%s", "%s", "%s")';
                        $province = isset($model['province']) ? $model['province'] : '';
                        $city = isset($model['city']) ? $model['city'] : '';
                        $area = isset($model['area']) ? $model['area'] : '';
                        echo sprintf($format, $province, $city, $area);
                ?>
            })()
        </script>

        <div class="row-fluid">
            <?php echo $form->labelEx($model, 'accessModel'); ?>
            <?php $accessList = ThirdDict::model()->getDict(ThirdDict::DICT_NAME_ACCESS_MODEL);
            $accessList = array('99' => '请选择') + $accessList;
            $accessModel = isset($model['accessModel']) ? $model['accessModel'] : 99;
            echo $form->dropDownList($model, 'accessModel', $accessList, array('class' => 'span9', 'value' => $accessModel)); ?>
            <?php echo $form->error($model, 'accessModel'); ?>
        </div>


<!--        <div class="row-fluid">-->
<!--            --><?php //echo $form->labelEx($model, 'contractNum'); ?>
<!--            --><?php //echo $form->textField($model, 'contractNum'); ?>
<!--            --><?php //echo $form->error($model, 'contractNum'); ?>
<!--        </div>-->


        <div class="row-fluid">
            <?php echo $form->labelEx($model, 'meta'); ?>
            <?php echo $form->textField($model, 'meta'); ?>
            <?php echo $form->error($model, 'meta'); ?>
        </div>


    </div>

    <div class="row-fluid" style="margin-top:20px;">
        <div class="span12">
            <div class="span4"></div>
            <div class="span8">
                <?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存', array('class' => 'btn btn-primary span3', 'id' => 'BonusCodeSbt')); ?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo CHtml::resetButton('取消', array('class' => 'btn btn-danger span3')) ?>
            </div>
        </div>
    </div>
    <!-- form -->

<?php $this->endWidget(); ?>
