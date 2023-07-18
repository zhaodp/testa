<div id="errorplace" style="font-size:20px;color:red;"><?php if(isset($errormsg) && !empty($errormsg)) print_r( $errormsg); if(is_array($errormsg)) {echo   '<br> password:123456';}?></div>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'testdriver-form',
    'enableAjaxValidation'=>false,
));
?>
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
    <label for="Driver_Vnumber">V号</label>    <input size="20" maxlength="20" class="span12" name="Driver[vnumber]" id="Driver_Vnumber" type="text" value="<?php echo isset($posts['vnumber'])? $posts['vnumber'] : '';?>">
</div>
<div class="span2">
    <label for="check" style="padding:10px;"></label>   <a href="javascript:void(0);"  id="check" class="btn btn-primary">检测是否可用</a>
</div>
<input type="hidden" name="Driver[single]" id="single" value="0">
<div>
    <div class="span2">
        <?php echo $form->label($model,'phone'); ?>
        <?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20,'class'=>"span12")); ?>
    </div>
    <div class="span2">
        <?php echo $form->label($model,'imei'); ?>
        <?php echo $form->textField($model,'imei',array('size'=>20,'maxlength'=>20,'class'=>"span12")); ?>
    </div>
    <div class="span2">
        <label for="Driver_sim">sim</label>    <input size="20" maxlength="20" class="span12" name="Driver[sim]" id="Driver_sim" type="text" value="<?php echo isset($posts['sim']) ? $posts['sim'] : '';?>">
    </div>
    <div class="controls controls-row">
        <?php echo CHtml::submitButton('提交',array('class'=>'btn span2','style'=>'margin-top:15px;')); ?>
    </div>

    <div class="span2">
        <label for="check" style="padding:10px;"></label>   <a href="javascript:void(0);"  id="lukaoid" class="btn btn-primary">生成路考工号</a>
    </div>

</div>

<?php $this->endWidget(); ?>
<script type="text/javascript">

    $(document).ready(function () {
        $("#check").click(function () {
            if($("#Driver_city_id").val() == 0){
                alert('请选择城市');
                return false;
            }
            var v = $("#Driver_Vnumber").val();
            if(v == ''){
                alert('请输入v号');
                return false;
            }
            $.get("<?php echo Yii::app()->createUrl('driverControl/GetVinfo');?>",
                {v:v},
                function (data) {
                if (data['status'] == '1') {
                    if(data['result']['phone']) $('#Driver_phone').val(data['result']['phone']);
                    $('#Driver_imei').val(data['result']['imei']);
                    $('#Driver_sim').val(data['result']['simcard']);
                } else {
                    alert(0);
                }
            }, "json");
        });

        $("#lukaoid").click(function () {
            if($("#Driver_city_id").val() == 0){
                alert('请选择城市');
                return false;
            }
            $("#single").val(1);
            $("#testdriver-form").submit();



        });
    });

</script>