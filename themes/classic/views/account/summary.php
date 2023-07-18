<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-8-9
 * Time: 上午6:30
 */
$cs=Yii::app()->clientScript;
$cs->registerCssFile(SP_URL_IMG.'icon/style.css');
?>
<style>
    .fs1 {
        color: #555555;
        font-size: 32px;
        margin-bottom: 0.35em;
        margin-top: 0.25em;
        width: 100%;;
    }

    .shortcuts {
        color: #666666;
        display: block;
        font-weight: 400;
        margin-top: 0.75em;
    }
</style>
<h1>我的控制台</h1>

<div class="row_fluid">
    <div class="span6">
        <div id="operate"></div> <!--财务数据-->
        <div id="customer_consume"></div><!--客户消费-->
        <div id="vip_consume"></div><!-- VIP 消费-->
        <div id="invoice_apply"></div><!-- 发票申请-->
        <div id="driver_admin"></div><!--司机管理-->
        <div id="kpi"></div><!-- KPI-->
        <div id="driver_online"></div><!-- 司机在线-->
        <div id="new_customer"></div><!-- 新客户统计-->
        <div id="common"></div> <!--common-->
    </div>

    <div class="span6">
        <div id="quick"></div>
        <div id="order"></div>
        <div id="comments"></div>
        <div id="errorLog"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        <?php
        if(AdminActions::model()->havepermission("account", "SummaryOperate")){
            echo "operate();";
        }

        if(AdminActions::model()->havepermission("account", "SummaryDriverAdmin")){
            echo "driverAdmin();";
        }

        if(AdminActions::model()->havepermission("account", "SummaryCommon")){
            echo "common();";
        }



        if(AdminActions::model()->havepermission("account", "SummaryDriverOnline")){
            echo "driverOnline();";
        }

        if(AdminActions::model()->havepermission("account", "SummaryNewCustomer")){
            echo "newCustomer();";
        }

        if(AdminActions::model()->havepermission("account", "SummaryKpi")){
            echo "kpi();";
        }


        if(AdminActions::model()->havepermission("account", "SummaryQuick")){
            echo "quick();";
        }



        if(AdminActions::model()->havepermission("account", "SummaryOrder")){
            echo "order();";
        }

        if(AdminActions::model()->havepermission("account", "SummaryErrorLog")){
            echo "errorLog();";
        }

        ?>
    });

    function operate() {
        var city = $("#operate #city_id").val();
        var data = "";
        if(typeof(city) != "undefined"){
            data = {'city_id': city };
        }

        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryOperate');?>',
            'data': data,
            'type': 'get',
            'success': function (data) {
                $('#operate').html(data);
            },
            'cache': false
        });
    }



    function driverAdmin() {
        var city = $("#driver_admin #city_id").val();
        var data = "";
        if (typeof(city) != "undefined") {
            data = {'city_id': city };
        }
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryDriverAdmin');?>',
            'data': data,
            'type': 'get',
            'success': function (data) {
                $('#driver_admin').html(data);
            },
            'cache': false
        });
    }









    function driverOnline() {
        var city = $("#driver_online #city_id").val();
        var data = "";
        if(typeof(city) != "undefined"){
            data = {'city_id': city };
        }

        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryDriverOnline');?>',
            'data': data,
            'type': 'get',
            'success': function (data) {
                $('#driver_online').html(data);
            },
            'cache': false
        });
    }

    function newCustomer() {
        var city = $("#new_customer #city_id").val();
        var data = "";
        if(typeof(city) != "undefined"){
            data = {'city_id': city };
        }

        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryNewCustomer');?>',
            'data': data,
            'type': 'get',
            'success': function (data) {
                $('#new_customer').html(data);
            },
            'cache': false
        });
    }

    function common() {
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryCommon');?>',
            'type': 'get',
            'success': function (data) {
                $('#common').html(data);
            },
            'cache': false
        });
    }

    function kpi() {
        var city = $("#kpi #city_id").val();
        var data = "";
        if (typeof(city) != "undefined") {
            data = {'city_id':city};
        }
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryKpi');?>',
            'type': 'get',
            'data':data,
            'success': function (data) {
                $('#kpi').html(data);
            },
            'cache': false
        });
    }

    function order() {
        var city = $("#order #city_id").val();
        var data = "";
        if (typeof(city) != "undefined") {
            data = {'city_id':city};
        }
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryOrder');?>',
            'data': data,
            'type': 'get',
            'success': function (data) {
                $('#order').html(data);
            },
            'cache': false
        });
    }

    function errorLog() {
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryErrorLog');?>',
            'type': 'get',
            'success': function (data) {
                $('#errorLog').html(data);
            },
            'cache': false
        });

    }

    function quick() {
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryQuick');?>',
            'type': 'get',
            'success': function (data) {
                $('#quick').html(data);
            },
            'cache': false
        });

    }

</script>








