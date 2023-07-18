<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<?php
$id = '';
$name = '';
$english_name = '';
$bonus_sn = '';
$bonus_sn2 = '';
$bonus_num = '';
$bind_num = '';
$begin_time = '';
$end_time =  '';
$sms = '';
$sms2 = '';
$template_id = '';
$page_json = '';
$act_target = '';
$target_people = '';
$act_sharpen = '';
$subsidy_type = '';
$subsidy_amount = '';
$data_send_begin_time = '';
$data_send_end_time = '';
$channel = '';
$send_rate = '';
$send_mails = '';
if(isset($model['id'])){
    $id = $model['id'];
    $name = isset($model['name']) ? $model['name'] : '';
    $english_name = isset($model['english_name']) ? $model['english_name'] : '';
    $bonus_sn = isset($model['bonus_sn']) ? $model['bonus_sn'] : '';
    $bonus_sn2 = isset($model['bonus_sn2']) ? $model['bonus_sn2'] : '';
    $bonus_num = isset($model['bonus_num']) ? $model['bonus_num'] : '';
    $bind_num = isset($model['bind_num']) ? $model['bind_num'] : '';
    $begin_time = isset($model['begin_time']) ? $model['begin_time'] : '';
    $end_time = isset($model['end_time']) ? $model['end_time'] : '';
    $sms = isset($model['sms']) ? $model['sms'] : '';
    $sms2 = isset($model['sms2']) ? $model['sms2'] : '';
    $template_id = isset($model['template_id']) ? $model['template_id'] : '';
    $page_json = isset($model['page_json']) ? $model['page_json'] : '';
    $act_target = isset($model['act_target']) ? $model['act_target'] : '';
    $target_people = isset($model['target_people']) ? $model['target_people'] : '';
    $act_sharpen = isset($model['act_sharpen']) ? $model['act_sharpen'] : '';
    $subsidy_type = isset($model['subsidy_type']) ? $model['subsidy_type'] : '';
    $subsidy_amount = isset($model['subsidy_amount']) ? $model['subsidy_amount'] : '';
    $data_send_begin_time = isset($model['data_send_begin_time']) ? $model['data_send_begin_time'] : '';
    $data_send_end_time = isset($model['data_send_end_time']) ? $model['data_send_end_time'] : '';
    $channel = isset($model['channel']) ? $model['channel'] : '';
    $send_rate = isset($model['send_rate']) ? $model['send_rate'] : '';
    $send_mails = isset($model['send_mails']) ? $model['send_mails'] : '';
    if(!empty($page_json)){
        $page_json = htmlspecialchars($page_json);
    }
}

//静态引用的域名配置
if (strpos($_SERVER['HTTP_HOST'], '.d.edaijia')) {
    $static_host = 'http://h5.d.edaijia.cn';
} else {
    $static_host = 'http://h5.edaijia.cn';
}
?>
    <link rel="stylesheet" type="text/css" href="<?php echo $static_host;?>/core/event_modules/common/css/admin.css">
    </head>
    <body>
        <header>
            <h1>e代驾活动配置后台V4</h1>
        </header>
        <form class="form-create">
            <div class="wrapper">
                <section>
                    <h1>基本信息</h1>
                    <p><label>活动中文名称: </label><input required name="name" value="<?php echo $name;?>"></p>
                    <p><label>活动英文名称: </label><input required placeholder="限15字以内英文、数字及_" name="english_name"  value="<?php echo $english_name;?>"></p>
                </section>
            </div>
            <div class="wrapper">
                <section>
                    <h1>页面设置</h1>
                    <fieldset>
                        <legend>文字部分</legend>
                        <p><label>页面标题: </label><input required class="json-item" data-item="page_title"></p>
                        <p><label>参与按钮文案: </label><input required class="json-item" data-item="text_join" value="我要领劵"></p>
                        <p><label>分享按钮文案: </label><input required class="json-item" data-item="text_share" value="分享"></p>
                        <p class="flex"><label>活动规则: </label>
                            <textarea  required rows="4" class="json-item" data-item="text_rule">
                                    <p>仅限3月20日0:00至3月21日7:00使用，结算代驾费时会自动扣除。不与其他优惠累计，不设找零。</p>
                                    <ul>
                                        <li>每个手机号只可领取一次</li>
                                        <li>通过400下单不能使用</li>
                                        <li>本活动不支持e代驾VIP用户</li>
                                        <li>本活动严禁刷单，一经发现，代驾券作废</li>
                                        <li>本活动最终解释权归e代驾所有</li>
                                    </ul>
                            </textarea></p>
                    </fieldset>
                    <fieldset>
                        <legend>UI部分</legend>
                        <p><label>首页头部: </label>
                            <input type="file">
                            <input type="hidden" required class="json-item" data-item="img_top">
                        </p>
                        <p><label>首页底部: </label>
                            <input type="file">
                            <input type="hidden" class="json-item" data-item="img_btm">
                            <label><input type="checkbox" class="use-empty">留空</label>
                        </p>
                        <p><label>成功页头部: </label>
                            <input type="file">
                            <input type="hidden" required class="json-item" data-item="img_top_suc">
                            <label><input type="checkbox" class="use-first-top">使用首页头部url</label>
                        </p>
                        <p><label>成功页头部2: </label>
                            <input type="file">
                            <input type="hidden" required class="json-item" data-item="img_top_suc2">
                            <label><input type="checkbox" class="use-first-top">使用首页头部url</label>
                        </p>
                        <p><label>成功页底部: </label>
                            <input type="file">
                            <input type="hidden" class="json-item" data-item="img_btm_suc">
                            <label><input type="checkbox" class="use-empty">留空</label>
                            <label><input type="checkbox" class="use-first-btm">使用首页底部url</label>
                        </p>
                        <p><label>结束页头部: </label>
                            <input type="file">
                            <input type="hidden" required class="json-item" data-item="img_top_over">
                            <label><input type="checkbox" class="use-first-top">使用首页头部url</label>
                        </p>
                        <p><label>结束页底部: </label>
                            <input type="file">
                            <input type="hidden" class="json-item" data-item="img_btm_over">
                            <label><input type="checkbox" class="use-empty">留空</label>
                            <label><input type="checkbox" class="use-first-btm">使用首页底部url</label>
                        </p>
                        <p><label>断网页底部: </label>
                            <input type="file">
                            <input type="hidden" class="json-item" data-item="img_btm_net">
                            <label><input type="checkbox" class="use-empty">留空</label>
                        </p>
                        <p><label>活动背景色: </label><input required placeholder="例如#ffffff" size="6" class="json-item" data-item="color_common" ></p>
                        <p><label>断网背景色: </label><input required placeholder="例如#ffffff" size="6" class="json-item" data-item="color_neterr" ></p>
                        <p class="text-light"><label>额外定制: </label><input type="checkbox" class="check-custom"> 注意，仅当特殊情况下前端人员配置用</p>
                    </fieldset>
                    <fieldset class="field-custom">
                        <legend>额外定制部分</legend>
                        <p class="flex"><label>CSS_text: </label><textarea  rows="4" class="json-item" data-item="text_css"></textarea></p>
                        <p class="flex"><label>JS_text: </label><textarea  rows="4" class="json-item" data-item="text_js"></textarea></p>
                    </fieldset>
                </section>
                <aside>
                    <h1>预览页面</h1>
                    <iframe id="previewIfr" src="<?php echo $static_host;?>/core/event_modules/module4/preview.html" width="320" height="568" ></iframe>
                    <p>
                        <label>选择页面: </label>
                        <select class="select-page">
                            <option data-page="page-unbind">首页</option>
                            <option data-page="page-binded">成功页</option>
                            <option data-page="page-empty">结束页</option>
                            <option data-page="page-network">断网页</option>
                        </select>
                    </p>
                </aside>
            </div>
            <div class="wrapper">
                <section>
                    <h1>分享设置</h1>
                    <p class="flex"><label>分享标题: </label><input required class="json-item" data-item="text_share_tit"></p>
                    <p class="flex"><label>分享描述: </label><input required class="json-item" data-item="text_share_dec"></p>
                    <p><label>分享图片: </label><input type="file"><input required type="hidden" class="json-item" data-item="img_share"></p>
                </section>
                <aside>
                    <h1>分享预览</h1>
                    <div class="share-preview-wrapper">
                        <h4 class="share-tit"></h4>
                        <img width="50" height="50"  >
                        <p class="share-dec"></p>
                    </div>
                </aside>
            </div>
            <div class="wrapper">
                <section>
                    <h1>其他设置</h1>
                    <fieldset>
                        <legend>业务信息</legend>
                        <p><label>新客优惠券sn码: </label><input required name="bonus_sn" value="<?php echo $bonus_sn;?>"></p>
                        <p><label>老客优惠券sn码: </label><input required name="bonus_sn2" value="<?php echo $bonus_sn2;?>"></p>
                        <p><label>优惠券数量: </label><input  required type="number" min="1" name="bonus_num"  value="<?php echo $bonus_num;?>" ></p>
                        <p>
                            <label>活动开始时间: </label>
                            <input placeholder="格式: 2015-03-26 00:00" required name="begin_time"  value="<?php echo $begin_time;?>" >
                        </p>
                        <p> 
                            <label>活动结束时间: </label>
                            <input placeholder="格式: 2015-03-26 00:00" required name="end_time" value="<?php echo $end_time;?>" >
                        </p>
                        <p><label>活动新客短信文案: </label>
                            <textarea required name="sms"><?php if(!empty($sms)){echo $sms; }?></textarea>
                        </p>
                        <p><label>活动老客短信文案: </label>
                            <textarea required name="sms2"><?php if(!empty($sms2)){echo $sms2; }?></textarea>
                        </p>
                    </fieldset>
                    <fieldset>
                        <legend>财务统计</legend>
                        <p><label>活动目标: </label>
                            <textarea name="act_target"><?php if(!empty($act_target)){echo $act_target; }?></textarea>
                        </p>
                        <p><label>目标人群: </label>
                            <textarea name="target_people"><?php if(!empty($target_people)){echo $target_people; }?></textarea>
                        </p>
                        <p><label>活动渠道: </label>
                            <select name="act_sharpen" multiple>
                                <option value="1" <?php if($act_sharpen == 1){echo 'selected'; }?>>短信</option>
                                <option value="2" <?php if($act_sharpen == 2){echo 'selected'; }?>>客户端推送</option>
                                <option value="3" <?php if($act_sharpen == 3){echo 'selected'; }?>>微信</option>
                                <option value="4" <?php if($act_sharpen == 4){echo 'selected'; }?>>其他</option>
                            </select>
                        </p>
                        <p><label>补贴形式: </label>
                            <select name="subsidy_type">
                                <option value="1" <?php if($subsidy_type == 1){echo 'selected'; }?>>优惠券</option>
                            </select>
                        </p>
                        <p>
                            <label>补贴金额: </label>
                            <input type="number" name="subsidy_amount"  value="<?php echo $subsidy_amount;?>" >
                        </p>
                    </fieldset>
                    <fieldset>
                        <legend>数据统计</legend>
                        <p>
                            <label>数据开始发送时间: </label>
                            <input placeholder="格式: 2015-03-26 00:00" required name="data_send_begin_time"  value="<?php echo $data_send_begin_time;?>" >
                        </p>
                        <p> 
                            <label>数据结束发送时间: </label>
                            <input placeholder="格式: 2015-03-26 00:00" required name="data_send_end_time" value="<?php echo $data_send_end_time;?>" >
                        </p>
                        <p>
                            <label>合作方渠道号: </label>
                            <input required placeholder="多个渠道号以;分隔" name="channel"  value="<?php echo $channel;?>" >
                        </p>
                        <p><label>发送频率: </label>
                            <select required name="send_rate">
                                <option value="1" <?php if($send_rate == 1){echo 'selected'; }?>>每天</option>
                                <option value="2" <?php if($send_rate == 2){echo 'selected'; }?>>每周</option>
                                <option value="3" <?php if($send_rate == 3){echo 'selected'; }?>>每月</option>
                            </select>
                        </p>
                        <p><label>收件人邮箱: </label>
                            <textarea required placeholder="多个邮件地址以;分隔" name="send_mails"><?php if(!empty($send_mails)){echo $send_mails; }?></textarea>
                        </p>
                    </fieldset>
                </section>
                <aside>
                    <input type="hidden" name="page_json" value="<?php echo $page_json;?>">
                    <input type="hidden" name="template_id" value="4">
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <input type="submit" class="ui-button-blue btn-submit" value="生成活动">
                    <div class="ev-links">
                        <p><label>活动链接: </label><span class="ev-link"></span></p>
                        <p><label>微信链接: </label><span class="wx-link"></span></p>
                        <!--<p><label>活动二维码: </label><img class="qr-img" src="" width="100" height="100"></p>-->
                    </div>
                </aside>
            </div>
        </form>
        <script type="text/javascript" src="<?php echo $static_host;?>/core/libs/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $static_host;?>/core/event_modules/module4/js/admin.js"></script>
    </body>
</html>