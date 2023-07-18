<h1>交通事故案件信息记录</h1>
<hr>
<?php $form = $this -> beginWidget('CActiveForm',array(
    'id'=>'trafficAccident_form',
    )); ?>
    出险时间:
    <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array(
            'value'=>isset($taModel->accidentTime) ? $taModel->accidentTime : '',
            'name' => 'CtrafficAccident[accidentTime]',
            'mode' => 'datetime', //use "time","date" or "datetime" (default)
            'options' => array(
                'dateFormat' => 'yy-mm-dd'
            ), // jquery plugin options
            'language' => 'zh',
        ));
    ?>


    <br>
    是否签署服务确认单:
    <?php
            echo $form->radioButtonList(
                $taModel,
                'sign_order',
                array(1=>'事前签单',2=>'事后补签',3=>'未签单'),
                array('separator' => '&nbsp;&nbsp;',
                    'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;'))
            )
    ?>
    <br>
    事故地点:
    <?php
        echo $form->radioButtonList(
            $taModel,
            'accident_site',
            array(1=>'出发时',2=>'行驶中',3=>'到达时',4=>'中途停靠'),
            array('separator' => '&nbsp;&nbsp;',
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;'))
        )
    ?>
    <br>
    是否拍照:
    <?php
        echo $form->radioButtonList(
            $taModel,
            'take_photos',
            array(1=>'司机已拍照',2=>'客户已拍照',3=>'保险公司已拍照',4=>'未拍照'),
            array('separator' => '&nbsp;&nbsp;',
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;'))
        )
    ?>
    <br>
    是否报警:
    <?php
        echo $form->radioButtonList(
            $taModel,
            'calll_police',
            array(1=>'是',2=>'否'),
            array('separator' => '&nbsp;&nbsp;',
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;'))
        )
    ?>
    <br>
    是否涉及人伤:
    <?php
        echo $form->radioButtonList(
            $taModel,
            'deaths',
            array(1=>'是',2=>'否'),
            array('separator' => '&nbsp;&nbsp;',
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;'))
        )
    ?>
    <br>
    是否涉及物损:
    <?php
        echo $form->radioButtonList(
            $taModel,
            'material',
            array(1=>'是',2=>'否'),
            array('separator' => '&nbsp;&nbsp;',
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;'))
        )
    ?>
    <br>
    单双方事故:
    <?php
        echo $form->radioButtonList(
            $taModel,
            'accident_duty',
            array(1=>'单方',2=>'双方'),
            array('separator' => '&nbsp;&nbsp;',
                'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;'))
        )
    ?>
    <div id="singleAccidentDiv">
        <br><br><b>单方事故</b><br><br>
        车型:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form -> textField($taModel,'single_carmodel'); ?>&nbsp;&nbsp;
        受损部位:&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form -> textField($taModel,'single_damagepart'); ?>
    </div>

    <div id="bothAccidentDiv">
        <br><br><b>双方事故</b><br><br>
        主车车型:&nbsp;&nbsp;<?php echo $form -> textField($taModel,'both_carmodel'); ?>&nbsp;&nbsp;
        主车受损部位:&nbsp;&nbsp;<?php echo $form -> textField($taModel,'both_damagepart'); ?>
        <br><br>
        第三方车型①:<?php echo $form -> textField($taModel,'third_carmodel1'); ?>&nbsp;&nbsp;
        第三方受损部位①:<?php echo $form -> textField($taModel,'third_damagepart1'); ?>
        <br><br>
        第三方车型②:<?php echo $form -> textField($taModel,'third_carmodel2'); ?>&nbsp;&nbsp;
        第三方受损部位②:<?php echo $form -> textField($taModel,'third_damagepart2'); ?>
        <br><br>
        第三方车型③:<?php echo $form -> textField($taModel,'third_carmodel3'); ?>&nbsp;&nbsp;
        第三方受损部位③:<?php echo $form -> textField($taModel,'third_damagepart3'); ?>
        <br><br>
        第三方车型④:<?php echo $form -> textField($taModel,'third_carmodel4'); ?>&nbsp;&nbsp;
        第三方受损部位④:<?php echo $form -> textField($taModel,'third_damagepart4'); ?>
        <br><br>
        第三方车型⑤:<?php echo $form -> textField($taModel,'third_carmodel5'); ?>&nbsp;&nbsp;
        第三方受损部位⑤:<?php echo $form -> textField($taModel,'third_damagepart5'); ?>
    </div>
    <br>
    <input type="hidden" id="hasId" name="hasId">
    <input type="hidden"  name="CtrafficAccident[id]" value="<?php echo $taModel->id ?>">
    <input type="hidden" name="CtrafficAccident[customer_id]" value="<?php echo $cid ?>">
    <a class="btn btn-info span2" name="trafficsubmit_btn" id="trafficsubmit_btn">提交</a>
<br>

<?php $this -> endWidget(); ?>

<script type="text/javascript">
    $(document).ready(function(){
        var accidentDutyChecked = $('input[name="CtrafficAccident[accident_duty]"]:checked').val();
        if(accidentDutyChecked == 1){
            $("#bothAccidentDiv").hide();
            $("#singleAccidentDiv").show();
        }else if(accidentDutyChecked == 2){
            $("#singleAccidentDiv").hide();
            $("#bothAccidentDiv").show();
        }
        var _id = "<?php echo $taModel->id ?>";//如果页面进来id不为空则说明已经存在信息
        if(_id){
            $("#hasId").val(1);//存在数据
        }else{
            $("#hasId").val(0);//不存在数据
        }
    });
    //选择单方事故 双方事故的版块隐藏
    $("#CtrafficAccident_accident_duty_0[value=1]").click(function(){
        $("#bothAccidentDiv").hide();
        $("#singleAccidentDiv").show();
    });
    //选择双方事故  单方事故的版块隐藏
    $("#CtrafficAccident_accident_duty_1[value=2]").click(function(){
        $("#singleAccidentDiv").hide();
        $("#bothAccidentDiv").show();
    });
    //异步提交表单处理
    $("#trafficsubmit_btn").click(function(){
        var accidentTime =  $("#CtrafficAccident_accidentTime").val().trim();
        if(!accidentTime){
            alert("出险时间必须填写!");
            $("#CtrafficAccident_accidentTime").focus();
            return false;
        }
        var options = {
            url: '<?php echo Yii::app()->createUrl('complain/trafficAccident') ?>',
            type: 'post',
            dataType: 'text',
            data: $("#trafficAccident_form").serialize(),
            success: function (data) {
                if (data == 'update'){
                    alert("数据修改成功");
                    $("#hasId").val(1);//存在数据
                }else if(data){
                    alert("数据保存成功");
                    $("input[name='CtrafficAccident[id]']").val(data);
                    $("#hasId").val(1);//存在数据
                }else{
                    alert("数据库出现异常保存失败!");
                }
            }
        };
        $.ajax(options);
        return false;
    });

</script>