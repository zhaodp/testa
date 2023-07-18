<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangjun
 * Date: 15/1/8
 * Time: 下午2:58
 */

?>
<script>
    app_data = {
        form: "bonus",
        rules: {
            name: {
                required: true
            },
            start_time:{
                required:true
            },
            end_time:{
                required:true
            }

        }, messages: {
            name: {
                required: "<span style='color: red'>配置名称不可以为空</span>"
            },
            start_time:{
                required:"<span style='color: red'>上线时间不可以为空</span>"
            },
            end_time:{
                required:"<span style='color: red'>下线时间不可以为空</span>"
            }
        },
        url: <?php echo '"'.$this->createUrl("ivr/config_insert").'"'?>,
        nextUrl: <?php echo '"'.$this->createUrl("ivr/config_list").'"'?>
    }
</script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/datetimeform/bootstrap-datetimepicker.css"/>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/datetimeform/bootstrap-datetimepicker.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/datetimeform/bootstrap-datetimepicker.zh-CN.js"></script>
<div class="container">
    <h4 class="page-header">ivr自助下单配置</h4>

    <div>
        <form class="form" role="form" id="bonus" method="post">
            <div class="form-group">
                <label class="sr-only" for="bonusId">配置名称</label>
                <input type="text" class="form-control" id="bonusId" placeholder="必填" name="name">
            </div>
            <div class="form-group">
                <label class="sr-only" for="bonusId">优惠码id</label>
                <input type="text" class="form-control" id="bonusId" placeholder="" name="bonus_code_id">
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">绑定优惠券短信</label>
                <textarea class="form-control" rows="3" name="sms"></textarea>
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">不绑定优惠券短信</label>
                <textarea class="form-control" rows="3" name="other_sms"></textarea>
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">使用城市</label>

                <div>
                    <?php
                    $city = RCityList::model()->getOpenCityList();
                    $city[0] = '全国';
                    ksort($city);
                    $i = 0;
                    foreach ($city as $k => $v) {
                        echo '<input type="checkbox" value="' . $k . '" name="citys[' . $i . ']">' . $v . '&nbsp;&nbsp;&nbsp;';
                        $i++;
                    }
                    ?>
                </div>
            </div>
            <br>

            <div class="radio-inline">
                <input type="checkbox" name="status" value="1" checked>
                是否启用
                &nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="repeat_use" value="1" checked>
                是否允许多次绑定优惠券
            </div>
            <br>
            <div class="radio-inline">
               使用范围                 &nbsp;&nbsp;&nbsp;
                <input type="radio" name="use_scope" id="inlineRadio1" value="0" checked> 不限制
                <input type="radio" name="use_scope" id="inlineRadio2" value="1"> app新客
            </div>
            <br>
            <div>
                <div class="input-group">
                      <span class="input-group-addon">
                        上线时间
                      </span>
                    <input type="text" class="datetimepicker form-control" id="onlinetimeId" placeholder="上线开始时间"
                           value="" name="start_time">
                      <span class="input-group-btn">
                        －
                      </span>
                    <input type="text" class="datetimepicker form-control" placeholder="上线结束时间" name="end_time"
                           value="">
                </div>
            </div>
            <button type="submit" class="btn btn-default">提交</button>
        </form>
    </div>
</div>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/formValid/jquery.validate.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/formValid/peaceful.form.js"></script>
<script>
    $('.datetimepicker').datetimepicker({
        language: "zh-CN",
        weekStart: 1,
        autoclose: true,
        todayHighlight: 1,
        minView: 1,
        format: "yyyy-mm-dd hh:00:00",
        forceParse: 0
    });
</script>