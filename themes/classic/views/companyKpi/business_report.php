<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 14-1-11
 * Time: 下午11:40
 * To change this template use File | Settings | File Templates.
 */
/*
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_STO.'www/js/swfobject.js');
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.uploadify.v2.1.0.min.js');
$cs->registerScriptFile(SP_URL_STO.'www/js/jquery.validate.js');
*/
?>
<div class="container">

<h1>市场推广上报</h1>

<div class="row-fluid">
    <div class="span12">
        <div class="row-fluid">
            <div class="span12">
                <h5>1、推广信息月份</h5>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <?php
                $date_list = array(
                    date('Ym', time()) => date('Y年m月', time()),
                    date('Ym', strtotime('-1 month')) => date('Y年m月', strtotime('-1 month')),
                );
                ?>
                <?php echo CHtml::dropDownList('use_date', '', $date_list);?>
            </div>
        </div>

    </div>
</div>


<div class="row-fluid">
    <div class="span12">
        <div class="row-fluid">
            <div class="span12">
                <h5>2.请选择推广的商家类型</h5>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <?php echo CHtml::radioButtonList('type_id', '', CompanyKpiCommon::$business_list, array('class'=> 'module', 'separator'=>'', 'template'=>'<label class="checkbox inline">{input}   {label}</label>'));?>
            </div>
        </div>
    </div>
</div>


<div class="row-fluid">
    <div class="span12" id="module_list">
        <div class="row-fluid">
            <div class="span12">
                <h5>3.请填写商家相关信息</h5>
            </div>
        </div>
        <!--此处模板列表-->
    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function(){
        /*
        * 选择提交模块
         */
        jQuery('.module').click(function(){
            $('.module').each(function(){
                var module_id = jQuery(this).val();

                if (jQuery(this).attr('checked') == 'checked') {
                    jQuery.get(
                        '<?php echo Yii::app()->createUrl('companyKpi/businessModule');?>',
                        {
                            business_type : module_id
                        },
                        function(d) {
                            if (d) {
                                jQuery('#module_list').append(d);
                            }
                        }
                    );
                } else {
                    jQuery('#module_type_'+module_id).remove();
                }
            });

        });
    });
</script>
