<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-29
 * Time: 下午5:20
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = 'KPI设置信息查看';
?>

<div class="container">

    <h1>分公司KPI设置信息查看</h1>

    <div class="alert alert-info">
        绩效考核模板：<strong><?php echo $mould_name; ?></strong>
    </div>

    <div>
        <form id="info">
            努力加载中......
        </form>
    </div>

    <div>
        <a href="javascript:void(0)" class="btn btn-info" style="margin-right: 20px; <?php echo !$can_modify ? 'display:none': '';?>" id="update">修改</a>
        <a href="javascript:void(0)" class="btn btn-danger" style="margin-right: 20px; display: none;" id="submit">保存</a>
    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function(){

        jQuery.get(
            '<?php echo Yii::app()->createUrl('companyKpi/info', array('city_id'=>$city_id, 'use_date'=>$use_date, 'type'=>$type));?>',
            function(d){
                var o = jQuery(d);
                jQuery('#info').html('').append(o);
                setTimeout(function(){jQuery('.cnt').attr('readonly','readonly')},1000);
            }
        );

        jQuery('#update').click(function(){
            jQuery('.cnt').attr('readonly',false);
            jQuery(this).hide();
            jQuery('#submit').show();
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

            jQuery('#info').append('<input type="hidden" name="city_id[]" value="<?php echo $city_id;?>">');
            jQuery('#info').append('<input type="hidden" name="use_date" value="<?php echo $use_date;?>">');

            var post_data = jQuery('#info').serialize();

            jQuery.post(
                '<?php echo Yii::app()->createUrl('companyKpi/setting');?>',
                post_data,
                function(d) {
                    if(d.status) {
                        alert('修改成功');
                        jQuery('#submit').hide();
                        jQuery('#update').show();
                    } else {
                        alert('修改失败');
                    }
                },
                'json'
            )

        });

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
    });

    function validator(){
        var flag = 1;
        jQuery('.cnt').each(function(){
            var val = jQuery(this).val();
            if (val == '' && jQuery(this).attr('disabled') != 'disabled') {
                jQuery(this).css('border', '1px solid red');
                if (flag != 0){
                    flag = 0;
                }
            }
        });
        return flag;
    }
</script>

