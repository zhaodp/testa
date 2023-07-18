<div class="form span11">
    <div class="row">
      <span class="required">*</span>说明:只能填写大于0的整数
    </div>
<br>
<div class="row">
        城市：<?php echo $city_name;?> 
    </div>
    <div class="row">
      当前剩余配额：<?php echo $left_crown; ?>

    </div>
     <div class="row">
      增加配额<span class="required">*</span>：<input type='text' name='incrNum' id="incrNum">
    </div>
    <div class="row">
      原因<span class="required">*</span>：<input type='text' name='reason' id="reason">
    </div>

<input type="submit" value="修改" class="btn btn-success" id="submit_btn">
</div>


<script>
    $(function(){

        $("#submit_btn").click(function(){
            var incrNum = $('#incrNum').val();
            var reason = $.trim($('#reason').val());

            if(reason == "" || reason == "null"){
                alert("原因不能为空");
                return;
            }
            var ex = /^[1-9]\d*$/;
            if (!ex.test(incrNum)) {
                alert("增加配额数必须为大于0的整数");
                return;
            }

            jQuery.post(
                '<?php echo Yii::app()->createUrl('/driver/crownUpdate');?>',
                {
                    city_id : '<?php echo $city_id;?>',
                    incrNum : incrNum,
                    reason : reason

                },
                function(d) {
                    if (d !=0 ) {
                        alert("修改成功。");
                        $(window.parent.document).find(".ui-dialog-buttonset button").click();
                    } else {
                        alert("修改失败！");
                    }
                }
            );
        });


    });

</script>