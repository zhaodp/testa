<div class="form span6">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'customer-complain-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
        'htmlOptions' => array('class' => "form-horizontal"),
            ));
    ?>
    <p class="note">Fields with <span class="required">*</span> are required.</p>
    <?php echo $form->errorSummary($model); ?>
        <fieldset>
            <legend></legend>
            <?php $openCity = RCityList::model()->getOpenCityList();?>
            <div class="control-group">
                <?php echo $form->labelEx($model, 'city_id', array('class' => 'control-label')); ?>
                <div class="controls">
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
                        'name' => 'CustomerComplain[city_id]',
                        'value' => $city_id,
                        'type' => 'modal',
                        'htmlOptions' => array(
                            'style' => 'width: 134px; cursor: pointer;',
                            'readonly' => 'readonly',
                        )
                    ));
                ?>                   
                 <?php echo $form->error($model, 'city_id'); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo $form->labelEx($model, 'name', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20)); ?>
                    <?php echo $form->error($model, 'name'); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo $form->labelEx($model, 'phone', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textField($model, 'phone', array('size' => 20, 'maxlength' => 20)); ?>
                    <?php echo $form->error($model, 'phone'); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo $form->labelEx($model, 'customer_phone', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textField($model, 'customer_phone', array('size' => 20, 'maxlength' => 20)); ?>
                    <?php echo $form->error($model, 'customer_phone'); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo $form->labelEx($model, 'driver_id', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textField($model, 'driver_id'); ?>
                    <?php echo $form->error($model, 'driver_id'); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo $form->labelEx($model, 'driver_phone', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textField($model, 'driver_phone', array('size' => 20, 'maxlength' => 20)); ?>
                    <?php echo $form->error($model, 'driver_phone'); ?>
                </div>
            </div>
            <div class="control-group">
                    <?php echo $form->labelEx($model, 'service_time', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php
                        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                        $this->widget('CJuiDateTimePicker', array(
                            'name' => 'service_time',
                            'value' => '',
                            'mode' => 'date', //use "time","date" or "datetime" (default)
                            'options' => array(
                                'dateFormat' => 'yy-mm-dd'
                            ), // jquery plugin options
                            'language' => 'zh',
                            'htmlOptions' => array(
                                'placeholder' => "使用代驾时间",
                            ),
                        ));
                    ?>
                    <?php echo $form->error($model, 'service_time'); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo $form->labelEx($model, 'complain_type', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php
                        echo CHtml::dropDownList('complain_maintype', $model->complain_type, $typelist, array(
                            'ajax' => array(
                                'type' => 'POST', //request type
                                'url' => Yii::app()->createUrl('complain/getsubtype'),
                                'update' => '#sub_type', //selector to update
                                'data' => array('complain_maintype' => 'js:$("#complain_maintype").val()')
                            ))
                        );
                    ?>
                    <?php echo CHtml::dropDownList('sub_type', '-1', array('-1' => '全部')); ?>
                    <?php echo $form->error($model, 'complain_maintype'); ?>
                </div>
            </div>
            <div class="control-group">
                    <?php echo $form->labelEx($model, 'source', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->dropDownList($model, 'source', array('' => '全部') + CustomerComplain::$source); ?>
                    <?php echo $form->error($model, 'source'); ?>
                </div>
            </div>
            <div class="control-group">
                    <?php echo $form->labelEx($model, 'detail', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?php echo $form->textArea($model, 'detail', array('class' => 'input-xlarge', 'rows' => '3')); ?>
                    <?php echo $form->error($model, 'detail'); ?>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <?php echo CHtml::submitButton('保存', array('class' => 'btn btn-large btn-primary', 'type' => 'button', 'name' => 'save')); ?>
                </div>
            </div>
        </fieldset>
    <?php $this->endWidget(); ?>
</div>