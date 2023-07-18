<div class="form span11">
    <div class="row">
      <span class="required">*</span>说明:只能填写1到N的数值，N加上司机当前分值不能大于12分
    </div>
    <br>
    <div class="row">说明:司机工号每行一个</div>
    <div class="row">
        司机工号<span class="required">*</span>：<textarea  name='driverIds' id="driverIds"></textarea>
    </div>
    <div class="row">
      增加分数<span class="required">*</span>：<input type='text' name='score' id="score">
    </div>
    <div class="row">
      原因<span class="required">*</span>：<input type='text' name='reason' id="reason">
    </div>

<input type="submit" value="修改" class="btn btn-success" id="submit_btn">
</div>


<script>
    $(function(){

        $("#submit_btn").click(function(){
            var driverIds = $.trim($('#driverIds').val());
            var score = parseInt($('#score').val());
            var reason = $.trim($('#reason').val());
            if(driverIds == "" || driverIds == "null") {
                alert("司机工号不能为空");
                return;
            }
            if(reason == "" || reason == "null"){
                alert("原因不能为空");
                return;
            }
            if(score < 0){
                alert("分数必须大于0");
                return;
            }

            jQuery.post(
                '<?php echo Yii::app()->createUrl('/driver/scoreBatchUpdate');?>',
                {
                    driver_id : driverIds,
                    score : score,
                    reason : reason
                },
                function(d) {
                    if (d == 1) {
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