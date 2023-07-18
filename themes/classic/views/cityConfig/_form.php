<?php
/* @var $this CityConfigController */
/* @var $model CityConfig */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'city-config-form',
        'enableAjaxValidation' => false,
    )); ?>
    <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">基本信息</a></li>
            <li><a href="#tab2" data-toggle="tab">缴费标准</a></li>
            <li><a href="#tab3" data-toggle="tab">收费标准</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <?php $isupdate = $model->isNewRecord ? false : true;
                    $can_not_update = $isupdate && $model->status == CityConfig::CITY_STATUS_OPEN && strtotime($model->online_time) < time();
                    if($can_not_update){
                        $style = array('size' => 10, 'maxlength' => 10,'readonly'=>'readonly');
                    }
                    else $style = array('size' => 10, 'maxlength' => 10);
                ?>
                <div class="row-fluid">
                    <div class="span3">
                        <label for="CityConfig_city_id">选择城市</label>
                        <?php
                        if(!$can_not_update){
//                        $user_city_id = Yii::app()->user->city;
//
//                        if ($user_city_id != 0) {
//                            $city_list = array(
//                                '城市' => array(
//                                    $user_city_id => Dict::item('city', $user_city_id)
//                                )
//                            );
//                            $city_id = $user_city_id;
//                        } else {
//                            $city_id = $model->city_id;
//                            $city_list = CityTools::cityPinYinSort();
//                        }
//                        $this->widget("application.widgets.common.DropDownCity", array(
//                            'cityList' => $city_list,
//                            'name' => 'CityConfig[city_id]',
//                            'value' => $city_id,
//                            'type' => 'modal',
//                            'htmlOptions' => array(
//                                'style' => 'width: 134px; cursor: pointer;',
//                                'readonly' => 'readonly',
//                            )
//                        ));
                           echo $form->dropDownList($model,
                                'city_id',
                                Common::getUnopenCity(),
                                array( 'maxlength' => 10)
                            );
                        }
                        else{
                            echo $model->city_id;
                        }
                        ?>
                        <?php echo $form->error($model, 'city_id'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'city_name'); ?>
                        <?php echo $form->textField($model, 'city_name', array('size' => 10, 'maxlength' => 10,'readonly'=>'readonly')); ?>
                        <?php echo $form->error($model, 'city_name'); ?>
                    </div>

                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'city_prifix'); ?>
                        <?php echo $form->textField($model, 'city_prifix', array('size' => 10, 'maxlength' => 10,'readonly'=>'readonly')); ?>
                        <?php echo $form->error($model, 'city_prifix'); ?>
                    </div>

                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'bonus_prifix'); ?>
                        <?php echo $form->textField($model, 'bonus_prifix', array('size' => 10, 'maxlength' => 10,'readonly'=>'readonly')); ?>
                        <?php echo $form->error($model, 'bonus_prifix'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'city_level'); ?>
                        <?php echo $form->dropDownList($model, 'city_level', CityConfig::getCityLevel()); ?>
                        <?php echo $form->error($model, 'city_level'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'first_letter'); ?>
                        <?php echo $form->textField($model, 'first_letter', $style); ?>
                        <?php echo $form->error($model, 'first_letter'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'pinyin'); echo '<span style="color:red;">注意：每个拼音间用逗号分隔</span>';?>
                        <?php echo $form->textField($model, 'pinyin',array('size' => 20, 'maxlength' => 20));?>
                        <?php echo $form->error($model, 'pinyin'); ?>
                    </div>
                </div>

                <div class="row-fluid">

                    <div class="span3">
                        <?php echo $form->labelEx($model, 'status'); ?>
                        <?php echo $form->dropDownList($model, 'status', CityConfig::getCityStatus()); ?>
                        <?php echo $form->error($model, 'status'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'captital'); ?>
                        <?php echo $form->dropDownList($model, 'captital', CityConfig::getCityCaptital()); ?>
                        <?php echo $form->error($model, 'captital'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'province_id'); ?>
                        <?php echo $form->dropDownList($model, 'province_id', CityProvince::getProvince()); ?>
                        <?php echo $form->error($model, 'province_id'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'online_time'); ?>
                        <?php
                        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                        $this->widget('CJuiDateTimePicker', array(
                            'name' => 'CityConfig[online_time]',
                            'model' => $model, //Model object
                            'value' => $model->online_time,
                            'mode' => 'datetime', //use "time","date" or "datetime" (default)
                            'options' => array(
                                'dateFormat' => 'yy-mm-dd'
                            ), // jquery plugin options
                            'language' => 'zh',
                            //'htmlOptions' => array('class' => "span12")
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab2">
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'cast_id'); ?>
                        <?php echo $form->dropDownList($model, 'cast_id', Dict::items("city_cast")); ?>
                        <?php echo $form->error($model, 'cast_id'); ?>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'fee_id'); ?>
                        <?php echo $form->dropDownList($model, 'fee_id', Dict::items("city_fee")); ?>
                        <?php echo $form->error($model, 'fee_id'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <!--ajax 请求显示价格表-->
                    <div class="box">
                        <div class="span6 box-content box-double-padding">
                            <div id='result'>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'daytime_price'); ?>
                        <?php echo $form->dropDownList($model, 'daytime_price', CityConfig::getDaytimePrice()); ?>
                        <?php echo $form->error($model, 'daytime_price'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'daytime_cast'); ?>
                        <?php echo $form->dropDownList($model, 'daytime_cast', CityConfig::getDaytimeCast()); ?>
                        <?php echo $form->error($model, 'daytime_cast'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'wash_car_price'); ?>
                        <?php echo $form->dropDownList($model, 'wash_car_price', CityConfig::getWashCarPrice()); ?>
                        <?php echo $form->error($model, 'wash_car_price'); ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab3">
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'pay_money'); ?>
                        <?php echo $form->textField($model, 'pay_money'); ?>
                        <?php echo $form->error($model, 'pay_money'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'screen_money'); ?>
                        <?php echo $form->textField($model, 'screen_money'); ?>
                        <?php echo $form->error($model, 'screen_money'); ?>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <?php echo $form->labelEx($model, 'bonus_back_money');
                        //echo $form->textField($model, 'bonus_back_money');
                        echo $form->dropDownList($model, 'bonus_back_money', CityConfig::getStartPrice());
                        echo $form->error($model, 'bonus_back_money'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>
    <script type="text/javascript">
        $(document).ready(function () {
            var cast_id = '<?php echo (isset($model->cast_id) && $model->cast_id) ? $model->cast_id : 'wx_single'; ?>';
            fee(cast_id);

            $("#CityConfig_city_id").change(function () {
                var city_id = $("#CityConfig_city_id").val();
                completeData(city_id);
            });

            $("#CityConfig_fee_id").change(function () {
                var group_id = $("#CityConfig_fee_id").val();
                fee(group_id);

            });


        });

        function fee(id) {
            $.ajax({
                type: "get",
                url: "<?php echo Yii::app()->createUrl('cityConfig/ajax');?>",
                dataType: 'html',
                data: 'id=' + id,
                success: function (html) {
                    $('#result').html(html);
                }
            });
        }

        function completeData(){

            var city_id = $("#CityConfig_city_id").val();
            $.ajax({
                type: "get",
                url: "<?php echo Yii::app()->createUrl('cityConfig/AjaxCompleteCity');?>",
                dataType: 'json',
                data: 'city_id=' + city_id,
                success: function (html) {
                    $('#CityConfig_city_name').val(html.city_name);
                    $('#CityConfig_city_prifix').val(html.city_prefix);
                    $('#CityConfig_bonus_prifix').val(html.city_bonus_no);
                }
            });
        }

    </script>

</div>
<!-- form -->
