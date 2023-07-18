<?php $this->pageTitle = '司机处理'; ?>
<div>

    <div id="myTab" class="btn-group" data-toggle="buttons-radio">
        <?php if(!$data['processed']){?>
            <a href="#shield" class="btn btn-primary active">屏蔽</a>
        <?php }else{?>
            <a href="#unshield" class="btn">解除屏蔽</a>
            <a href="#follow" class="btn btn-primary active">跟进</a>
        <?php }?>

    </div>


    <div class="tab-content">
        <?php if(!$data['processed']){?>
            <div class="tab-pane active" id="shield" >
               <br/>
                屏蔽：
                <p><?php echo CHtml::dropDownList('limit_day','', array('1'=>'屏蔽1天','3'=>'屏蔽3天','7'=>'屏蔽7天','3600'=>'永久屏蔽'));?></p>
                备注：
                <p><?php echo CHtml::textArea('txtmark','',array("rows"=>"3")); ?></p>
            </div>
        <?php }else{?>
            <div class="tab-pane" id="unshield">
                <br/>
                解除屏蔽原因：
                <p><?php echo CHtml::textArea('txtreason','',array("rows"=>"3")); ?></p>
                <p>司机补偿现金&nbsp;<?php echo CHtml::checkBox('is_recoup',false); ?></p>
                <p style="color: red;">提示“请在解除屏蔽原因框内填写补偿现金金额”</p>
            </div>
            <div class="tab-pane active" id="follow">
                <br/>
                <p><?php echo CHtml::textArea('txtfollow','',array("rows"=>"3")); ?></p>
                <p>已解约&nbsp;<?php echo CHtml::checkBox('is_leave',false); ?></p>

            </div>
        <?php }?>
        <p>
            <a id="submit" class="btn btn-success span5">提交</a>
        </p>
        <p>
            <?php
            //hiddenField  textField
            $actived=!$data['processed']?'shield':'follow';
            echo CHtml::hiddenField('action_type',$actived);
            echo CHtml::hiddenField('stime',$data['stime']);
            echo CHtml::hiddenField('etime',$data['etime']);
            echo CHtml::hiddenField('driver_id',$data['driver_id']);
            ?>
        </p>
    </div>


</div>
<script>
    $(document).ready(function(){
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');

            //清空tab所有样式
            $('#myTab a').each(function(){
                $(this).attr('class','btn');
            });
            //标示当前操作
            $(this).attr('class','btn btn-primary active');
            var type=$(this).attr('href');
            $('#action_type').val(type.replace("#",""));
        })

        $("#submit").click(function(){
            var ac_type=$("#action_type").val();
            var driver_id=$("#driver_id").val();
            var stime=$("#stime").val();
            var etime=$("#etime").val();
            var param={ac_type : ac_type,driver_id:driver_id,stime:stime,etime:etime};
            if(ac_type=='follow'){
                var txtfollow=$("#txtfollow").val();
                var is_leave=$("#is_leave").attr('checked')=='checked'?1:0;
                param.is_leave=is_leave;
                param.txtfollow=txtfollow;
            }
            if(ac_type=='unshield'){
                var txtreason=$("#txtreason").val();
                var is_recoup=$("#is_recoup").attr('checked')=='checked'?1:0;
                param.txtreason =txtreason;
                param.is_recoup=is_recoup;
            }
            if(ac_type=='shield'){
                var limit_day=$("#limit_day").val();
                var txtmark=$("#txtmark").val();
                param.limit_day=limit_day;
                param.txtmark=txtmark;
            }
            $(this).attr("disabled",'');
            jQuery.post(
                "<?php echo Yii::app()->createUrl('driver/process');?>",
                param,
                function(data) {
                    var ret=eval("'"+data+"'");
                    var obj=$.parseJSON(ret);
                    if(obj.succ==true){
                        $('#myModal').modal('hide');
                        $.fn.yiiGridView.update('ranking-grid');
                    }else{
                        $(this).attr("disabled",'');
                        alert(obj.msg);
                    }
                }
            );

        });


    });



</script>