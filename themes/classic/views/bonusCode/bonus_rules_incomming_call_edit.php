<?php
?>
<script>
    app_data = {
        form:"bonus",
        rules: {
            bonus_id: {
                required: true
            }

        }, messages: {
            bonus_id: {
                required: "优惠码不可以为空"
            }
        },
        url: <?php echo '"'.$this->createUrl("bonusCode/bonus_generate_400_update").'"'?>,
        nextUrl: <?php echo '"'.$this->createUrl("bonusCode/bonus_rules_create_call_list").'"'?>

    }
</script>
<div class="container">
    <h3 class="page-header">来电弹窗优惠券更新</h3>
    <div>
        <form class="form" role="form" id="bonus" method="post">
            <div class="form-group">
                <label class="sr-only" for="bonusId">优惠码id</label>
                <input type="text" class="form-control" id="bonusId" placeholder="必填" name="bonus_id" value="<?php echo $bonus400['bonus_code_id']?>" readonly>
                <input type="hidden" class="form-control" id="bonusId" placeholder="必填" name="id" value="<?php echo $bonus400['id']?>" readonly>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">备注</div>
                    <input class="form-control" type="test" placeholder="" name="remark" value="<?php echo $bonus400['remark']?>">
                </div>
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">短信内容</label>
                <textarea class="form-control" rows="3" name="sms"><?php echo $bonus400['sms']?></textarea>
            </div>
            <div class="radio">
                <label>
                    <input type="checkbox" name="status"  value="1" <?php echo  ($bonus400['status'] == 1?'checked':'')?>>
                    是否启用
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="checkbox" name="multi"  value="1" <?php echo  ($bonus400['multi'] == 1?'checked':'')?>>
                    是否允许同一手机多次绑定
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="use_range"  value="1" <?php echo  ($bonus400['use_range'] == 1?'checked':'')?>>
                    不限制
		
                </label>
		 <label>
                 
                <input type="radio" name="use_range"  value="2" <?php echo  ($bonus400['use_range'] == 2?'checked':'')?>>
                                        app新客
                </label>
            </div>
            <button type="submit" class="btn btn-default">更新</button>
        </form>
    </div>
</div>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/formValid/jquery.validate.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/formValid/peaceful.form.js"></script>
<script>
    $.getJSON()
</script>
