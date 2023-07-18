<h1>公司账目一览表</h1>

<?php
    $city_list = RCityList::model()->getOpenCityList();
    $city_list['0'] = '全部';
    $openCityIds = array_keys($city_list);
    ksort($city_list);
	$isGroupByCity 	= (1 == $searchType);
	if($isGroupByCity){
		$yearList	= array('2010','2011','2012','2013','2014','2015','2016','2017','2018');
		echo "年:";
		echo '<select name= "year" id = "year" >';
		foreach($yearList as $y){
			if($year == $y){
				echo "<option value=" . $y . " selected>" . $y . "</option>";
			}else{
				echo "<option value=" . $y . ">" . $y . "</option>";
			}
		}
		echo '</select>';
		$monthList	= array('01','02','03','04','05','06','07','08','09','10','11','12');
		echo "月:";
		echo '<select name= "month" id = "month" >';
		foreach($monthList as $m){
			if($month == $m){
				echo "<option value=" . $m . " selected>" . $m . "</option>";
			}else{
				echo "<option value=" . $m . ">" . $m . "</option>";
			}
		}
		echo '</select>';
		echo '<input type="submit" name="yt0" class="btn" id="search_btn_two" value="查询">';
		echo  '<input type="submit" name="yt0" class="btn" id="switch_date_btn" value="切换到月份视图">';
	}else{
		echo '城市：<select name="city_id" id="city_id">';
        foreach ($city_list as $key => $name) {
            if ($key == $city_id) {
                echo "<option value=" . $key . " selected>" . $name . "</option>";
            } else {
                echo "<option value=" . $key . ">" . $name . "</option>";
            }
        }

		echo '</select>';
		echo '<input type="submit" name="yt0" class="btn" id="search_btn" value="查询">';
		echo  '<input type="submit" name="yt0" class="btn" id="switch_city_btn" value="切换到城市视图">';
	}
?>


<table class="table table-bordered" style="width:100%">
    <tr class="info">
        <td rowspan="2">
			<?php
				if($isGroupByCity){
					echo '城市';
				}else{
					echo '月份';
				}
			?>
		</td>
        <td colspan="4" style="text-align:center;">公司总收入(元)</td>
        <td colspan="2" style="text-align:center;">司机收入(元)</td>
        <td colspan="4" style="text-align:center;">信息费来源(元)</td>
        <td colspan="2" style="text-align:center;">预收入(元)</td>
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

        <td>信息费结转</td>
        <td>信息费结余</td>
    </tr>

    <?php
    $num = 0;
    $per = 0.5;
	if(!empty($employ_account_list)){
		$balanceForward = DriverBalance::model()->getBalance($city_id);
		$user = Yii::app()->user->getID();
		foreach ($employ_account_list as $data) {
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
			$cityId = $data['city_id'];
			if($isGroupByCity){
				$hrefName	= $cityName = (in_array($cityId, $openCityIds)) ? $city_list[$cityId] : 'null';
				if(170 == $cityId){
					$hrefName = '铁岭';
				}
				$link		= CHtml::link($hrefName, array("account/accountHistoryTotel", "city_id" => $cityId), array("target"=>"_blank"));
			}else{
				$link		= CHtml::link($data['id'], array("account/accountDailyHistory", "id" => $data["settle_date"], "city_id" => $cityId),array("target"=>"_blank"));
			}
			echo '
		<tr class="' . $color . '">
			<td>合计</td>
			<td colspan = "4" style="text-align:center; ">' . number_format(-$company_count, 2) . '</td>
			<td rowspan = "2" style="text-align:center;">' . number_format($data["t0"], 2) . '</td>
			<td rowspan = "2" style="text-align:center;">' . number_format($count, 2) . '</td>
			<td colspan = "4" style="text-align:center;">' . number_format($fees, 2) . '</td>
			<td rowspan = "2">' . number_format($data['totle'], 2) . '</td>
			<td rowspan = "2">' . number_format($balanceForward, 2) . '</td>
		</tr>
		<tr class="' . $color . '">
			<td width="60">' . $link . '</td>
			<td style="text-align:right">' . number_format(-$data["t1"], 2) . '</td>
			<td style="text-align:right">' . number_format(-$data["t2"], 2) . '</td>
			<td style="text-align:right">' . number_format(-$data["t6"], 2) . '</td>
			<td>' . number_format(-$data["t4"], 2) . '</td>

			<td style="text-align:right">' . number_format($data["t5"], 2) . '</td>
			<td style="text-align:right">' . number_format($data["t3"], 2) . '</td>
			<td style="text-align:right">' . number_format($data["t9"], 2) . '</td>
			<td style="text-align:right">' . number_format($favorable_count, 2) . '</td>
		</tr>';
			$balanceForward = $balanceForward - $data['totle'];
		}
	}else{
		echo '<h3>没有找到任何数据</h3>';
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
	预收入(最近账单)
		信息费结余 = 上个月信息费结转 + 上个月信息费结余 + 当月信息费结转；
	预收入(之前月份)
		信息费结余 = 上个月信息费结余 + 上个月信息费结转；
</pre>
<script>
    jQuery(function ($) {
        $('#search_btn').click(function () {
            var city_id = $("#city_id").val();
            var url = '<?php echo Yii::app()->createUrl('/account/accountHistoryTotel');?>';
            location.href = url + '&city_id=' + city_id;
        });
    })
</script>

<script>
	jQuery(function ($) {
		$('#search_btn_two').click(function () {
			var year = $("#year").val();
			var month = $("#month").val();
			var url = '<?php echo Yii::app()->createUrl('/account/accountHistoryTotel');?>';
			location.href = url + '&searchType=1' + '&year=' + year + '&month=' + month ;
		});
	})
</script>

<script>
	jQuery(function ($) {
		$('#switch_city_btn').click(function () {
			var searchType = $("#searchType").val();
			var url = '<?php echo Yii::app()->createUrl('/account/accountHistoryTotel');?>';
			location.href = url + '&searchType=1' ;
		});
	})
</script>

<script>
	jQuery(function ($) {
		$('#switch_date_btn').click(function () {
			var searchType = $("#searchType").val();
			var url = '<?php echo Yii::app()->createUrl('/account/accountHistoryTotel');?>';
			location.href = url + '&searchType=0' ;
		});
	})
</script>