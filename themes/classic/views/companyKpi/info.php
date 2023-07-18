<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-28
 * Time: 下午4:15
 * To change this template use File | Settings | File Templates.
 */

?>

<style>
    td {
        vertical-align:middle;
        text-align: center;
    }

    .cnt {
        width : 85px;
    }
</style>

<div id="service_div" style="margin-top: 20px">
    <p><strong>服务品质</strong></p>
    <table class="table table-bordered">
        <thead>
        <tr>
            <td rowspan="2" style="vertical-align:middle;">品质分类</td>
            <td rowspan="2" style="vertical-align:middle;">分类基础分</td>
            <td colspan="3">评分标准</td>
            <td colspan="4">当月完成情况得分</td>
        </tr>
        <tr>
            <td>挑战值<small>(万分之)</small></td>
            <td>目标值<small>(万分之)</small></td>
            <td>合格值<small>(万分之)</small></td>
            <td>完成挑战值得分</td>
            <td>完成目标值得分</td>
            <td>完成合格值得分</td>
            <td>不合格得分</td>
        </tr>
        </thead>
        <tbody id="service_container">

        </tbody>
    </table>
</div>

<div id="operate_div">
    <p><strong>运营业绩</strong></p>
    <table class="table table-bordered">
        <thead>
        <tr>
            <td>运营业绩</td>
            <td>分类基础分</td>
            <td>评分标准（设定期望值）</td>
        </tr>
        </thead>
        <tbody id="operate_container">

        </tbody>
    </table>
</div>

<div id="business_div" style="display: none">
    <p><strong>市场推广</strong></p>
    <table class="table table-bordered">
        <thead>
        <tr>
            <td rowspan="2" style="vertical-align:middle;">推广分类</td>
            <td rowspan="2" style="vertical-align:middle;">分类基础分</td>
            <td colspan="3">评分标准</td>
            <td colspan="4">当月完成情况得分</td>
        </tr>
        <tr>
            <td>挑战值<small>(个)</small></td>
            <td>目标值<small>(个)</small></td>
            <td>合格值<small>(个)</small></td>
            <td>完成挑战值得分</td>
            <td>完成目标值得分</td>
            <td>完成合格值得分</td>
            <td>不合格得分</td>
        </tr>
        </thead>
        <tbody id="business_container">

        </tbody>
    </table>
</div>

<script type="text/javascript">

    var back_type = <?php echo intval($type); ?>; //后台类型，运营or市场

    var service_list = <?php echo json_encode($service_list); ?>;

    var operate_list = <?php echo json_encode($operate_list); ?>;

    var business_list = <?php echo json_encode($business_list);?>;

    var service_attr = <?php echo json_encode($service_attr); ?>;

    var operate_attr = <?php echo json_encode($operate_attr); ?>;

    var business_attr = <?php echo json_encode($business_attr);?>

    var data = <?php echo is_array($data) && count($data) ? json_encode($data) : ($type == 1 ? '{"service":{"1":{"type_id":1,"basic_score":"5.00","chanllenge":"2.00","goal":"6.00","standard":"15.00","c_score":"8.00","g_score":"5.00","s_score":"2.00","uns_score":"0.00","name":"安全类"},"2":{"type_id":2,"basic_score":"5.00","chanllenge":"1.50","goal":"4.00","standard":"8.00","c_score":"8.00","g_score":"5.00","s_score":"2.00","uns_score":"0.00","name":"服务类"},"3":{"type_id":3,"basic_score":"5.00","chanllenge":"1.50","goal":"4.00","standard":"8.00","c_score":"8.00","g_score":"5.00","s_score":"2.00","uns_score":"0.00","name":"纠纷类"},"4":{"type_id":4,"basic_score":"5.00","chanllenge":"2.00","goal":"6.00","standard":"15.00","c_score":"8.00","g_score":"5.00","s_score":"2.00","uns_score":"0.00","name":"标准类"},"5":{"type_id":5,"basic_score":"5.00","chanllenge":"0.10","goal":"0.50","standard":"2.00","c_score":"8.00","g_score":"5.00","s_score":"2.00","uns_score":"0.00","name":"其它类"}},"business":{"9":{"type_id":9,"basic_score":"40","chanllenge":"99.99","goal":"50.00","standard":"45.00","c_score":"80.00","g_score":"40.00","s_score":"20.00","uns_score":"0.00","name":"餐厅推广"},"10":{"name":"银行推广"},"11":{"name":"KTV推广"},"12":{"name":"4S店推广"}},"operate":{"6":{"type_id":6,"basic_score":"15","grade":"20","name":"入职司机数"},"7":{"type_id":7,"basic_score":"10","grade":"6","name":"峰值时段空闲数"},"8":{"name":"订单增长环比"},"13":{"type_id":13,"basic_score":"10","grade":"500","name":"订单数"}}}'
    :'{"service":{"1":{"type_id":1,"basic_score":"10","chanllenge":"2","goal":"3","standard":"8","c_score":"20","g_score":"10","s_score":"2","uns_score":"0","name":"安全类"},"2":{"type_id":2,"basic_score":"10","chanllenge":"1.5","goal":"3","standard":"8","c_score":"15","g_score":"10","s_score":"2","uns_score":"0","name":"服务类"},"3":{"type_id":3,"basic_score":"10","chanllenge":"1.5","goal":"3","standard":"8","c_score":"15","g_score":"10","s_score":"2","uns_score":"0","name":"纠纷类"},"4":{"type_id":4,"basic_score":"5","chanllenge":"2","goal":"3","standard":"8","c_score":"8","g_score":"5","s_score":"1","uns_score":"0","name":"标准类"},"5":{"type_id":5,"basic_score":"5","chanllenge":"0.1","goal":"0.5","standard":"2","c_score":"8","g_score":"5","s_score":"1","uns_score":"0","name":"其它类"}},"business":{"9":{"name":"餐厅推广"},"10":{"name":"银行推广"},"11":{"name":"KTV推广"},"12":{"name":"4S店推广"}},"operate":{"6":{"basic_score":"20","grade":"1000","name":"入职司机数"},"7":{"basic_score":"5","grade":"30","name":"峰值时段空闲数"},"8":{"basic_score":"35","grade":"10","name":"订单增长环比"},"13":{"basic_score":"35","grade":"36","name":"订单数"}}}'); ?>;


    jQuery(document).ready(function(){

        var service_html = initTable('service');

        var operate_html = initTable('operate');

        var business_html = initTable('business');

        jQuery('#service_container').html(service_html);

        jQuery('#operate_container').html(operate_html);

        if (back_type) {
        jQuery('#business_container').html(business_html);
        jQuery('#business_div').show();
        }

        function initTable(type) {
            var can_select = false;
            if (type == 'service') {
                var list =  service_list;
                var attr =  service_attr;
                var setting_data = data.service;
            } else if (type == 'operate'){
                var list = operate_list;
                var attr = operate_attr;
                var setting_data = data.operate;
            } else if (type == 'business') {
                var list = business_list;
                var attr = business_attr;
                var setting_data = data.business;
                can_select = true;
            }
            var html = '';
            jQuery.each(list, function(type_id, name){
                html += '<tr id="'+type_id+'">';
                if (can_select) {
                    html += '<td><input type="checkbox" func="active_row" m_chk >'+name+'</td>';
                } else {
                    html += '<td>'+name+'</td>';
                }
                jQuery.each(attr, function(i, filed){

                    html += '<td><input class="cnt" name="'+type+"["+type_id+"]["+filed+"]"+'" ';
                    if (typeof(setting_data) == 'object' && setting_data[type_id][filed]) {
                        html += 'value="'+setting_data[type_id][filed] +'" ';
                        if (can_select) {
                            html = html.replace(/m_chk/, 'checked="checked"');
                        }
                    } else {
                        if (can_select) {
                            html += 'disabled="disabled"'
                        }
                    }

                    if (filed == 'basic_score') {
                        html += 'func="basic_score" ';
                    }
                    html+=' /></td>';
                });
                html += '</tr>';
            });
            return html;
        }

        jQuery('[func="active_row"]').live('click', function(){
            if (jQuery(this).attr('checked') == 'checked') {
                jQuery(this).parent('td').siblings().find('input').removeAttr('disabled');
            } else {
                jQuery(this).parent('td').siblings().find('input').val('').attr('disabled', 'disabled');
            }
        });
    });

</script>

