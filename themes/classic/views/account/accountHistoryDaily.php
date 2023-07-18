<?php
if ($id == 0) {
    $day = date('Y年m月');
    $date_id = 0;
} else {
    $day = date('Y年m月', $id);
    $date_id = date('Y-m', $id);
}?>
<h1><?php echo $day; ?>账目一览表</h1>
城市：
<select name="city_id" id="city_id">
    <?php
    $city_list = RCityList::model()->getOpenCityList();
    $city_list['0'] = '全部';
    ksort($city_list);

    foreach ($city_list as $key => $name) {
        if ($key == $city_id) {
            echo "<option value=" . $key . " selected>" . $name . "</option>";
        } else {
            echo "<option value=" . $key . ">" . $name . "</option>";
        }
    }
    ?>
</select>
<input type="submit" name="yt0" id="search_btn" value="查询">

<table class="table table-bordered" style="width:100%">

    <tr class="info">
        <td rowspan="2">天<br/>（零点到零点）</td>
        <td colspan="4" style="text-align:center;">公司收入(元)</td>
        <td colspan="2" style="text-align:center;">司机收入(元)</td>
        <td colspan="4" style="text-align:center;">信息费来源(元)</td>
    </tr>

    <tr class="info">
        <td>信息费</td>
        <td>发票扣税</td>
        <td>保险费</td>
        <td>罚金</td>

        <td>司机毛收入</td>
        <td>司机纯收入</td>

        <td>信息费充值</td>
        <td>vip转信息费</td>
        <td>司机发卡返现</td>
        <td>优惠券转信息费</td>
    </tr>

    <?php
    $num = 0;
    $per = 0.5;
    $user = Yii::app()->user->getID();
    foreach ($list_settle as $data) {
        if ($user == '张国蓉') {
            foreach ($data as $k => $v) {
                if ($k != 'settle_date' && $k != 'id') {
                    $data[$k] = $v * $per;
                }
            }
        }
        $driver_count = $data["t0"];
        $company_count = $data["t1"] + $data["t2"] + $data["t4"] + $data["t6"];
        $favorable_count = $data["t7"] + $data["t8"] + $data["t10"];
        $count = $data["t0"] + $company_count;
        $fees = $data["t5"] + $data["t3"] + $data["t9"] + $favorable_count;
        $color = $num % 2 == 0 ? "warning" : "success";
        $num++;
        echo '
	<tr class="' . $color . '">
		<td>合计</td>
		<td colspan = "4" style="text-align:center;">' . number_format(-$company_count, 2) . '</td>
		<td rowspan = "2" style="text-align:center;">' . number_format($data["t0"], 2) . '</td>
		<td rowspan = "2" style="text-align:center;">' . number_format($count, 2) . '</td>
		<td colspan = "4" style="text-align:center;">' . number_format($fees, 2) . '</td>
	</tr>
	<tr class="' . $color . '">
		<td>' . date("Y-m-d (D)", $data["id"]) . '</td>
		<td style="text-align:right">' . number_format(-$data["t1"], 2) . '</td>
		<td style="text-align:right">' . number_format(-$data["t2"], 2) . '</td>
		<td style="text-align:right">' . number_format(-$data["t6"], 2) . '</td>
		<td style="text-align:right">' . number_format(-$data["t4"], 2) . '</td>
		
		<td style="text-align:right">' . number_format($data["t5"], 2) . '</td>
		<td style="text-align:right">' . number_format($data["t3"], 2) . '</td>
		<td style="text-align:right">' . number_format($data["t9"], 2) . '</td>
		<td style="text-align:right">' . number_format($favorable_count, 2) . '</td>
	</tr>';
    }

    ?>
    </tbody>
</table>
<pre>
说明公式：
	公司总收入 = 信息费 + 发票扣税 + 保险费 + 罚金;
	信息费来源 = 信息费充值 + vip转信息费 + 司机发卡返现 + 优惠券转信息费;
	司机纯收入 = 司机毛收入 - 公司总收入;
	信息费结转 = 信息费来源 - 公司总收入;
</pre>
<script>
    jQuery(function ($) {
        $('#search_btn').click(function () {
            var city_id = $("#city_id").val();
            var url = '<?php echo Yii::app()->createUrl('/account/accountDailyHistory',array('id' => $date_id));?>';
            location.href = url + '&city_id=' + city_id;
        });
    })
</script>