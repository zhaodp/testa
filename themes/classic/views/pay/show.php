<html>
    <head>
        <title>账目收支展示</title>
    </head>
</html>
<body>
    <?php
    /**
     * Created by PhpStorm.
     * User: tuan
     * Date: 6/12/14
     * Time: 16:56
     */

    function buildCell($item){
        echo "<td>".$item["date"]."</td>";
        echo "<td>".$item["sumIn"]."</td>";
        echo "<td>".$item["customerIn"]."</td>";
        echo "<td>".$item["driverIn"]."</td>";
        echo "<td>".$item["sumTest"]."</td>";
        echo "<td>".$item["testDriverIn"]."</td>";
        echo "<td>".$item["testCustomerIn"]."</td>";

        echo "<td>".$item["sumFee"]."</td>";
        echo "<td>".$item["sumBalance"]."</td>";
        $tmp = "index.php?r=pay/detail&date=".$item["date"];
        if($item["sumIn"] == $item["sumDbCount"]){
            echo "<td >"
                ."<a  style=\"color: #52b02b;\" href = ".$tmp.">yes"."</a>"
                ."</td>";
        }else{
            echo "<td >"
                ."<a  style=\"color: #FF0000;\" href = ".$tmp.">no"."</a>"
                ."</td>";

        }
        echo "<td>".$item["sumDbCount"]."</td>";
    }

    //echo "welcome";
    ?>
<table class="table table-striped">
    <tr>
        <td>日期</td><td>充值总额</td><td>用户充值总额</td><td>司机充值总额</td>
        <td>测试充值额</td><td>测试司机充值额</td><td>测试用户充值额</td><td>手续费</td><td>实收金额</td>
        <td>是否相等</td><td>数据库值</td>
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