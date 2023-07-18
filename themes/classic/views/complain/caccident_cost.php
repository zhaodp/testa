<h1>交通事故涉及费用</h1>
<hr>
<?php $form = $this -> beginWidget('CActiveForm',array(
    'id'=>'accidentCost_form'
)); ?>
    赔偿维修费:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'maintain_cost'); ?><br><br>
    实际本车维修费:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'realcarcost'); ?><br><br>
    实际第三方维修费:&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'realthirdcost'); ?><br><br>
    保费上浮:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'premium_up'); ?><br><br>
    额外补偿:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'extal_cost'); ?><br><br>
    拖车费:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'towing_fee'); ?><br><br>
    交通补偿:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'traffic_compensation'); ?><br><br>
    运营车辆务工:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'car_workcost'); ?><br><br>
    物损:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'damage_cost'); ?><br><br>
    人伤:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'hurt_cost'); ?><br><br>
    其他:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ccModel,'other_cost'); ?><br><br>
    <br>
<input type="hidden" id="hasId3" name="hasId3">
<input type="hidden"  name="CaccidentCost[id]" value="<?php echo $ccModel->id ?>">
<input type="hidden" name="CaccidentCost[customer_id]" value="<?php echo $cid ?>">
<a class="btn btn-info span2" name="costubmit_btn" id="costubmit_btn">提交</a><br>
<?php $this -> endWidget(); ?>

<script type="text/javascript">
    $(document).ready(function(){
        var _id = "<?php echo $ccModel->id ?>";//如果页面进来id不为空则说明已经存在信息
        if(_id){
            $("#hasId3").val(1);//存在数据
        }else{
            $("#hasId3").val(0);//不存在数据
        }
    });
    function validates(){
        var _pcwxf = $("#CaccidentCost_maintain_cost").val();
        var _sjxccb = $("#CaccidentCost_realcarcost").val();
        var _sjdsfwxf = $("#CaccidentCost_realthirdcost").val();
        var _bfsf = $("#CaccidentCost_premium_up").val();
        var _ewbc = $("#CaccidentCost_extal_cost").val();
        var _tcf = $("#CaccidentCost_towing_fee").val();
        var _jtbc = $("#CaccidentCost_traffic_compensation").val();
        var _yyclwg = $("#CaccidentCost_car_workcost").val();
        var _ws = $("#CaccidentCost_damage_cost").val();
        var _rs = $("#CaccidentCost_hurt_cost").val();
        var _qt = $("#CaccidentCost_other_cost").val();
        var valueArrays = [_pcwxf,_sjxccb,_sjdsfwxf,_bfsf,_ewbc,_tcf,_jtbc,_yyclwg,_ws,_rs,_qt];
        var flag = false;
        for(var i = 0;i < valueArrays.length;i++){
            if(valueArrays[i]){
                flag = true;
                break;
            }
        }
        if(!flag){
            alert("必须填写一项信息!");
            return false;
        }
        if(isNaN(_pcwxf)){
            alert("赔偿维修费必须是数字!");
            $("#CaccidentCost_realcarcost").focus();
            return false;
        }
        if(isNaN(_sjxccb)){
            alert("实际本车维修费必须是数字!");
            $("#CaccidentCost_realcarcost").focus();
            return false;
        }
        if(isNaN(_sjdsfwxf)){
            alert("实际第三方维修费必须是数字!");
            $("#CaccidentCost_realthirdcost").focus();
            return false;
        }
        if(isNaN(_bfsf)){
            alert("保费上浮必须是数字!");
            $("#CaccidentCost_premium_up").focus();
            return false;
        }
        if(isNaN(_ewbc)){
            alert("额外补偿必须是数字!");
            $("#CaccidentCost_extal_cost").focus();
            return false;
        }
        if(isNaN(_tcf)){
            alert("拖车费必须是数字!");
            $("#CaccidentCost_towing_fee").focus();
            return false;
        }
        if(isNaN(_jtbc)){
            alert("交通补偿必须是数字!");
            $("#CaccidentCost_traffic_compensation").focus();
            return false;
        }
        if(isNaN(_yyclwg)){
            alert("运营车辆务工必须是数字!");
            $("#CaccidentCost_car_workcost").focus();
            return false;
        }
        if(isNaN(_ws)){
            alert("物损必须是数字!");
            $("#CaccidentCost_damage_cost").focus();
            return false;
        }
        if(isNaN(_rs)){
            alert("人伤费用必须是数字!");
            $("#CaccidentCost_hurt_cost").focus();
            return false;
        }
        if(isNaN(_qt)){
            alert("其他费用必须是数字!");
            $("#CaccidentCost_other_cost").focus();
            return false;
        }
        return true;
    }
    //异步提交表单处理
    $("#costubmit_btn").click(function(){
        if(!validates()){
            return false;
        }
        var options = {
            url: '<?php echo Yii::app()->createUrl('complain/accidentCost') ?>',
            type: 'post',
            dataType: 'text',
            data: $("#accidentCost_form").serialize(),
            success: function (data) {
                if (data == 'update'){
                    alert("数据修改成功");
                    $("#hasId3").val(1);//存在数据
                }else if(data){
                    alert("数据保存成功");
                    $("input[name='CaccidentCost[id]']").val(data);
                    $("#hasId3").val(1);//存在数据
                }else{
                    alert("数据库出现异常保存失败!");
                }
            }
        };
        $.ajax(options);
        return false;
    });
</script>