<h1>解除屏蔽</h1>
<div class="span6">备注</div>
<input type="hidden" id="id" value="<?php echo $id ;?>"/>
<input type="hidden" id="phone" value="<?php echo $phone ;?>"/>
<textarea cols="30" rows="10" name="mark" id="mark"></textarea>
<div class="span6" style="text-align: center">
    <?php echo CHtml::button('确定解屏蔽', array('id'=>'subtmit_add_ajax','action'=>'update','class' => 'btn btn-large btn-primary','style' => 'margin-right:15px')); ?>
</div>
<script type="text/javascript">
    $(function(){
        $('#subtmit_add_ajax').click(function(){
            var post_data={};
            post_data['action'] = $(this).attr('action');
            post_data['mark'] = $('#mark').val();
            post_data['id'] = $('#id').val();
            post_data['phone'] = $('#phone').val();
            $('#subtmit_add_ajax').attr('disabled','disabled');
            $.post('<?php echo Yii::app()->createUrl('/customer/mainupdate_status',array('id'=>$id));?>',
                post_data,
                function(data){
                    if(data.succ==1){
                        window.location.reload();
                    }
                }, "json");
        });
    });
</script>
