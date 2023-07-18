<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-28
 * Time: 上午11:33
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '分公司绩效考核设置';
?>

<div class="container">
    <h1>分公司绩效考核设置</h1>

    <div class="mini-layout fluid" style="height: 100px;">
        <div class="span1">
            城市选择
        </div>
        <div class="span8">
            <?php
            $model = new CompanyKpiCommon();
            $city_arr = $model->getBackgroundCityList($type);
            foreach($city_arr as $c_id=>$c_name) {
            ?>
            <label class="checkbox" style="margin-right: 20px; float: left">
                <input class="set_city" type="checkbox" name="set_cityname" value="<?php echo $c_id;?>"><?php echo $c_name;?>
            </label>
            <?php
            }
            ?>
        </div>
        <div class="span2">
            <a id="filter" class="btn btn-primary" href="javascript:void(0)">过滤已设置城市</a>
            <a id="show" class="btn btn-primary" style="display: none" href="javascript:void(0)">显示全部城市</a>
        </div>
        <div class="span2" style="margin-top:20px;">
            <span style="padding:5px;margin-top:5px;"><a id="checkall" class="btn btn-primary" href="javascript:void(0)">全选</a></span>
            <span style="padding:5px;margin-top:5px;"><a id="invert" class="btn btn-primary" href="javascript:void(0)">反选</a></span>
        </div>
    </div>

    <div class="row-fluid" style="display: none">
        <div class="span3"><input type="text" name="use_date" value="<?php echo date('Ym', time());?>"/></div>
    </div>

    <div class="row-fluid">
        <div class="span6">
            <label class="checkbox inline">
            <?php echo CHtml::checkBox('copy', false);?>从已有模版复制
            </label>
            <span id="mould_container" style="display: none">
            <?php $mould_list[0] = '请选择模板'; ksort($mould_list);?>
            <?php echo CHtml::dropDownList('mould_list', '',$mould_list, array('style'=>'width:160px;'));?>
            </span>
        </div>
    </div>

    <div id="container">
        <form id="frm">

        </form>
    </div>

    <div>
        <button type="button" class="btn btn-primary" data-loading-text="正在保存中...." id="submit">保存</button>
    </div>
</div>

<script type="text/javascript" >
    var mould_list = <?php unset($mould_list[0]); echo is_array($mould_list)&&count($mould_list) ? json_encode(array_keys($mould_list)) : json_encode(array());?>;

    jQuery(document).ready(function(){
        jQuery.get(
            '<?php echo Yii::app()->createUrl('companyKpi/info', array('type'=>$type));?>',
            function(d) {
                jQuery('#frm').html(d);
            }
        );

        jQuery('#copy').click(function(){
            jQuery('#mould_container').toggle();
        });

        jQuery('#filter').click(function(){
            if (mould_list.length == 0) {
                alert('本月还没有设置城市');
                return false;
            }
            jQuery.each(mould_list, function(i, v){
                var id = v.split('_');
                if (id.length == 2) {
                    jQuery('[class="set_city"][value="'+id[0]+'"]').hide();
                }
            });
            jQuery(this).hide();
            jQuery('#show').show();
        });

        jQuery('#show').click(function(){
            jQuery('.set_city').show();
            jQuery(this).hide();
            jQuery('#filter').show();
        });

        jQuery('#mould_list').change(function(){
            var v = jQuery(this).val();
            var attr = v.split('_');
            var city_id = attr[0];
            var use_date = attr[1];
            jQuery.get(
                '<?php echo Yii::app()->createUrl('companyKpi/info');?>',
                {
                    'city_id' : city_id,
                    'use_date' : use_date
                },
                function(d) {
                    jQuery('#frm').html(d);
                }
            );
        });

        jQuery('#submit').click(function(){

            if (validator()==0) {
                alert('您还有数据没有填写');
                return false;
            }

            if (!checkData()) {
                alert('分类基础分相加应为100分');
                return false;
            }

            //var use_date = jQuery('[name="use_date"]').val();
            var city_list = new Array();
            jQuery('.set_city').each(function(){
                if (jQuery(this).attr('checked') == 'checked') {
                    city_list.push(jQuery(this).val());
                    jQuery('#frm').append('<input type="hidden" name="city_id[]" value="'+jQuery(this).val()+'">');
                }
            });

            if ( city_list.length <=0) {
                alert('请选择城市');
                return false;
            } else {
                /*
                if (parseInt(use_date)>0){
                    jQuery('#u_date').remove();
                    jQuery('#frm').append('<input type="hidden" id="u_date" name="use_date" value="'+use_date+'">');
                }
                */
            }

            var post_data = jQuery('#frm').serialize();

            var flag = true;

            jQuery('#submit').attr({"disabled" : "disabled"});

            jQuery.post(
                '<?php echo Yii::app()->createUrl('companyKpi/setting');?>',
                post_data,
                function(d) {
                    if(d.status) {
                        jQuery.each(d.data, function(i,v){
                            if (v) {
                                //jQuery('#'+i).find('.cnt').val('');
                            } else {
                                flag = false;
                            }
                        });
                        if (flag) {
                            alert('设置成功');
                        } else {
                            alert('设置失败请重新设置');
                        }
                    }
                    jQuery('#submit').removeAttr('disabled');
                },
                'json'
            );
        });

        function validator(){
            var flag = 1;
            jQuery('.cnt').each(function(){
                var val = jQuery(this).val();
                if (val == '' && jQuery(this).attr('disabled') != 'disabled') {
                    if (jQuery(this).attr('disabled') != 'disabled') {
                        jQuery(this).css('border', '1px solid red');
                    }
                    if (flag != 0){
                        flag = 0;
                    }
                }
            });
            return flag;
        }

        function checkData(){
            var v=0;
            jQuery('[func="basic_score"]').each(function(){
                if (jQuery(this).attr('disabled') != 'disabled') {
                    v = parseInt(v)+parseInt(jQuery(this).val());
                }
            });
            if (v==100) {
                return true;
            } else {
                return false;
            }
        }

        $("#checkall").click(function() {
            $("input[name='set_cityname']:checkbox").each(function() { //遍历所有的name为selectFlag的 checkbox
                $(this).attr("checked", true);
            });
        });

        $("#invert").click(function() {
            $("input[name='set_cityname']:checkbox").each(function() { //遍历所有的name为selectFlag的 checkbox
                $(this).attr("checked",!this.checked);
            });
        });

    });
</script>

