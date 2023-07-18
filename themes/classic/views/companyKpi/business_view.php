<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 14-2-24
 * Time: 上午10:28
 * To change this template use File | Settings | File Templates.
 */
$cs=Yii::app()->clientScript;
$fancybox_js_path = SP_URL_STO.'www/js/fancybox';

//<!-- Add jQuery library -->
Yii::app()->clientScript->registerCoreScript('jquery');

//<!-- Add mousewheel plugin (this is optional) -->
$cs->registerScriptFile($fancybox_js_path.'/lib/jquery.mousewheel.pack.js?v=3.1.3');

//<!-- Add fancyBox main JS and CSS files -->
$cs->registerScriptFile($fancybox_js_path.'/source/jquery.fancybox.pack.js?v=2.1.5');
$cs->registerCssFile($fancybox_js_path.'/source/jquery.fancybox.css?v=2.1.5');

//<!-- Add Button helper (this is optional) -->
$cs->registerCssFile($fancybox_js_path.'/source/helpers/jquery.fancybox-buttons.css?v=1.0.5');
$cs->registerScriptFile($fancybox_js_path.'/source/helpers/jquery.fancybox-buttons.js?v=1.0.5');

//<!-- Add Thumbnail helper (this is optional) -->
$cs->registerCssFile($fancybox_js_path.'/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7');
$cs->registerScriptFile($fancybox_js_path.'/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7');

//<!-- Add Media helper (this is optional) -->
$cs->registerScriptFile($fancybox_js_path.'/source/helpers/jquery.fancybox-media.js?v=1.0.6');

?>

<?php $business_type = $data['type_id'];?>

<!--各模块模板信息-->
<!--餐厅信息-->
<?php if ($business_type == CompanyKpiCommon::TYPE_RESTAURANT) { ?>
    <div class="row-fluid" >
        <div class="span12">
            <div class="alert alert-info">餐厅信息</div>
            <form id="module_type_<?php echo CompanyKpiCommon::TYPE_RESTAURANT; ?>">
            <table class="table table-bordered">
                <tr>
                    <td class="span2">店名:</td><td><?php echo $data['name'];?></td>
                    <td>人均消费:</td><td><?php echo $data['options']['consume'];?></td>
                    <td>桌数:</td><td><?php echo $data['options']['table_num'];?></td>
                </tr>
                <tr>
                    <td>联系人:</td><td><?php echo $data['contact'];?></td>
                    <td>电话:</td><td><?php echo $data['phone'];?></td>
                    <td>备用电话:</td><td><?php echo $data['contact_phone']?></td>
                </tr>
                <tr>
                    <td>地址:</td><td colspan="5"><?php echo $data['address']; ?></td>
                </tr>
                <tr>
                    <td>大众点评链接:</td><td colspan="5"><?php echo $data['options']['link']; ?></td>
                </tr>
                <tr>
                    <td>内容备注:</td><td colspan="5"><?php echo $data['remarks'];?></td>
                </tr>
                <tr>
                    <td>店头照片</td>
                    <td colspan="2">
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['banner']; ?>">
                            <img width="200" src="<?php echo $data['images']['banner'];?>" name="info[images][banner]"  />
                        </a>
                    </td>
                    <td>卡片摆放</td>
                    <td colspan="2">
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['card']; ?>">
                            <img width="200" src="<?php echo $data['images']['card'];?>" name="info[images][card]" />
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>贴牌</td>
                    <td colspan="2">
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['branded']; ?>">
                            <img width="200" src="<?php echo $data['images']['branded'];?>" name="info[images][branded]" />
                        </a>
                    </td>
                    <td>物料摆放</td>
                    <td colspan="2">
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['materials']; ?>">
                            <img width="200" src="<?php echo $data['images']['materials']?>" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>其他照片</td>
                    <td colspan="2">
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['others']; ?>">
                            <img width="200" src="<?php echo $data['images']['others'] ?>" />
                        </a>
                    </td>
                    <td colspan="4"></td>
                    <!---->
                    <input type="hidden" name="info[type_id]" value="<?php echo CompanyKpiCommon::TYPE_RESTAURANT;?>"/>
                    <input type="hidden" name="info[use_date]" value=""/>
                    <input type="hidden" value="<?php echo $data['city_id']; ?>" name="info[city_id]" />
                    <input type="hidden" value="<?php echo $data['operator']; ?>" name="info[operator]" />
                </tr>
            </table>
            </form>
        </div>
    </div>
<?php } ?>
<!--餐厅信息结束-->

<!--银行信息-->
<?php if ($business_type == CompanyKpiCommon::TYPE_BANK) { ?>
<form id="module_type_<?php echo CompanyKpiCommon::TYPE_BANK; ?>" >
    <div class="row-fluid">
        <div class="span12">
            <div class="alert alert-info">银行信息</div>
            <table class="table table-bordered">
                <tr>
                    <td>银行名称:</td><td><?php echo $data['name'];?></td>
                    <td>联系人:</td><td><?php echo $data['contact'];?></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>电话:</td><td><?php echo $data['phone'];?></td>
                    <td>备用电话:</td><td><?php echo $data['contact_phone'];?></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>地址:</td><td colspan="5"><?php echo $data['address'];?></td>
                </tr>
                <tr>
                    <td>内容备注:</td><td colspan="5"><?php echo $data['remarks'];?></td>
                </tr>
                <tr>
                    <td>店头照片</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['banner']; ?>">
                            <img width="200" src="<?php echo $data['images']['banner']; ?>" />
                        </a>
                    </td>
                    <td>名片照片</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['card']; ?>">
                            <img width="200" src="<?php echo $data['images']['card']; ?>"/>
                        </a>
                    </td>
                    <td>其他照片</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['others']; ?>">
                            <img width="200" src="<?php echo $data['images']['others']; ?>"/>
                        </a>
                    </td>
                </tr>
                    <!---->
                    <input type="hidden" name="info[type_id]" value="<?php echo CompanyKpiCommon::TYPE_BANK;?>" />
                    <input type="hidden" value="use_date" name="info[use_date]" />
                    <input type="hidden" value="<?php echo $data['city_id']; ?>" name="info[city_id]" />
                    <input type="hidden" value="<?php echo $data['operator']; ?>" name="info[operator]" />
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
            <div class="alert alert-info">KTV信息</div>
            <table class="table table-bordered">
                <tr>
                    <td class="span4">店名:</td><td><?php echo $data['name']; ?></td>
                    <td>联系人:</td><td><?php echo $data['contact']; ?></td>
                </tr>
                <tr>
                    <td>电话:</td><td><?php echo $data['phone']; ?></td>
                    <td>备用电话:</td><td><?php echo $data['contact_phone']; ?></td>
                </tr>
                <tr>
                    <td>地址:</td><td colspan="3"><?php echo $data['address']; ?></td>
                </tr>
                <!--
                <tr>
                    <td>大众点评链接:</td><td colspan="5"><input type="text"  class="span8" name="info[options][link]"/></td>
                </tr>
                -->
                <tr>
                    <td>内容备注:</td><td colspan="5"><?php echo $data['remarks']; ?></td>
                </tr>
                <tr>
                    <td>活动内容:</td><td colspan="5"><?php echo $data['options']['activity']; ?></td>
                </tr>
                <tr>
                    <td>店头照片</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['banner']; ?>">
                            <img width="200" src="<?php echo $data['images']['banner']; ?>" />
                        </a>
                    </td>
                    <td>卡片摆放</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['card']; ?>">
                            <img width="200" src="<?php echo $data['images']['card']; ?>" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>贴牌</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['branded']; ?>">
                            <img width="200" src="<?php echo $data['images']['branded']; ?>" />
                        </a>
                    </td>
                    <td>其他照片</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['others']; ?>">
                            <img width="200" src="<?php echo $data['images']['others']; ?>" />
                        </a>
                    </td>
                </tr>
                </tr>
                <!---->
                <input type="hidden" name="info[type_id]" value="<?php echo CompanyKpiCommon::TYPE_KTV;?>"/>
                <input type="hidden" value="<?php echo $data['city_id']; ?>" name="info[city_id]" />
                <input type="hidden" name="info[use_date]" value=""/>
                <input type="hidden" value="<?php echo $data['operator']; ?>" name="info[operator]" />
            </table>
        </div>
    </div>
</form>
<?php } ?>
<!--KTV信息结束-->

<!--4S店信息-->
<?php if ($business_type == CompanyKpiCommon::TYPE_4S) { ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="alert alert-info">4S店信息</div>
            <form id="module_type_<?php echo CompanyKpiCommon::TYPE_4S; ?>">
            <table class="table table-bordered">
                <tr>
                    <td class="span4">店名:</td><td><?php echo $data['name'];?></td>
                    <td>联系人:</td><td><?php echo $data['contact']; ?>></td>
                </tr>
                <tr>
                    <td>电话:</td><td><?php echo $data['phone']; ?></td>
                    <td>备用电话:</td><td><?php echo $data['contact_phone']; ?></td>
                </tr>
                <tr>
                    <td>地址:</td><td colspan="3"><?php echo $data['address']; ?></td>
                </tr>
                <!--
                <tr>
                    <td>大众点评链接:</td><td colspan="3"><input type="text"  class="span8" name="info[options][link]"/></td>
                </tr>
                -->
                <tr>
                    <td>内容备注:</td><td colspan="3"><?php echo $data['remarks']; ?></td>
                </tr>
                <tr>
                    <td>活动内容:</td><td colspan="3"><?php echo $data['options']['activity']; ?></td>
                </tr>
                <tr>
                    <td>店头照片</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['banner']; ?>">
                            <img width="200" src="<?php echo $data['images']['banner']; ?>" />
                        </a>
                    </td>
                    <td>卡片摆放</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['card']; ?>">
                            <img width="200" src="<?php echo $data['images']['card']; ?>" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>贴牌</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['branded']; ?>">
                            <img width="200" src="<?php echo $data['images']['branded']; ?>" />
                        </a>
                    </td>
                    <td>其他照片</td>
                    <td>
                        <a class="fancybox-buttons" data-fancybox-group="button" href="<?php echo $data['images']['others']; ?>">
                            <img width="200" src="<?php echo $data['images']['others']; ?>" />
                        </a>
                    </td>
                </tr>
                    <!---->
                    <input type="hidden" name="info[type_id]" value="<?php echo CompanyKpiCommon::TYPE_4S;?>" />
                    <input type="hidden" value="<?php echo $data['city_id']; ?>" name="info[city_id]" />
                    <input type="hidden" name="info[use_date]" value="<?php echo $data['city_id'];?>"/>
                    <input type="hidden" value="<?php echo $data['operator']; ?>" name="info[operator]" />

            </table>
            </form>
        </div>
    </div>
<?php } ?>
<!--4S店信息结束-->
<script>
    jQuery(document).ready(function(){
        $('.fancybox-thumbs').fancybox({
            prevEffect : 'none',
            nextEffect : 'none',

            closeBtn  : false,
            arrows    : false,
            nextClick : true,

            helpers : {
                thumbs : {
                    width  : 50,
                    height : 50
                }
            }
        });

        /*
         *  Button helper. Disable animations, hide close button, change title type and content
         */

        $('.fancybox-buttons').fancybox({
            openEffect  : 'none',
            closeEffect : 'none',

            prevEffect : 'none',
            nextEffect : 'none',

            closeBtn  : false,

            helpers : {
                title : {
                    type : 'inside'
                },
                buttons	: {}
            },

            afterLoad : function() {
                this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
            }
        });
    });
</script>

