<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-8-9
 * Time: 上午6:30
 */
?>

<div class="row_fluid">
    <div class="span6">
        <div id="operate"></div>
        <div id="driver"></div>
        <div id="coupon"></div>
    </div>

    <div class="span6">
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

        if(AdminActions::model()->havepermission("account", "SummaryDriver")){
            echo "driver();";
        }

        if(AdminActions::model()->havepermission("account", "SummaryCoupon")){
            echo "coupon();";
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

    function driver() {
        var city = $("#driver #city_id").val();
        var data = "";
        if (typeof(city) != "undefined") {
            data = {'city_id': city };
        }
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryDriver');?>',
            'data': data,
            'type': 'get',
            'success': function (data) {
                $('#driver').html(data);
            },
            'cache': false
        });
    }

    function coupon() {
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/account/SummaryCoupon');?>',
            'type': 'get',
            'success': function (data) {
                $('#coupon').html(data);
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

</script>

