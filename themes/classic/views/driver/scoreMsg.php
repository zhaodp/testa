<span id="waring"></span>
司机工号：<?php echo $data->user;?>  司机手机号码：<?php echo ($data->ext_phone) ? $data->ext_phone : $data->phone; ?>
<br>
短信内容
<textarea rows="3" cols="80" class="span12" id="sms_content"><?php echo $data->user;?>师傅，由于您扣分已达到或超过9分 请于.....来公司培训。</textarea>
<input type="submit" value="提交" class="btn btn-success" id="submit_btn">


<script>
    $(function(){

        $("#submit_btn").click(function(){
            var arrID = new Array();
            var num = $(".table tr").length;
            var batch=$("#batch").val();
            var sms_content = $('#sms_content').val();

            //if(other_id.length!=0 && action=='exam'){ alert("目前有"+other_id.length+"位司机目前不能进行路考，请重新确认列表里的司机，");return false;}
            if(sms_content.length<5){ alert("请输入短信内容");return false;}



            jQuery.post(
                '<?php echo Yii::app()->createUrl('/driver/scoreStudyNotice');?>',
                {
                    driver_id : '<?php echo $data->user;?>',
                    sms_content : sms_content
                },
                function(d) {
                    if (d == 1) {
                        alert("发送成功。");
                        $(window.parent.document).find(".ui-dialog-buttonset button").click();
                    } else {
                        alert("操作失败！");
                    }
                }
            );
        });


    });

</script>