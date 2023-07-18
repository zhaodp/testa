<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 14-2-21
 * Time: 下午3:05
 * To change this template use File | Settings | File Templates.
 */
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_STO.'www/js/swfobject.js');
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.uploadify.v2.1.0.min.js');
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.validate.js');
?>

<!--各模块模板信息-->
<!--餐厅信息-->
<?php if ($business_type == CompanyKpiCommon::TYPE_RESTAURANT) { ?>
<form id="module_type_<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>">
    <div class="row-fluid" >
        <div class="span12">
            <div class="alert alert-info" style="margin-bottom: 0px">餐厅信息</div>
            <table class="table table-bordered">
                <tr>
                    <td class="span2">店名:</td><td><input type="text" required="true" name="info[name]" value=""/></td>
                    <td>人均消费:</td><td><input type="text" required="true" number="true" name="info[options][consume]" value=""/></td>
                    <td>桌数:</td><td><input type="text" required="true" number="true" name="info[options][table_num]" value=""/></td>
                </tr>
                <tr>
                    <td>联系人:</td><td><input type="text" required="true" name="info[contact]" value=""/></td>
                    <td>电话:</td><td><input isPhone="true" type="text" required="true" name="info[phone]" value=""/></td>
                    <td>备用电话:</td><td><input isPhone="true" type="text" required="true" name="info[contact_phone]" value=""/></td>
                </tr>
                <tr>
                    <td>地址:</td><td colspan="5"><input type="text" required="true" class="span8" name="info[address]"/></td>
                </tr>
                <tr>
                    <td>大众点评链接:</td><td colspan="5"><input type="url" class="span8" name="info[options][link]"/></td>
                </tr>
                <tr>
                    <td>内容备注:</td><td colspan="5"><textarea cols="900" rows="3" name="info[remarks]" style="width:665px;"></textarea></td>
                </tr>
                <tr>
                    <td>店头照片</td>
                    <td><input type="file"  func="uploadify" file_target="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_banner" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_file_banner"/></td>
                    <td colspan="4"><input required="true" isPic="true" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_banner" type="hidden" name="info[images][banner]" /></td>
                </tr>
                <tr>
                    <td>卡片摆放</td>
                    <td><input type="file" func="uploadify" file_target="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_card" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_file_card"/></td>
                    <td colspan="4"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_card" type="hidden" name="info[images][card]" /></td>
                </tr>
                <tr>
                    <td>贴牌</td>
                    <td><input type="file" func="uploadify" file_target="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_branded" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_file_branded" /></td>
                    <td colspan="4"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_branded" type="hidden" name="info[images][branded]" /></td>
                </tr>
                <tr>
                    <td>物料摆放</td>
                    <td><input type="file" func="uploadify" file_target="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_materials" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_file_materials"/></td>
                    <td colspan="4"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_materials" type="hidden" name="info[images][materials]" /></td>
                </tr>
                <tr>
                    <td>其他照片</td>
                    <td><input type="file" func="uploadify" file_target="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_others" id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_file_others" /></td>
                    <td colspan="4"><input id="<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>_image_others" type="hidden" name="info[images][others]" /></td>
                    <!---->
                    <input type="hidden" name="info[type_id]" value="<?php echo CompanyKpiCommon::TYPE_RESTAURANT;?>"/>
                    <input type="hidden" name="info[use_date]" value=""/>
                    <input type="hidden" value="<?php echo $city_id; ?>" name="info[city_id]" />
                    <input type="hidden" value="<?php echo $operator; ?>" name="info[operator]" />
                </tr>
                <tr>
                    <td colspan="6"><a id="btn_test" href="javascript:void(0)" func="submit" type="<?php echo CompanyKpiCommon::TYPE_RESTAURANT;?>" class="btn btn-success" >保存</a></td>
                </tr>
            </table>
        </div>
    </div>
</form>
<?php } ?>
<!--餐厅信息结束-->

<!--银行信息-->
<?php if ($business_type == CompanyKpiCommon::TYPE_BANK) { ?>
<form id="module_type_<?php echo CompanyKpiCommon::TYPE_BANK; ?>" >
    <div class="row-fluid">
        <div class="span12">
            <div class="alert alert-info" style="margin-bottom: 0px">银行信息</div>
            <table class="table table-bordered">
                <tr>
                    <td>银行名称:</td><td><input type="text" required="true" name="info[name]"/></td>
                    <td>联系人:</td><td><input type="text" required="true" name="info[contact]"/></td>
                </tr>
                <tr>
                    <td>电话:</td><td><input type="text" isPhone="true" required="true" name="info[phone]" /></td>
                    <td>备用电话:</td><td><input type="text" isPhone="true" required="true" name="info[contact_phone]" /></td>
                </tr>
                <tr>
                    <td>地址:</td><td colspan="5"><input type="text" required="true" class="span8" name="info[address]"/></td>
                </tr>
                <tr>
                    <td>内容备注:</td><td colspan="5"><textarea cols="900" rows="3" name="info[remarks]" style="width:670px;"></textarea></td>
                </tr>
                <tr>
                    <td>店头照片</td>
                    <td><input type="file" func="uploadify" file_target="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_image_banner" id="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_file_banner"/></td>
                    <td colspan="2"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_image_banner" type="hidden" name="info[images][banner]"/></td>
                </tr>
                <tr>
                    <td>名片照片</td>
                    <td><input type="file" func="uploadify" file_target="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_image_card" id="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_file_card"/></td>
                    <td colspan="2"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_image_card" type="hidden" name="info[images][card]"/></td>
                </tr>
                <tr>
                    <td>其他照片</td>
                    <td><input type="file" func="uploadify" file_target="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_image_others" id="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_file_others"/></td>
                    <td colspan="2"><input id="<?php echo CompanyKpiCommon::TYPE_BANK; ?>_image_others" type="hidden" name="info[images][others]"/></td>
                    <!---->
                    <input type="hidden" name="info[type_id]" value="<?php echo CompanyKpiCommon::TYPE_BANK;?>" />
                    <input type="hidden" value="" name="info[use_date]" />
                    <input type="hidden" value="<?php echo $city_id; ?>" name="info[city_id]" />
                    <input type="hidden" value="<?php echo $operator; ?>" name="info[operator]" />
                </tr>
                <tr>
                    <td colspan="4"><a href="javascript:void(0)" func="submit" type="<?php echo CompanyKpiCommon::TYPE_BANK;?>" class="btn btn-success">保存</a></td>
                </tr>
            </table>
        </div>
    </div>
</form>
<?php } ?>
<!--银行信息结束-->

<!--KTV信息-->
<?php if ($business_type == CompanyKpiCommon::TYPE_KTV) { ?>
<form id="module_type_<?php echo CompanyKpiCommon::TYPE_KTV; ?>">
    <div class="row-fluid" >
        <div class="span12">
            <div class="alert alert-info" style="margin-bottom: 0px">KTV信息</div>
            <table class="table table-bordered">
                <tr>
                    <td class="span4">店名:</td><td><input type="text" required="true" name="info[name]"/></td>
                    <td>联系人:</td><td><input type="text" required="true" name="info[contact]"/></td>
                </tr>
                <tr>
                    <td>电话:</td><td><input type="text" isPhone="true" required="true" name="info[phone]"/></td>
                    <td>备用电话:</td><td><input type="text" isPhone="true" required="true" name="info[contact_phone]"/></td>
                </tr>
                <tr>
                    <td>地址:</td><td colspan="3"><input type="text" required="true" class="span8" name="info[address]"/></td>
                </tr>
                <!--
                <tr>
                    <td>大众点评链接:</td><td colspan="5"><input type="text"  class="span8" name="info[options][link]"/></td>
                </tr>
                -->
                <tr>
                    <td>内容备注:</td><td colspan="5"><textarea cols="900" rows="3" name="info[remarks]" style="width:740px;" ></textarea></td>
                </tr>
                <tr>
                    <td>活动内容:</td><td colspan="5"><textarea required="true" cols="900" rows="3" name="info[options][activity]" style="width:740px;"></textarea></td>
                </tr>
                <tr>
                    <td>店头照片</td>
                    <td><input type="file" file_target="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_image_banner" func="uploadify" id="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_file_banner"/></td>
                    <td colspan="2"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_image_banner" type="hidden" name="info[images][banner]" /></td>
                </tr>
                <tr>
                    <td>卡片摆放</td>
                    <td><input type="file" file_target="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_image_card" func="uploadify" id="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_file_card"/></td>
                    <td colspan="2"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_image_card" type="hidden" name="info[images][card]" /></td>
                </tr>
                </tr>
                <tr>
                    <td>贴牌</td>
                    <td><input type="file" file_target="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_image_branded" func="uploadify" id="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_file_branded"/></td>
                    <td colspan="2"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_image_branded" type="hidden" name="info[images][branded]" /></td>
                </tr>
                </tr>
                <tr>
                    <td>其他照片</td>
                    <td><input type="file" file_target="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_image_others" func="uploadify" id="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_file_others"/></td>
                    <td colspan="2"><input id="<?php echo CompanyKpiCommon::TYPE_KTV; ?>_image_others" type="hidden" name="info[images][others]" /></td>
                    <!---->
                    <input type="hidden" name="info[type_id]" value="<?php echo CompanyKpiCommon::TYPE_KTV;?>"/>
                    <input type="hidden" value="<?php echo $city_id; ?>" name="info[city_id]" />
                    <input type="hidden" name="info[use_date]" value=""/>
                    <input type="hidden" value="<?php echo $operator; ?>" name="info[operator]" />
                </tr>
                </tr>
                <tr>
                    <td colspan="4"><a href="javascript:void(0)" func="submit" type="<?php echo CompanyKpiCommon::TYPE_KTV;?>" class="btn btn-success">保存</a></td>
                </tr>
            </table>
        </div>
    </div>
</form>
<?php } ?>
<!--KTV信息结束-->

<!--4S店信息-->
<?php if ($business_type == CompanyKpiCommon::TYPE_4S) { ?>
<form id="module_type_<?php echo CompanyKpiCommon::TYPE_4S; ?>">
    <div class="row-fluid">
        <div class="span12">
            <div class="alert alert-info" style="margin-bottom: 0px">4S店信息</div>
            <table class="table table-bordered">
                <tr>
                    <td class="span4">店名:</td><td><input type="text" required="true" name="info[name]"/></td>
                    <td>联系人:</td><td><input type="text" required="true" name="info[contact]"/></td>
                </tr>
                <tr>
                    <td>电话:</td><td><input type="text" required="true" isPhone="true"  name="info[phone]" /></td>
                    <td>备用电话:</td><td><input type="text" required="true" isPhone="true" name="info[contact_phone]" /></td>
                </tr>
                <tr>
                    <td>地址:</td><td colspan="3"><input type="text" class="span8" required="true" name="info[address]"/></td>
                </tr>
                <!--
                <tr>
                    <td>大众点评链接:</td><td colspan="3"><input type="text"  class="span8" name="info[options][link]"/></td>
                </tr>
                -->
                <tr>
                    <td>内容备注:</td><td colspan="3"><textarea cols="900" rows="3" name="info[remarks]" style="width: 560px;"></textarea></td>
                </tr>
                <tr>
                    <td>活动内容:</td><td colspan="3"><textarea cols="200" rows="3" required="true" name="info[options][activity]" style="width: 560px;"></textarea></td>
                </tr>
                <tr>
                    <td>店头照片</td>
                    <td><input type="file" file_target="<?php echo CompanyKpiCommon::TYPE_4S; ?>_image_banner" href="javascript:void(0)" func="uploadify" id="<?php echo CompanyKpiCommon::TYPE_4S; ?>_file_banner"/></td>
                    <td colspan="2"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_4S; ?>_image_banner" type="hidden" name="info[images][banner]" /></td>
                </tr>
                <tr>
                    <td>卡片摆放</td>
                    <td><input type="file" file_target="<?php echo CompanyKpiCommon::TYPE_4S; ?>_image_card" href="javascript:void(0)" func="uploadify" id="<?php echo CompanyKpiCommon::TYPE_4S; ?>_file_card" /></td>
                    <td colspan="2"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_4S; ?>_image_card" type="hidden" name="info[images][card]" /></td>
                </tr>
                <tr>
                    <td>贴牌</td>
                    <td><input type="file" file_target="<?php echo CompanyKpiCommon::TYPE_4S; ?>_image_branded" href="javascript:void(0)" func="uploadify" id="<?php echo CompanyKpiCommon::TYPE_4S; ?>_file_branded" /></td>
                    <td colspan="2"><input required="true" id="<?php echo CompanyKpiCommon::TYPE_4S; ?>_image_branded" type="hidden" name="info[images][branded]" /></td>
                </tr>
                <tr>
                    <td>其他照片</td>
                    <td><input type="file" file_target="<?php echo CompanyKpiCommon::TYPE_4S; ?>_image_others" href="javascript:void(0)" func="uploadify" id="<?php echo CompanyKpiCommon::TYPE_4S; ?>_file_others" /></td>
                    <td colspan="2"><input id="<?php echo CompanyKpiCommon::TYPE_4S; ?>_image_others" type="hidden" name="info[images][others]" /></td>
                    <!---->
                    <input type="hidden" name="info[type_id]" value="<?php echo CompanyKpiCommon::TYPE_4S;?>" />
                    <input type="hidden" value="<?php echo $city_id; ?>" name="info[city_id]" />
                    <input type="hidden" name="info[use_date]" value=""/>
                    <input type="hidden" value="<?php echo $operator; ?>" name="info[operator]" />
                </tr>
                <tr>
                    <td colspan="4"><a href="javascript:void(0)" func="submit" type="<?php echo CompanyKpiCommon::TYPE_4S;?>" class="btn btn-success" >保存</a></td>
                </tr>
            </table>
        </div>
    </div>
</form>
<?php } ?>
<!--4S店信息结束-->



<script>
    jQuery(document).ready(function(){
        /*
        *  上传插件配置参数
        */
        var uploadify_config = {
            'uploader'       : '<?php echo SP_URL_STO.'www/js/uploadify.swf'; ?>',
            'script'         : '<?php echo Yii::app()->createUrl('image/upload');?>',
            'cancelImg'      : '<?php echo SP_URL_STO.'www/images/cancel.png'; ?>',
            'folder'         : 'business',
            'queueID'        : 'fileQueue',
            'buttonText'     : 'upload',
            'auto'           : true,
            'multi'          : true,
            'displayData'    : 'speed',
            //'fileDesc'       : 'jpg文件或jpeg文件',
            //'fileExt'        : '*.jpg;*.png',
            'scriptData'     : {bucketname:'edaijia'},
            onComplete : function(evt, queueID, fileObj, response, data){
                eval("var theJsonValue = "+response);
                var img_url = theJsonValue.data;
                var file_target_id = jQuery(evt.target).attr('file_target');
                var file_target = jQuery('#'+file_target_id);
                file_target.val(img_url);
                file_target.parent().append('<img style="height:100px;" src="'+img_url+'" height="100" />');
            },
            onError : function(a, b, c, d){
                if (d.status == 404)
                    alert('Could not find upload script. Use a path relative to: '+'<?= getcwd() ?>');
                else if (d.type === "HTTP")
                    alert('error '+d.type+": "+d.status);
                else if (d.type ==="File Size")
                    alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
                else
                    alert('error '+d.type+": "+d.text);
            }
        }

        /*
        * 绑定上传事件
        */
        jQuery('[func="uploadify"]').each(function(){
            var file_target = jQuery(this).attr('file_target');
            var queueID = file_target.replace('image', 'div');
            jQuery('#'+file_target).parent().attr('id', queueID);
            uploadify_config.queueID = queueID;
            jQuery(this).uploadify(uploadify_config);
            jQuery(this).removeAttr('func');
        });

        /*
        * 绑定提交事件
        */
        jQuery('[func="submit"]').click(function(){
            var act = '<?php echo $act; ?>';
            var type_id = jQuery(this).attr('type');
            var module_id = 'module_type_'+type_id;
            var module = jQuery('#'+module_id);
            if (act == 'create') {
                var use_date = jQuery('#use_date').val();
                module.find('[name="info[use_date]"]').val(use_date);
            }
            var post_data = module.serialize();
            var is_valid = module.valid();
            if (is_valid) {
                jQuery.post(
                    "<?php echo Yii::app()->createUrl('companyKpi/businessReport', array('act'=>$act, 'id'=>$id));?>",
                    post_data,
                    function(d){
                        if (d.status) {
                            if (act == 'create') {
                                jQuery('.module').each(function(){
                                    if (jQuery(this).val() == type_id) {
                                        jQuery(this).click();
                                    }
                                });
                                jQuery('#module_type_'+type_id).hide();
                                jQuery('#module_type_'+type_id).find('input').val('');
                                jQuery('#module_type_'+type_id).find('textarea').val('');
                            } else {
                                alert('编辑成功');
                            }
                        }
                    },
                    'json'
                );
            } else {
                alert('输入有误');
            }
        });

        //jQuery('form').validate();

        jQuery.extend(jQuery.validator.messages, {
            required: "必填字段",
            remote: "请修正该字段",
            email: "请输入正确格式的电子邮件",
            url: "请输入合法的网址",
            date: "请输入合法的日期",
            dateISO: "请输入合法的日期 (ISO).",
            number: "请输入合法的数字",
            digits: "只能输入整数",
            creditcard: "请输入合法的信用卡号",
            equalTo: "请再次输入相同的值",
            accept: "请输入拥有合法后缀名的字符串",
            maxlength: jQuery.validator.format("请输入一个 长度最多是 {0} 的字符串"),
            minlength: jQuery.validator.format("请输入一个 长度最少是 {0} 的字符串"),
            rangelength: jQuery.validator.format("请输入 一个长度介于 {0} 和 {1} 之间的字符串"),
            range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
            max: jQuery.validator.format("请输入一个最大为{0} 的值"),
            min: jQuery.validator.format("请输入一个最小为{0} 的值")
        });

        jQuery.extend(jQuery.validator.defaults, {
            errorClass : 'text-error',
            ignore : ''
        });

        jQuery.validator.addMethod("isPhone", function(value,element) {
            var length = value.length;
            var mobile = /^(((13[0-9]{1})|(15[0-9]{1}))+\d{8})$/;
            var tel = /^\d{3,4}-?\d{7,9}$/;
            return this.optional(element) || (tel.test(value) || mobile.test(value));
        }, "请正确填写您的联系电话");

        /*
        *  向模块赋值
        */

        var data = <?php echo is_array($data) && count($data) ? json_encode($data) : json_encode(array()) ?>;

        var act = '<?php echo $act; ?>';

        var module_type = <?php echo $business_type; ?>;

        var data_length = 0;

        jQuery.each(data, function(i,v){
            data_length++;
        });

        if (act == 'update' && data_length > 0) {
            var module = jQuery('#module_type_'+module_type);
            jQuery.each(data, function(i, v){
                if (i != 'options' && i != 'images') {
                    //module.find('[name="info['+i+']"]').val(v);
                    module.find('[name="info['+i+']"]').attr('value', v);
                }

                if (i == 'options' && typeof v == 'object' && v) {
                    jQuery.each(v, function(opt_name, opt_value){
                        module.find('[name="info[options]['+opt_name+']"]').val(opt_value);
                    });
                }

                if (i == 'images' && typeof v == 'object' && v) {
                    jQuery.each(v, function(opt_name, opt_value){
                        var img_module = module.find('[name="info[images]['+opt_name+']"]');
                        img_module.val(opt_value);
                        img_module.parent().append('<img src="'+opt_value+'" style="height: 200px;"/>');
                    });
                }
            });
        }

    });
</script>