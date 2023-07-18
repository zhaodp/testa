<div class="form span11">
    <div class="row">
      <span class="required">*</span>说明:只能填写1到N的数值，N加上司机当前分值不能大于12分
    </div>
<br>
<div class="row">
        司机工号：<?php echo $data->driver_id;?> 
    </div>
    <div class="row">
      当前分数：<?php echo $data->score; ?>

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
            var currentScore=parseInt('<?php echo $data->score; ?>');
            var score = parseInt($('#score').val());
            var reason = $.trim($('#reason').val());
            var totalScore=parseInt(currentScore+score);
            if(reason == "" || reason == "null"){
                alert("原因不能为空");
                return;
            }
            if(score < 0){
                alert("分数必须大于0");
                return;
            }
            // alert(totalScore);
            if(totalScore>12){
                alert("恢复的总分不能超过12分");
                return;
            }
            jQuery.post(
                '<?php echo Yii::app()->createUrl('/driver/scoreUpdate');?>',
                {
                    driver_id : '<?php echo $data->driver_id;?>',
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