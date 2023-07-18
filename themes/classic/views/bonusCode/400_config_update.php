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
            }

        }, messages: {
            name: {
                required: "<span style='color: red'>配置名称不可以为空</span>"
            }
        },
        url: <?php echo '"'.$this->createUrl("bonusCode/config_update").'"'?>,
        nextUrl: <?php echo '"'.$this->createUrl("bonusCode/config_list").'"'?>

    }
</script>
<div class="container">
    <h4 class="page-header">400未接通电话设置更新</h4>

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
            </div>
            <br>
            <button type="submit" class="btn btn-default">提交</button>
        </form>
    </div>
</div>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/formValid/jquery.validate.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/formValid/peaceful.form.js"></script>