<html>
<head>
    <title>账目收支展示</title>
</head>
</html>
<body>
<?php
    function buildCell($item){
        $checkStatus = $item["check_status"];
        if(3 == $checkStatus){
            $status = "相等";
        }elseif(1 == $checkStatus){
            $status = "数据库里不存在";
        }elseif(2 == $checkStatus){
            $status = "对账表格里面没有";
        }elseif(4 == $checkStatus){
            $status = "不相等";
        }
        echo "<td>".$item["order_id"]."</td>";
        echo "<td>".$item["income"]."</td>";
        echo "<td>".$status."</td>";
        echo "<td>".$item["db_count"]."</td>";
        echo "<td>".$item["fee"]."</td>";
        echo "<td>".$item["balance"]."</td>";
        echo "<td>".$item["user_id"]."</td>";
        $isDriver = $item["isDriver"];
        if(1 == $isDriver){
            $driverFlag = "是";
        }else{
            $driverFlag = "否";
        }
        echo "<td>".$driverFlag."</td>";
        $isTest = $item["isTest"];
        if(1 == $isTest){
            $testFlag = "是";
        }else{
            $testFlag = "否";
        }
        echo "<td>".$testFlag."</td>";
        echo "<td>".$item["bank_card"]."</td>";
        echo "<td>".$item["clearing_date"]."</td>";
        echo "<td>".$item["trade_date"]."</td>";
        echo "<td>".$item["trade_time"]."</td>";
        echo "<td>".$item["trace_id"]."</td>";


    }

?>

<table class="table table-striped">
    <tr>
        <td>订单号</td><td>收入</td><td>检查情况</td><td>数据库值</td><td>手续费</td><td>实际收入</td><td>用户id</td>
        <td>是否是司机</td><td>是否是测试</td>
        <td>银行卡号</td><td>清算日期</td><td>交易日期</td><td>交易时间</td><td>跟踪号</td>

    </tr>
    <?php
    foreach($model as $i){
        echo "<tr>";
        buildCell($i);
        echo "</tr>";
    }
    ?>
</table>
</body>