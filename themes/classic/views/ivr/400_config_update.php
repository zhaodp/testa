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
            start_time: {
                required: true
            },
            end_time: {
                required: true
            }

        }, messages: {
            name: {
                required: "<span style='color: red'>配置名称不可以为空</span>"
            },
            start_time: {
                required: "<span style='color: red'>上线时间不可以为空</span>"
            },
            end_time: {
                required: "<span style='color: red'>下线时间不可以为空</span>"
            }
        },
        url: <?php echo '"'.$this->createUrl("ivr/config_update").'"'?>,
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
            <input name="is_update" value="1" type="hidden">
            <input name="id" value="<?php echo $config['id'] ?>" type="hidden">

            <div class="form-group">
                <label class="sr-only" for="bonusId">配置名称</label>
                <input type="text" class="form-control" id="bonusId" placeholder="必填" name="name"
                       value="<?php echo $config['name'] ?>">
            </div>
            <div class="form-group">
                <label class="sr-only" for="bonusId">优惠码id</label>
                <input type="text" class="form-control" id="bonusId" placeholder="" name="bonus_code_id"
                       value="<?php echo $config['bonus_code_id'] ?>">
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">绑定优惠券短信内容</label>
                <textarea class="form-control" rows="3" name="sms"><?php echo $config['sms'] ?></textarea>
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">不绑定优惠券短信内容</label>
                <textarea class="form-control" rows="3" name="other_sms"><?php echo $config['other_sms'] ?></textarea>
            </div>
            <div class="form-group">
                <label class="sr-only" for="exampleInputPassword2">使用城市</label>

                <div>
                    <?php
                    $city = RCityList::model()->getOpenCityList();
                    $city[0] = '全国';
                    ksort($city);
                    if ($config['citys'] == "null" || empty($config['citys']))
                        $choosed = array();
                    else
                        $choosed = json_decode($config['citys']);
                    $i = 0;
                    foreach ($city as $k => $v) {
                        if (in_array($k, $choosed))
                            echo '<input type="checkbox" checked value="' . $k . '" name="citys[' . $i . ']">' . $v . '&nbsp;&nbsp;&nbsp;';
                        else
                            echo '<input type="checkbox" value="' . $k . '" name="citys[' . $i . ']">' . $v . '&nbsp;&nbsp;&nbsp;';
                        $i++;
                    }
                    ?>
                </div>
            </div>
            <br>

            <div>
                <?php
                if (isset($config['status']) && $config['status'] == 1) {
                    echo '<input type="checkbox" name="status" value="1" checked>';
                } else {
                    echo '<input type="checkbox" name="status" value="1">';
                }
                ?>
                是否启用
                &nbsp;&nbsp;&nbsp;
                <?php
                if (isset($config['repeat_use']) && $config['repeat_use'] == 1) {
                    echo '<input type="checkbox" name="repeat_use" value="1" checked>';
                } else {
                    echo '<input type="checkbox" name="repeat_use" value="1">';
                }
                ?>
                是否允许多次绑定优惠券
            </div>
            <br>

            <div class="radio-inline">
                使用范围                &nbsp;&nbsp;&nbsp;

                <?php
                if (isset($config['use_scope']) && $config['use_scope'] == 1) {
                    echo '<input type="radio" name="use_scope" id="inlineRadio1" value="0" > 不限制';
                    echo '<input type="radio" name="use_scope" id="inlineRadio2" value="1" checked> app新客';
                } else {
                    echo '<input type="radio" name="use_scope" id="inlineRadio1" value="0" checked> 不限制';
                    echo '<input type="radio" name="use_scope" id="inlineRadio2" value="1"> app新客';
                }
                ?>
            </div>
            <br>

            <div>
                <div class="input-group">
                      <span class="input-group-addon">
                        上线时间
                      </span>
                    <input type="text" class="datetimepicker form-control" id="onlinetimeId" placeholder="上线开始时间"
                           value="<?php echo $config['start_time'] ?>" name="start_time">
                      <span class="input-group-btn">
                        －
                      </span>
                    <input type="text" class="datetimepicker form-control" placeholder="上线结束时间" name="end_time"
                           value="<?php echo $config['end_time'] ?>">
                </div>
            </div>
            <br>
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