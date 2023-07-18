<?php $this->pageTitle = '申报错误';  ?>

<div style="width: 450;">
    <h4>申报错误</h4>
    <div>
        <label>错误类型：</label>
        <div>
        <?php
        foreach(CallcenterError::$errorArr as $k=>$v){
            echo '<input type="checkbox" id="ckb_'.$k.'" name="error_type[]" value="'.$k.'"  width="20px"> '.$v.'&nbsp;';
        }
        ?>
        </div>
    </div>
    <div>
        <label>备注：</label>
        <?php echo CHtml::textArea('mark','',array('class'=>'input-xlarge','rows'=>'4','style'=>'width: 320px;'));?>
    </div>
    <div>

        <?php
        echo CHtml::hiddenField('queue_id',$queueid);
        echo CHtml::hiddenField('order_id',$orderid);
        echo CHtml::submitButton('提交',array('id'=>'submitBtn','class'=>'btn btn-success span2'));?>
    </div>

</div>


<script>
    $(document).ready(function(){
        $("#submitBtn").click(function(){
            var falg=true;
            var error_type="";
            $("input[name='error_type[]']:checked").each(function(){
                error_type+=$(this).val()+",";
            });

            var mark=$("#mark").val();
            var queueId=$("#queue_id").val();
            var orderId=$("#order_id").val();
            var token= '<?php echo Yii::app()->request->csrfToken; ?>';

            if(error_type.length<1){
                falg=false;
                alert('请选择错误类型！');
            }
            if(queueId=="" && orderId=="" ){
                falg=false;
                alert('ID为空！');
            }
            if(falg==true){
                $(this).attr("disabled",'disabled');
                jQuery.post(
                    '<?php echo Yii::app()->createUrl('CallCenter/error');?>',
                    {
                        qid : queueId,
                        oid : orderId,
                        error_type : error_type,
                        mark : mark,
                        YII_CSRF_TOKEN : token
                    },
                    function(data) {
                        var ret=eval("'"+data+"'");
                        var obj=$.parseJSON(ret);
                        if(obj.succ==true){
                            $('#myModal').modal('hide');
                        }else{
                            $(this).attr("disabled",'');
                            alert(obj.msg);
                        }
                    }
                );
            }
        });

    })


</script>