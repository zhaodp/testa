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
        url: <?php echo '"'.$this->createUrl("bonusCode/bonus_generate_400").'"'?>,
        nextUrl: <?php echo '"'.$this->createUrl("bonusCode/bonus_rules_create_call_list").'"'?>

    }
</script>
<div class="container">
    <h3 class="page-header">来电弹窗优惠券绑定设置</h3>
    <div>
        <form class="form" role="form" id="bonus" method="post">
            <div class="form-group">
                <label class="sr-only" for="bonusId">优惠码id</label>
                <input type="text" class="form-control" id="bonusId" placeholder="必填" name="bonus_id">
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">备注</div>
                    <input class="form-control" type="test" placeholder="" name="remark">
                </div>
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">短信内容</label>
                <textarea class="form-control" rows="3" name="sms"></textarea>
            </div>
            <div class="radio">
                <label>
                    <input type="checkbox" name="status"  value="1" checked>
                    是否启用
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="checkbox" name="multi"  value="1" checked>
                    是否允许同一手机多次绑定
                </label>
            </div>
           <div class="radio">
                <label>
                    <input type="radio" name="use_range"  value="1" checked>
                    不限制
		   
                </label> 
		 <label>
                    
                    <input type="radio" name="use_range"  value="2" >
                                        app新客
                </label>
            </div>  
	 <button type="submit" class="btn btn-default">提交</button>
        </form>
    </div>
</div>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/formValid/jquery.validate.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/formValid/peaceful.form.js"></script>
<script>
    $.getJSON()
</script>
