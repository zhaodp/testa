<?php
	$this->pageTitle = '常见问题 - e代驾';
?>         	
<h3>常见问题</h3>
<p>
                <b>1.e代驾服务开通城市都哪些？</b><br/>
                <?php echo $city_open;?>
            </p>

            <p>
                <b>2.e代驾手机客户端都支持哪些手机系统？</b><br/>
                目前e代驾手机客户端支持：苹果和安卓操作系统
            </p>

            <p>
                <b>3.e代驾手机客户端在哪能下载？</b><br/>
                http://wap.edaijia.cn
            </p>

            <p>
                <b>4.e代驾如何收费？</b><br/>
                <?php

                $zhushi_arr = array(
                    'conventional'=>'1.不同时间段的代驾起步费用以实际出发时间为准，默认最短约定时间为客户 呼叫时间延后20分钟。<br/>
                2.按照车内里程总表计算公里数，代驾距离超过10公里后，每超过10公里加收20元，不足10公里按10公里计算。<br/>
                3.约定时间前到达客户指定位置，从约定时间开始，每满30分钟收20元等候费，不满30分钟不收费；约定时间之后到达客户指定位置，从司机到达时间后，每满30分钟收20元等候费，不满30分钟不收费。<br/>&nbsp;<br/>',
                    'wx_single'=>'1.不同时段的代驾起步费以实际出发时间为准。<br/>
                2.代驾距离超过5公里后，每5公里加收20元，不足5公里按5公里计算。<br/>
                3.等候时间每满30分钟收费20元，不满30分钟不收费。<br/>&nbsp;<br/>',
                    'hz_single'=>'1.不同时间段的代驾起步费用以实际出发时间为准，默认最短约定时间为客户  呼叫时间延后20分钟。<br/>
                2.按照车内里程总表计算公里数，代驾距离超过10公里后，每超过5公里加收20元，不足5公里按5公里计算。<br/>
                3.约定时间前到达客户指定位置，从约定时间开始，每满30分钟收20元等候费，不满30分钟不收费；约定时间之后到达客户指定位置，从司机到达时间后，每满30分钟收20元等候费，不满30分钟不收费。<br/>&nbsp;<br/>',
                    'cq_single'=>'1.按照车内里程总表计算公里数，代驾距离超过10公里后，每超过5公里加收20元，不足5公里按5公里计算。<br/>
                2.约定时间前到达客户指定位置，从约定时间开始，每满30分钟收20元等候费，不满30分钟不收费；约定时间之后到达客户指定位置，从司机到达时间后，每满30分钟收20元等候费，不满30分钟不收费。<br/>
                <br/>',
                );
                foreach($fee_arr['citys'] as $k => $v){
                    echo 'e代驾 ';
                    $city_str = implode('、',$v);
                    echo $city_str;
                    echo ' 收费标准<br/>';

//print_r($fee_arr);die;

                    //////---------
                    $fee = $fee_arr['fees'][$k];
                    //echo $k;echo '-------';
                    $str = '';
                    if (!empty($fee['minFee'])) {
                        if (!empty($fee['firstFee'])) {
                            $fee_first = $fee['minFeeHour'] . '—' . $fee['firstFeeHour']. ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                        } else {
                            $fee_first = '全天';
                        }
                        $str .=  $fee_first  . $fee['minFee'] . ' 元(包含'.$fee['minDistance'].'公里)<br>';
                    }

                    if (!empty($fee['firstFee'])) {
                        if (!empty($fee['secondFee'])) {
                            $fee_second = $fee['firstFeeHour'] . '—' . $fee['secondFeeHour']. ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                        } else {
                            $fee_second = $fee['firstFeeHour'] . '—' . $fee['minFeeHour']. ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                        }
                        $str .= $fee_second .$fee['firstFee'] .  ' 元(包含'.$fee['minDistance'].'公里)<br>';
                    }

                    if (!empty($fee['secondFee'])) {
                        if (!empty($fee['thirdFeeHour'])) {
                            $fee_second = $fee['secondFeeHour'] . '—' . $fee['thirdFeeHour']. ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                        } else {
                            $fee_second = $fee['secondFeeHour'] . '—' . $fee['minFeeHour']. ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                        }
                        $str .= $fee_second . $fee['secondFee'].  ' 元(包含'.$fee['minDistance'].'公里)<br>';
                    }

                    if (!empty($fee['thirdFeeHour'])) {
                        $fee_second = $fee['thirdFeeHour'] . '—' . $fee['minFeeHour']. ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';

                        $str .=  $fee_second . $fee['thirdFee'] .  ' 元(包含'.$fee['minDistance'].'公里)<br>';
                    }

                    $str .= '
        备注： <br/>
                ';
                    $str.=$zhushi_arr[$k];
                    echo $str;
                    //---------//
                    //
                }
                ?>
            </p>

            <p>
                <b>5.哪些情况不属于e代驾服务范畴？</b><br/>
                军车、无牌照车辆、肇事逃逸车辆、车内有违禁品、客户属于醉酒状态并无旁人陪伴
            </p>

            <p>
                <b>6.代驾司机在为其代驾过程中出现交通违规该如何处理？</b><br/>
                上午10：00—下午18：00点，拨打4006—91—3939电话，经客服核实确认，所有违规责任由代驾司机承担
            </p>

            <p>
                <b>7.发票如何开具？</b><br/>
                e代驾开具发票内容仅限：代驾服务费，公司以快递形式将发票邮寄给客户，代驾费用不足500元需客户自行支付快递费用，金额满500元由E代驾公司承担快递费用。
            </p>

            <p>
                <b>8.代驾师傅多久能到达客户指定地点？</b><br/>
                平均到达客户时间为：15分钟（除恶劣天气、较偏远地方），具体情况请与代驾师傅具体沟通。
            </p>

            <p>
                <b>9.客户取消代驾，是否可以收费用？</b><br/>
                判断标准：是否代驾师傅等待超过30分钟？若没有超过30分钟，不得收取费用，若等待超过30分钟，按照每半小时20元收取等候费
            </p>

            <p>
                <b>10.司机在何种条件下可索要打车费用？</b><br/>
                判断标准：是否客户有主动要求代驾司机打车到达出发地？ 若没有要求，不可向客户索要打车费，若要求代驾司机打车到出发地需支付打车费用，按照打车发票的金额由客户支付给代驾司机
            </p>

            <p>
                <b>11.如何核算起步时间为标准？</b><br/>
                以实际出发时间为准，核算客户起步时间段
            </p>
