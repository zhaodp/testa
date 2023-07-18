<div class="form span11">
    <div class="row">
      <span class="required">*</span>说明:只能填写-10000到10000之间的整数值
    </div>
    <br>
    <div class="row">说明:司机工号每行一个</div>
    <div class="row">
        司机工号<span class="required">*</span>：<textarea  name='driverIds' id="driverIds"></textarea>
    </div>
    <div class="row">
      增加e币<span class="required">*</span>：<input type='text' name='add_wealth' id="add_wealth">
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
            var add_wealth = parseInt($('#add_wealth').val());
            var reason = $.trim($('#reason').val());
            if(driverIds == "" || driverIds == "null") {
                alert("司机工号不能为空");
                return;
            }
            if(reason == "" || reason == "null"){
                alert("原因不能为空");
                return;
            }
            if (add_wealth > 10000 || add_wealth<-10000) {
                alert("奖赏或处罚的e币数不能超过10000");
                return;
            }

            jQuery.post(
                '<?php echo Yii::app()->createUrl('/driver/ecoinBatchUpdate');?>',
                {
                    driver_id : driverIds,
                    add_wealth : add_wealth,
                    reason : reason
                },
                function(d) {
                    if (d>0) {
                        alert("修改成功。");
                    } else {
                        alert("修改失败！");
                    }
                    $.fn.yiiGridView.update('ecoin-driver-grid');
                }
            );
        });
    });

</script>