<?php

class Common
{


    /**
     * 判断是否线上环境
     */
    public static function isOnlineEnv(){
        $dir_name = (dirname(dirname(__FILE__))).'/config/';
        $test_lock = $dir_name.'test.lock';
        $dev_lock = $dir_name.'dev.lock';
        if(file_exists($test_lock) || file_exists($dev_lock)){
            return false;
        }
        return true;
    }

    /**
     * 获取环境
     * 20160809 zhangtongkai
     * return dev|test|online
     */
    public static function getEnvironment(){
        $env = "online";
        $dir_name = (dirname(dirname(__FILE__))).'/config/';
        $test_lock = $dir_name.'test.lock';
        $dev_lock = $dir_name.'dev.lock';
        if(file_exists($dev_lock)){
            $env = "dev";
        }else if(file_exists($test_lock)){
            $env = "test";
        }else{
            $env = "online";
        }
        return $env;
    }
    
    /**
     * 旧版本，没有日间业务的 城市收费配置
     * @auther duke
     */
    public static function fee($id = 'conventional',$show_all = false)
    {
        $fee = array();
        $fee['conventional'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'07:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'22:59',
                    'price'=>59,
                ),
                array(
                    'start_time'=>'23:00',
                    'end_time'=>'23:59',
                    'price'=>79,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>99,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 10, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['hz_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'07:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'06:59',
                    'price'=>59,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['wx_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'07:00',
                    'end_time'=>'20:59',
                    'price'=>19,
                ),
                array(
                    'start_time'=>'21:00',
                    'end_time'=>'06:59',
                    'price'=>29,
                )
            ),

            'distince' => 5, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['cq_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'07:00',
                    'end_time'=>'06:59',
                    'price'=>39,
                )
            ),

            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        if($show_all) return $fee;
        else
            return $fee[$id];
    }





    /**
     * 城市收费配置
     * @auther mengtianxue
     */

    public static function feeback($id = 'conventional',$show_all = false)
    {
        $fee = array();
        $fee['conventional'] = array(
            'minFee' => 39, //基础价格（单位：元）
            'firstFee' => 59, //第一次变价后的价格（单位：元）
            'secondFee' => 79, //第二次变价后的价格（单位：元）
            'thirdFee' => 99, //第三次变价后的价格（单位：元）
            'minDistance' => 10, //基础距离（单位：公里）
            'feeStep' => 20, //变价步长（单位：元）
            'distanceStep' => 10, //距离计价步长（单位：公里）
            'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'beforeWaitingFee' => 20, //代驾开始前的等候费用步长（单位：元）
            'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'beforeWaitingStep' => 30, //代驾开始前的等候时长步长（单位：元）
            'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'minFeeHour' => '07:00', //基价开始时间（单位：时，24小时制）
            'firstFeeHour' => '22:00', //第一次变价开始时间（单位：时，24小时制）
            'secondFeeHour' => '23:00', //第二次变价开始时间（单位：时，24小时制）
            'thirdFeeHour' => '00:00' //第三次变价开始时间（单位：时，24小时制）
        );

        $fee['hz_single'] = array(
            'minFee' => 39, //基础价格（单位：元）
            'firstFee' => 59, //第一次变价后的价格（单位：元）
            'secondFee' => null, //第二次变价后的价格（单位：元）
            'thirdFee' => null, //第三次变价后的价格（单位：元）
            'minDistance' => 10, //基础距离（单位：公里）
            'feeStep' => 20, //变价步长（单位：元）
            'distanceStep' => 5, //距离计价步长（单位：公里）
            'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'beforeWaitingFee' => 20, //代驾开始前的等候费用步长（单位：元）
            'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'beforeWaitingStep' => 30, //代驾开始前的等候时长步长（单位：元）
            'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'minFeeHour' => '07:00', //基价开始时间（单位：时，24小时制）
            'firstFeeHour' => '22:00', //第一次变价开始时间（单位：时，24小时制）
            'secondFeeHour' => '07:00', //第二次变价开始时间（单位：时，24小时制）
            'thirdFeeHour' => null
        );


        $fee['wx_single'] = array(
            'minFee' => 19, //基础价格（单位：元）
            'firstFee' => 29, //第一次变价后的价格（单位：元）
            'secondFee' => null, //第二次变价后的价格（单位：元）
            'thirdFee' => null, //第三次变价后的价格（单位：元）
            'minDistance' => 5, //基础距离（单位：公里）
            'feeStep' => 20, //变价步长（单位：元）
            'distanceStep' => 5, //距离计价步长（单位：公里）
            'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'beforeWaitingFee' => 20, //代驾开始前的等候费用步长（单位：元）
            'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'beforeWaitingStep' => 30, //代驾开始前的等候时长步长（单位：元）
            'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'minFeeHour' => '07:00', //基价开始时间（单位：时，24小时制）
            'firstFeeHour' => '21:00', //第一次变价开始时间（单位：时，24小时制）
            'secondFeeHour' => '07:00', //第二次变价开始时间（单位：时，24小时制）
            'thirdFeeHour' => null //第三次变价开始时间（单位：时，24小时制）
        );

        $fee['cq_single'] = array(
            'minFee' => 39, //基础价格（单位：元）
            'firstFee' => null, //第一次变价后的价格（单位：元）
            'secondFee' => null, //第二次变价后的价格（单位：元）
            'thirdFee' => null, //第三次变价后的价格（单位：元）
            'minDistance' => 10, //基础距离（单位：公里）
            'feeStep' => 20, //变价步长（单位：元）
            'distanceStep' => 5, //距离计价步长（单位：公里）
            'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'beforeWaitingFee' => 20, //代驾开始前的等候费用步长（单位：元）
            'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'beforeWaitingStep' => 30, //代驾开始前的等候时长步长（单位：元）
            'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'minFeeHour' => '00:00', //基价开始时间（单位：时，24小时制）
            'firstFeeHour' => null, //第一次变价开始时间（单位：时，24小时制）
            'secondFeeHour' => null, //第二次变价开始时间（单位：时，24小时制）
            'thirdFeeHour' => null //第三次变价开始时间（单位：时，24小时制）
        );

        if($show_all) return $fee;
        else
            return $fee[$id];

    }
     

    /**
     * 开通日间业务的 城市 夜间收费配置
     * @auther mengtianxue
     */
    public static function feeDayTime($id = 'conventional',$show_all = false)
    {
        $fee = array();
        $fee['conventional'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'18:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'22:59',
                    'price'=>59,
                ),
                array(
                    'start_time'=>'23:00',
                    'end_time'=>'23:59',
                    'price'=>79,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>99,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 10, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['hz_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'18:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'06:59',
                    'price'=>59,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['wx_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'18:00',
                    'end_time'=>'20:59',
                    'price'=>19,
                ),
                array(
                    'start_time'=>'21:00',
                    'end_time'=>'06:59',
                    'price'=>29,
                )
            ),

            'distince' => 5, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['cq_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'18:00',
                    'end_time'=>'06:59',
                    'price'=>39,
                )
            ),

            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        if($show_all) return $fee;
        else
            return $fee[$id];

    }


    /**
     * 官网价格配置
     * @auther aiguoxin
     */
    public static function feeDayTimeForSite($id = 'conventional',$show_all = false)
    {
        $fee = array();
        $fee['conventional'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'18:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'22:59',
                    'price'=>59,
                ),
                array(
                    'start_time'=>'23:00',
                    'end_time'=>'23:59',
                    'price'=>79,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>99,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 10, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['hz_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'18:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'06:59',
                    'price'=>59,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );
        $fee['zs_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'07:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'06:59',
                    'price'=>59,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['wx_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'07:00',
                    'end_time'=>'20:59',
                    'price'=>19,
                ),
                array(
                    'start_time'=>'21:00',
                    'end_time'=>'06:59',
                    'price'=>29,
                )
            ),

            'distince' => 5, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );
        $fee['sz_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'18:00',
                    'end_time'=>'20:59',
                    'price'=>19,
                ),
                array(
                    'start_time'=>'21:00',
                    'end_time'=>'06:59',
                    'price'=>29,
                )
            ),

            'distince' => 5, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['cq_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'18:00',
                    'end_time'=>'06:59',
                    'price'=>39,
                )
            ),

            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        if($show_all) return $fee;
        else
            return $fee[$id];

    }

    /**
     * 开通日间业务的 城市 夜间收费配置 19：00
     * @auther mengtianxue
     */
    public static function feeDayTimeOld($id = 'conventional',$show_all = false)
    {
        $fee = array();
        $fee['conventional'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'22:59',
                    'price'=>59,
                ),
                array(
                    'start_time'=>'23:00',
                    'end_time'=>'23:59',
                    'price'=>79,
                ),
                array(
                    'start_time'=>'00:00',
                    'end_time'=>'06:59',
                    'price'=>99,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 10, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['hz_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'21:59',
                    'price'=>39,
                ),
                array(
                    'start_time'=>'22:00',
                    'end_time'=>'06:59',
                    'price'=>59,
                )
            ),
            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['wx_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'20:59',
                    'price'=>19,
                ),
                array(
                    'start_time'=>'21:00',
                    'end_time'=>'06:59',
                    'price'=>29,
                )
            ),

            'distince' => 5, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        $fee['cq_single'] = array(
            'part_price'=>array(
                array(
                    'start_time'=>'19:00',
                    'end_time'=>'06:59',
                    'price'=>39,
                )
            ),

            'distince' => 10, //基础距离（单位：公里）
            'next_price' => 20, //变价步长（单位：元）
            'next_distince' => 5, //距离计价步长（单位：公里）
            //'distanceFeeStep' => 20, //距离费用步长（单位：元）
            'before_waiting_price' => 20, //代驾开始前的等候费用步长（单位：元）
            //'onWaitingFee' => 20, //代驾中的等候费用步长（单位：元）
            'before_waiting_time' => 30, //代驾开始前的等候时长步长（单位：元）
            //'onWaitingStep' => 30, //代驾中的等候时长步长（单位：元）
            'desc'=>''
        );

        if($show_all) return $fee;
        else
            return $fee[$id];

    }


    public static function getClientRealIp()
    {
        $return = "Unknow";
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $tmp = ($_SERVER['HTTP_CLIENT_IP']);
            if (!empty($tmp)) $return = $tmp;
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $tmp = ($_SERVER['HTTP_X_FORWARDED_FOR']);
            if (!empty($tmp)) $return = $tmp;
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $tmp = ($_SERVER['REMOTE_ADDR']);
            if (!empty($tmp)) $return = $tmp;
        }
        return $return;
    }

    public static function phoneEncode($phone)
    {
        if (function_exists('edaijia_encode')) {
            return @edaijia_encode($phone);
        } else {
            return @base64_encode($phone);
        }
    }

    public static function phoneDecode($phone)
    {
        if (function_exists('edaijia_decode')) {
            return @edaijia_decode($phone);
        } else {
            return @base64_decode($phone);
        }
    }

    private static $hostipname = array(
        //prod
        '10.200.118.221' => 'api01',
        '10.200.118.222' => 'api02',
        '10.132.34.13' => 'api03',
        '10.132.63.217' => 'api04',
        '10.132.17.218' => 'cache01',
        '10.132.17.216' => 'cache02',
        '10.161.129.221' => 'cache03',
        '10.161.139.71' => 'job01',
        '10.161.139.70' => 'job02',

        '10.200.118.224' => 'web_01',
        '10.122.64.215' => 'web_02',

        //test
        '10.200.118.22' => 'web_04',
        '10.241.221.106' => 'test01',
        '10.160.45.37' => 'test02',


    );

    public static function getMyHostName()
    {

        $ip = gethostbyname('myhost');
        if ($ip == 'myhost') return '1.1.1.1';

        if (isset(self::$hostipname[$ip])) {
            return self::$hostipname[$ip];
        } else return $ip;
    }

    /**
     * 公司测试帐户
     * Enter description here ...
     * @var unknown_type
     */
    const TESTACCOUNT = "";

    public static function getTestDriverID()
    {
        $driver_arr = Driver::model()->getTestDriver();
        $driver = "'" . implode(',', $driver_arr) . "'";
        return $driver;
    }

    /**
     * 取得测试工号
     *
     * @author sunhongjing 2013-06-19
     * @param int $city_id
     * @return array
     */
    public static function getTestDriverIds($city_id = 0)
    {
        $test_driver_id_list = array(
            1 => array('BJ0001', 'BJ9000', 'BJ9001', 'BJ9002', 'BJ9003', 'BJ9004', 'BJ9005', 'BJ9006', 'BJ9010', 'BJ9012', 'BJ9013', 'BJ9016', 'BJ9017', 'BJ9018', 'BJ9020', 'BJ9021', 'BJ9022', 'BJ9023', 'BJ9011', 'BJ1161'),
            2 => array('CD0133', 'CD9001', 'CD9002'),
            4 => array('HZ0000',),
            3 => array('SH8888'),
            6 => array('SZ9999',),
            7 => array('CQ0001', 'CQ9001'),
        );

        if ($city_id > 0) {
            return empty($test_driver_id_list[$city_id]) ? array() : $test_driver_id_list[$city_id];
        } else {
            $ret = array();
            foreach ($test_driver_id_list as $value) {
                $ret = array_merge($ret, $value);
            }
            return $ret;
        }
    }

    /**
     * 取得智能计费测试工号
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-28
     * @param int $city_id
     * @return array $ret
     */
    public static function getAutoTestDriverIds()
    {
        $test_driver_id_list = array( //北京
//            'BJ0001', 'BJ9000', 'BJ9001', 'BJ9002', 'BJ9003', 'BJ9004', 'BJ9005', 'BJ9006', 'BJ9010', 'BJ9012', 'BJ9013',
//            'BJ9016', 'BJ9017', 'BJ9018' , 'BJ9020', 'BJ9021', 'BJ9011', 'BJ1161',
            //'BJ2120' , 'BJ0731' , 'BJ0777' , 'BJ0217' , 'BJ0300' , 'BJ0760' , 'BJ0837' , 'BJ0980' , 'BJ0796' , 'BJ0252' ,

            //上海
//            'SH1163', 'SH0037', 'SH0761', 'SH0730', 'SH0870', 'SH0796', 'SH0333', 'SH0052', 'SH0012', 'SH1012', 'SH8888',

            //广州
            //'GZ0620' , 'GZ0102' , 'GZ0023' , 'GZ0060' , 'GZ0368' , 'GZ0033' , 'GZ0529' , 'GZ0179' , 'GZ0821' , 'GZ1061' ,

        );
        return $test_driver_id_list;
    }

    /**
     * 取得智能计费测试工号(new)
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-28
     * @param int $city_id
     * @return array $ret
     */
    public static function getCallOrderAutoTestDriverIds()
    {
        $test_driver_id_list = array(
            //北京
            'BJ0001', 'BJ9000', 'BJ9001', 'BJ9002', 'BJ9003', 'BJ9004', 'BJ9005', 'BJ9006', 'BJ9010', 'BJ9012', 'BJ9013',
            'BJ9016', 'BJ9017', 'BJ9018', 'BJ9020', 'BJ9021', 'BJ9011', 'BJ1161', 'BJ9023',
            'BJ0001', 'BJ0217', 'BJ0300', 'BJ0760', 'BJ0837', 'BJ0378', 'BJ0796', 'BJ0252', 'BJ2120', 'BJ0731', 'BJ0777', 'BJ0162', 'BJ0288',
            //'BJ2120' , 'BJ0731' , 'BJ0777' , 'BJ0217' , 'BJ0300' , 'BJ0760' , 'BJ0837' , 'BJ0980' , 'BJ0796' , 'BJ0252' ,
            //上海
            'SH1163', 'SH0037', 'SH0761', 'SH0730', 'SH0870', 'SH0796', 'SH0333', 'SH0052', 'SH0012', 'SH1012', 'SH8888',
            //广州
            //'GZ0620' , 'GZ0102' , 'GZ0023' , 'GZ0060' , 'GZ0368' , 'GZ0033' , 'GZ0529' , 'GZ0179' , 'GZ0821' , 'GZ1061' ,

        );
        return $test_driver_id_list;
    }

    /**
     * 自动派单加测试电话号
     * @return array
     * @author AndyCong<congming@edaijia.cn
     */
    public static function getTestPhones()
    {
        return array('18911883373', '13611126764', '18701552183', '13811121479', '18618140153');
    }

    /**
     * 返回黑车师傅的号码
     * @author sunhongjing
     */
    public static function getBlackCarPhones()
    {
        return array('13522452154', '13488888291', '18810559219', '15510028853', '15510028853');
    }

    /**
     * 小于200屏蔽，每次扣500，发短信通知，收信息费城市id
     * @return array
     * author mengtianxue
     */
    public static function getDriverCityLt200()
    {
        return array(1, 2, 3, 4, 5, 6, 7, 8, 10, 11, 14, 15, 18, 20);//adjust by 2014-02-28
    }

    /**
     * 小于100屏蔽，每次扣200，发短信通知，收信息费城市id
     * @return array
     * author mengtianxue
     * 2014-03-28   上线十一个城市   mengtianxue   51, 50, 49, 37, 46, 44, 53, 48, 71, 61, 56
     */
    public static function getDriverCityLt100()
    {
        return array(9, 12, 16, 19, 21, 22, 23,24, 25, 27, 29, 30, 31,33, 51, 50, 49, 37, 46, 44, 53, 48, 71, 61, 56, 41, 43,76, 65, 47, 75, 73, 74,28,103,106,79,40,68,84,85,90,114,134,151,152,26,77,35,36,95,96,98,101,104,107,113,136,138,160,13,83,128,132,133,159,167,17,38,87,120,121,124,130,171,141,69,140,175,115,177,97);
    }

    /**
     * 费用19元的城市，新课邀请码返现时候用
     * @return array
     * author mengtianxue
     * 2014-03-28   上线十一个城市   mengtianxue  51, 50, 49, 37, 46, 44, 53, 48, 71, 61
     */
    public static function getCityFeeEq19()
    {
        return array(30, 16, 51, 50, 49, 37, 46, 44, 53, 48, 71, 61);
    }



    /**
     *
     * 测试账号和返程车不收信息费
     * @author sunhongjing 2013-06-23
     * @modify duke 2014-09-16
     * @param object $driver 司机基本信息
     * @param int $type 1,信息费折扣，返回0-1之间的数 ，2，保险费是否征收，返回 1收 或 0不收 ，两种类型返回值不一样。
     * @return int
     */
    public static function driver_fee_discount($driver_id, $type = '1')
    {
        $discount = 1; //没有折扣
        $driver_active_date = '';



        $driver = Driver::model()->find('user=:user', array(':user' => $driver_id));

        if($driver->is_test == 1) return $discount;  //测试工号 面信息费

        if (empty($driver) || empty($driver->city_id) || empty($driver->created)) {
            return $discount;
        }


        //类型转换
        if (2 == $type) {
            $discount = (int)$discount;
        }

        return $discount;
    }

    /**
     * 新司机签约，不用添加
     * @param $driver 如果是数组，必须包括 driver_id、city_id 和 created，否则传driver_id就新
     * @param string $type
     * @return int
     * author mengtianxue
     */
    public static function driver_discount($driver, $type = '1')
    {
        $discount = 1; //没有折扣
        $driver_active_date = '';
        if (!is_array($driver)) {
            $driver_id = $driver;
            $driver = Driver::model()->getDriver($driver_id);
        }

        if (empty($driver) || (isset($driver['city_id']) && empty($driver['city_id'])) || (isset($driver['created']) && empty($driver['created']))) {
            return $discount;
        }

        //南京12月前不收信息费
        if ($driver['city_id'] == 8) {
            if (strtotime('2014-12-31 23:59:59') > time()) {
                $discount = 0;
            }
        }

        //类型转换
        if (2 == $type) {
            $discount = (int)$discount;
        }

        return $discount;
    }




    /**
     * 根据前缀获取城市id
     * Enter description here ...
     * @param unknown_type $city_prefix
     */
    public static function getCity($city_prefix)
    {
        $city_id = 0;

        if (!empty($city_prefix)) {
            $city_id = Dict::code('city_prefix', $city_prefix);
        }
        return $city_id ? $city_id : 0;

    }

    /**
     * 根据城市id获取城市前缀
     * Enter description here ...
     * @param unknown_type $city_id
     */
    public static function getCityCode($city_id)
    {
        $city_prefix = 'BJ';
        if (!empty($city_id)) {
            $city_prefix = Dict::item('city_prefix', $city_id);
        }
        return $city_prefix ? $city_prefix : 'BJ';
    }

    /**
     * 获取开通城市
     * @auhtor bidong
     * @return array()
     */
    public static function getOpenCity()
    {
        $all = array('0' => '全部');
        $openCtiy = RCityList::model()->getOpenCityList();
        return $all + $openCtiy;
    }


    /**
     * 返回未开通城市列表
     * @author duke
     * @return array
     */

    public static function getUnopenCity(){
        $openCtiy = RCityList::model()->getOpenCityList();
        $allCity = Dict::items('city');
        $a = array_diff_key ($allCity,$openCtiy);
        return $a;
    }


    /**
     * 计算器计算规则
     * Enter description here ...
     * @param unknown_type $city
     * @param unknown_type $km
     * @param unknown_type $booking_time
     * @param unknown_type $wait_time
     * 51, 50, 49, 37, 46, 44, 53, 48, 71, 61
     */
    public static function calculator($city, $km, $booking_time, $wait_time)
    {
        switch ($city) {
            case 1:
            case 3:
            case 5:
            case 6:
                return self::conventional($km, $booking_time, $wait_time);
                break;
//            case 2:
//                return self::cd_single($km, $booking_time, $wait_time);
//                break;
            case 7:
                return self::cq_single($km, $booking_time, $wait_time);
                break;
            case 30:
            case 16:
            case 31:
            case 51:
            case 50:
            case 49:
            case 37:
            case 46:
            case 44:
            case 53:
            case 48:
            case 71:
            case 61:
            case 47:
            case 65:
            case 73:
            case 74:
            case 75:
	    case 40:
	    case 68:
	    case 84:
	    case 85:
	    case 90:
	    case 114:
	    case 134:
	    case 151:
	    case 152: 	   
	    //2014-06-24
	    case 95:
	    case 96:
	    case 98:
	    case 113:
	    case 136:
	    case 138:
	    case 160:
	   //2014-07-17
	   case 13:
	   case 128:
	   case 132:
	   case 133:
	   case 159:
	   case 167: 
	   //2014-08-26
	   case 87:
	   case 120:
	   case 121:
	   case 124:
	   case 130:
	   case 171:
        //2014-09-24
        case 141:
        case 69:
        case 140:
        case 175:
        case 115:
        case 177:
        case 97:
                return self::wx_single($km, $booking_time, $wait_time);
                break;
            default:
                return self::hz_single($km, $booking_time, $wait_time);
                break;
        }
    }


    /**
     * 分四个时间段收费 （上海、广州、深圳、北京）
     * @param $km
     * @param $booking_time
     * @param int $wait_time
     * @return int
     * author mengtianxue
     */
    public static function conventional($km, $booking_time, $wait_time = 0)
    {
        //$base  基数  时间不同基数也不一样
        $base = $redundant = 0;
        if ($km > 0) {
            $time = date('H', $booking_time);
            if ($time < 7) {
                $base = 99;
            } elseif ($time < 22) {
                $base = 39;
            } elseif ($time == 22) {
                $base = 59;
            } else {
                $base = 79;
            }
            //多余出来的
            $redundant = ceil(($km - 10) / 10) * 20 + floor($wait_time / 30) * 20;
        } else {
            //多余出来的
            $redundant = floor($wait_time / 30) * 20;
        }
        return $base + $redundant;
    }

    /**
     * 分两个时间段收费 （南京、西安、郑州、武汉、天津、济南、杭州）
     * 7--22 点收取  39
     * 其他时间      59
     * 没五公里加收20
     * @param $km
     * @param $booking_time
     * @param $wait_time
     * @return int
     * author mengtianxue
     */
    public static function hz_single($km, $booking_time, $wait_time)
    {
        //$base  基数  时间不同基数也不一样
        $excess = $base = 0;
        $time = date('H', $booking_time);
        if (7 <= $time && $time < 22) {
            $base = 39;
        } else {
            $base = 59;
        }

        //多余出来的
        if ($km > 10) {
            $excess = ceil(($km - 10) / 5) * 20;
        }

        $redundant = $excess + floor($wait_time / 30) * 20;
        return $base + $redundant;
    }

    /**
     * 固定收费 （成都）
     * 每一单收取10块钱信息费
     * 每加5公里加收20
     * @param $km
     * @param $booking_time
     * @param $wait_time
     * @return int
     * author mengtianxue
     */
    public static function cd_single($km, $booking_time, $wait_time)
    {
        //$base  基数  时间不同基数也不一样
        $excess = 0;
        $base = 0;
        if ($km > 0) {
            $base = 39;
        }
        //多余出来的
        if ($km > 5) {
            $excess = ceil(($km - 5) / 5) * 20;
        }
        $redundant = $excess + floor($wait_time / 30) * 20;
        return $base + $redundant;
    }

    /**
     * 固定收费 （无锡）
     * 每一单收取10块钱信息费
     * 每加5公里加收20
     * @param $km
     * @param $booking_time
     * @param $wait_time
     * @return int
     * author mengtianxue
     */
    public static function wx_single($km, $booking_time, $wait_time)
    {
        //$base  基数  时间不同基数也不一样
        $excess = 0;
        $time = date('H', $booking_time);
        if (7 <= $time && $time < 21) {
            $base = 19;
        } else {
            $base = 29;
        }

        //多余出来的
        if ($km > 5) {
            $excess = ceil(($km - 5) / 5) * 20;
        }
        $redundant = $excess + floor($wait_time / 30) * 20;
        return $base + $redundant;
    }

    /**
     * 固定收费 （重庆）
     * 每一单收取10块钱信息费
     * 每加5公里加收20
     * @param $km
     * @param $booking_time
     * @param $wait_time
     * @return int
     * author mengtianxue
     */
    public static function cq_single($km, $booking_time, $wait_time)
    {
        //$base  基数  时间不同基数也不一样
        $excess = 0;
        $base = 0;
        if ($km > 0) {
            $base = 39;
        }

        //多余出来的
        if ($km > 10) {
            $excess = ceil(($km - 10) / 5) * 20;
        }
        $redundant = $excess + floor($wait_time / 30) * 20;
        return $base + $redundant;
    }

    /**
     * 计算订单的信息费
     * （北京、南京、西安、郑州、武汉、天津、济南） * 20%
     * @param $order
     * @return float|int
     * author mengtianxue
     * 51, 50, 49, 37, 46, 44, 53, 48, 71, 61
     */
    public static function cast($order)
    {
        //查看优惠信息
        $driver_id = $order['driver_id'];
        $driver_fee_discount = Common::driver_fee_discount($driver_id);

        switch (self::getCityCode($order['city_id'])) {
            //20%
            case 'BJ':
            case 'NJ':
            case 'XA':
            case 'ZZ':
            case 'WH':
            case 'TJ':
            case 'JN':
            case 'CD':
            case 'CS':
            case 'NC':
            case 'XN':
            $cast = self::_castBJ($order);
            break;
            //5、10、15、20
            case 'SH':
            case 'GZ':
            case 'SZ':
            $cast = self::_castLevelFour($order);
            break;
            case 'CQ': //39元以下2元 大于39元5块钱
            $cast = self::_castCQ($order);
            break;
            case  'HZ': //5块10块
            $cast = self::_castLevel($order);
            break;
            case  'WX': //5块10块
            case  'SU':
            case  'CZ':
            case  'JX':
            case  'SX':
            case  'JH':
            case  'NT':
            case  'ZJ':
            case  'YZ':
            case  'HU':
            case  'XZ':
            case  'DT':
            case  'LY':
            case  'TZ':
            case  'TS':
            case  'LG':
            case  'LS':
            case  'YH':
	    case  'WI':
	    case  'YI': 
	    case  'WF':
	    case  'JG':
	    case  'DZ':
	    case  'SQ':
	    case  'XG':
	    case  'MY':
	    case  'DA':
	    case  'RZ':  
 	    case  'LW':
 	    case  'ZN':   
 	    case  'HI':   
 	    case  'HS':    
 	    case  'JO':   
 	    case  'PX':
	    case  'WZ':  
 	    case  'YE':   
 	    case  'WN': 
 	    case  'HH':   
 	    case  'XU':    
 	    case  'JW': 
	    case 'LI':
	    case 'JZ':
	    case 'XC':
	    case 'CH':
	    case 'BI':
	    case 'SA':
	    case 'HJ':
	    case 'YY':
	    case 'CE':
	    case 'CN':
	    case 'HD':
	    case 'DH':
	    case 'YW':

                $cast = self::_castWX($order);
                break;
            default:
                $cast = self::_castBJ($order);
                break;
        }
        return $cast * $driver_fee_discount;
    }




                /**
     * 扣除保险费以后的信息费 * 20%
     * @param $order
     * @return float|int
     * author mengtianxue
     */
    public static function _castBJ($order)
    {
        $income = $order['income'];
        $cast = 0;
        if ($income > 40) {
            //去除vip扣除税点的逻辑  九月五号扣司机税点  --mengtianxue
//            $cast = floor(($income - 2) * 0.2);
            $cast = ($income - 2) * 0.2;
//            if (empty($order['vipcard'])) {
//                $cast = floor(($income - 2) * 0.2);
//            } else {
//                $cast = floor(($income - 2 - $income * 0.06) * 0.2);
//            }
        }
        return $cast;
    }

    /**
     * 收入10%信息费
     * @param $order
     * @return float|int
     * @auther mengtianxue
     */
    public static function _castWX($order)
    {
        $income = $order['income'];
        $cast = 0;
        if ($income > 19) {
//            $cast = floor(($income - 2) * 0.1);
            $cast = ($income - 2) * 0.1;
        }
        return $cast;
    }


    /**
     * 重庆价格调整
     * @param $order
     * @return float|int
     * author mengtianxue
     */
    public static function _castCQ($order)
    {
        $income = $order['income'];
        $cast = 2;
        if ($income > 39) {
            $cast = 5;
        }
        return $cast;
    }

    /**
     * 固定信息费   (7 重庆)   -- 过期重新换价格调整  2013-09-16
     * @return int
     * author mengtianxue
     */
    public static function _castFixed()
    {
        $cast = 10;
        return $cast;
    }

    /**
     * 阶梯收费  5、10 两个阶段（4杭州）
     * @param $order
     * @return int
     * author mengtianxue
     */
    public static function _castLevel($order)
    {
        $bookingTime = $order['call_time'];
        $hour = date('H', $bookingTime);
        $minutes = date('i', $bookingTime);
        //换算为分钟后计算
        $min_sum = $hour * 60 + $minutes;

        if ($min_sum >= 60 * 7 and $min_sum < 60 * 22) {
            $cast = 5;
        } else {
            $cast = 10;
        }
        return $cast;
    }

    /**
     * 阶梯收费   5、10、15、20 四个阶段
     * @param $order
     * @return int
     * author mengtianxue
     */
    public static function _castLevelFour($order)
    {
        $booking_time = $order['call_time'];
        $hours = date('H', $booking_time);
        if ($hours < 7) {
            $case = 20;
        } elseif ($hours < 22) {
            $case = 5;
        } elseif ($hours == 22) {
            $case = 10;
        } else {
            $case = 15;
        }
        return $case;
    }


    /**
     * 根据城市ID得到城市等级
     * @author libaiyang 2013-05-09
     * @param int $city_id
     */
    public static function getCityLevel($city_id)
    {
        $cityLevel = 0;
        switch ($city_id) {
            case 1:
                $cityLevel = 1;
                break;
            case 2:
                $cityLevel = 3;
                break;
            case 3:
                $cityLevel = 2;
                break;
            default:
                $cityLevel = 3;
                break;
        }
        return $cityLevel;
    }

    /**
     * @author libaiyang 2013-05-08
     * 计算司机评分
     * @param unknown_type $user
     * @param unknown_type $newLevel
     * @param unknown_type $reportCount
     */

    public static function _getDriverLevel($old_level, $serviceCount, $star_one, $star_two, $all_comments, $city_id)
    {
        //一个一星=5个5星
        $one = 5 * 5;
        //一个两星=3个5星
        $two = 3 * 5;
        $level = 0;

        //城市等级
        $cityLevel = self::getCityLevel($city_id);

        if ($all_comments > 0) {
            $level = number_format(($old_level - ($star_one * $one) - ($star_two * $two)) / $all_comments, 1);
        }
        if ($level <= 0) {
            $level = 0;
        }

        switch ($cityLevel) {
            case 1:
                if ($serviceCount > 0 && $serviceCount <= 10) {
                    if ($level > 1) {
                        $level = 1;
                    }
                } else if ($serviceCount > 10 && $serviceCount <= 20) {
                    if ($level > 2) {
                        $level = 2;
                    }
                } else if ($serviceCount > 20 && $serviceCount <= 80) {
                    if ($level > 3) {
                        $level = 3;
                    }
                } else if ($serviceCount > 80 && $serviceCount <= 200) {
                    if ($level > 4) {
                        $level = 4;
                    }
                }
                break;

            case 2:
                if ($serviceCount > 0 && $serviceCount <= 10) {
                    if ($level > 1) {
                        $level = 1;
                    }
                } else if ($serviceCount > 10 && $serviceCount <= 20) {
                    if ($level > 2) {
                        $level = 2;
                    }
                } else if ($serviceCount > 20 && $serviceCount <= 80) {
                    if ($level > 3) {
                        $level = 3;
                    }
                } else if ($serviceCount > 80 && $serviceCount <= 150) {
                    if ($level > 4) {
                        $level = 4;
                    }
                }
                break;

            default:
                if ($serviceCount > 0 && $serviceCount <= 10) {
                    if ($level > 1) {
                        $level = 1;
                    }
                } else if ($serviceCount > 10 && $serviceCount <= 20) {
                    if ($level > 2) {
                        $level = 2;
                    }
                } else if ($serviceCount > 20 && $serviceCount <= 40) {
                    if ($level > 3) {
                        $level = 3;
                    }
                } else if ($serviceCount > 40 && $serviceCount <= 100) {
                    if ($level > 4) {
                        $level = 4;
                    }
                }
                break;
        }

        return $level;
    }

    public static function formatMoney($number, $fractional = false)
    {
        return money_format("%i", $number);
    }




    /**
     * 通用价格
     * @param $city
     * @param $booking_time
     * @return int
     */
    public static function conventional_price($city, $booking_time)
    {
        $time = date('H', $booking_time);
        if ($time < 7) {
            $base = 99;
        } elseif ($time < 22) {
            $base = 39;
        } elseif ($time == 22) {
            $base = 59;
        } else {
            $base = 79;
        }
        return $base;
    }

    /**
     * 杭州收费价格
     * @param $city
     * @param $booking_time
     * @return int
     */
    public static function hz_single_price($city, $booking_time)
    {
        $time = date('H', $booking_time);
        if (7 < $time && $time < 22) {
            $base = 39;
        } else {
            $base = 59;
        }
        return $base;
    }

    /**
     * 重庆收费价格
     * @param $city
     * @param $booking_time
     * @return int
     */
    public static function cq_single_price($city, $booking_time)
    {
        return 39;
    }

    /**
     * 对二维数组按照指定的键值进行排序，也可以指定升序或降序排序法（默认为降序）
     * @author zhanglimin 2013-06-20
     * @param $arr
     * @param $keys 排序键值
     * @param string $type
     * @param bool $dispaly
     * @return array
     */
    public static function array_sort($arr, $keys = 'weight', $type = 'desc', $dispaly = false)
    {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        foreach ($keysvalue as $k => $v) {
            if (!$dispaly) {
                unset($arr[$k][$keys]); //将$keys属性移除
            }
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

    /**
     * 新老投诉、销单转换
     *
     * @author sunhongjing 2013-11-09
     * @param unknown_type $type_id
     * @param unknown_type $is_ts
     */
    public static function convertCancelType($type_id = 0, $is_ts = 0)
    {
        $ret = 0;
        $rel_qx = array('1' => '25', '2' => '11', '3' => '14', '4' => '14', '5' => '99', '6' => '12', '7' => '15', '8' => '15');
        $rel_ts = array('1' => '23', '2' => '24', '3' => '99', '4' => '25', '5' => '31', '6' => '32');

        $type_id = empty($type_id) ? '0' : $type_id;

        //如果是投诉类型，则用rel_ts，否则用rel_qx对应，如果都对应不上，就返回99
        if ($is_ts) {
            $ret = isset($rel_ts[$type_id]) ? $rel_ts[$type_id] : $type_id;
        } else {
            $ret = isset($rel_qx[$type_id]) ? $rel_qx[$type_id] : $type_id;
        }
        return $ret;
    }

    /**
     *
     * just a test
     * $str = 'phpddt.com<meta http-equiv="refresh" content="0;">';
     * Common::clean_xss($str);//如果你把这个注释掉，你就知道xss攻击的厉害了
     * echo $str;
     * @blog http://www.phpddt.com
     *
     * @author sunhongjing 2013-11-08
     * @param $string
     * @param $low_level 安全别级低
     * @return bool
     */
    public static function clean_xss($string, $low_level = false)
    {
        if (!is_array($string)) {
            $string = trim($string);
            $string = strip_tags($string);
            $string = htmlspecialchars($string);
            if ($low_level) {
                return $string;
            }
            //$string = str_replace ( array ('"', "\\", "'", "/", "..", "../", "./", "//" ), '', $string );
            $no = '/%0[0-8bcef]/';
            $string = preg_replace($no, '', $string);
            $no = '/%1[0-9a-f]/';
            $string = preg_replace($no, '', $string);
            $no = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
            $string = preg_replace($no, '', $string);
            return $string;
        }
        $keys = array_keys($string);
        foreach ($keys as $key) {
            $string [$key] = self::clean_xss($string [$key], $low_level);
        }
        return $string;
    }


    /**
     * 定时任务开头标准输出
     * @author sunhongjing 2013-08-31
     *
     * @param unknown_type $str
     * @return string
     */
    public static function jobBegin($str = '')
    {
        $ext_str = empty($str) ? '' : trim($str);
        return "\r\n------" . $ext_str . '------Job start: ' . date('Y-m-d H:i:s', time()) . "------------\r\n";
    }

    /**
     * 定时任务结尾标准输出
     * @author sunhongjing 2013-08-31
     *
     * @param unknown_type $str
     * @return string
     */
    public static function jobEnd($str = '')
    {
        $ext_str = empty($str) ? '' : trim($str);
        return "\r\n------" . $ext_str . '------Job end: ' . date('Y-m-d H:i:s', time()) . "------------\r\n";
    }


    /**
     *  格式化数据
     * @author sunhongjing 2013-08-08
     * @param string $str
     *
     * @return array
     *
     */
    public static function myStr2Array($str)
    {
        $ret = array();

        if (empty($str)) {
            return $ret;
        }
        $ret = preg_split('/[\r\n]+/', $str);
        return $ret;
    }

    /**
     * 格式化时间
     * @author sunhongjing 2013-09-10
     * @param unknown_type $log_time
     * @return string
     */
    public static function format_log_time($log_time)
    {
        $log_time = trim($log_time);

        if (strlen($log_time) != 14) return false;
        $year = @substr($log_time, 0, 4);
        $month = @substr($log_time, 4, 2);
        $day = @substr($log_time, 6, 2);
        $hour = @substr($log_time, 8, 2);
        $min = @substr($log_time, 10, 2);
        $sec = @substr($log_time, 12, 2);

        $date = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $min . ":" . $sec;
        return $date;
    }

    /**
     *  通过判断权限返回手机号码
     * @param $phone  电话号码
     * @param string $type (driver/customer)
     */
    public static function parsePhone($phone, $type = 'driver')
    {
        //查看用户是否有查看用户电话号的权限，如果有全部显示。如果没有显示隐藏中间位的手机号
//        $admin_group = new AdminGroup();
//        $role_id = $type == 'driver' ? $admin_group->getIdByCode('driver_phone_view') : $admin_group->getIdByCode('admins_phone_view');
        $auth = ($type == 'driver') ? AdminSpecialAuth::model()->haveSpecialAuth('driver_phone') : AdminSpecialAuth::model()->haveSpecialAuth('user_phone');
        if (!$auth) {
            if (strlen($phone) == 11) {
                $pattern = "/(1\d{1,2})\d\d(\d{0,3})/";
                $replacement = "\$1*****\$3";
                $phone = preg_replace($pattern, $replacement, $phone);
            }
        }
        return $phone;
    }

    /**
     * 返回司机号码，如果没有查看司机电话号码权限则返回中间五位隐藏的手机号
     * @param $phone
     */
    public static function parseDriverPhone($phone)
    {
        return self::parsePhone($phone, 'driver');
    }

    /**
     * 返回客户号码，如果没有查看客户电话号码权限则返回中间五位隐藏的手机号
     * @param $phone
     */
    public static function parseCustomerPhone($phone)
    {
        return self::parsePhone($phone, 'customer');
    }

    /**
     *  通过判断权限返回返回优惠券
     * @param $bonus  返回优惠券
     * add:2014-02-26 by zhangtongkai
     */
    public static function parseBonus($bonus)
    {
        //查看用户是否有查看优惠券的权限，如果有全部显示。如果没有,显示隐藏后两位的优惠券

        if (!AdminSpecialAuth::model()->haveSpecialAuth('bonus')) {
            if (strlen($bonus) >1) {
                $bonus_last = substr_replace($bonus, '', 0, strlen($bonus)-2);
                $bonus=str_pad($bonus_last,strlen($bonus),"*",STR_PAD_LEFT);
            }
        }
        return $bonus;
    }

    /**
     * 导出xls文件
     * @param <array> $data     主体部分内容
     * @param <array> $header   标题
     * @param <sting> $fileName 文件名
     */
    public static function exportXls($data, $header = null, $fileName = 'new file.xls')
    {
        $xls[] = "<html><meta http-equiv=content-type content=\"text/html; charset=gbk\"><body><table border='1'>";
        $xls[] = "<tr><td>" . implode("</td><td>", array_values($header)) . '</td></tr>';
        foreach ($data As $o) {
            $line = array();
            foreach ($header AS $k => $v) {
                $line[] = $o[$k];
            }
            $xls[] = '<tr><td style="vnd.ms-excel.numberformat: @;">' . implode('</td><td style="vnd.ms-excel.numberformat: @;">', $line) . '</td></tr>';
        }
        $xls[] = '</table></body></html>';
        $xls = join("\r\n", $xls);
        header('Content-Disposition: attachment; filename="' . $fileName . '.xls"');
        die(mb_convert_encoding($xls, 'gbk', 'UTF-8'));
    }

    public static function  hasPermission($controller , $action){
        $permission_mod = new AdminActions();
        $permission = $permission_mod->havepermission($controller , $action);
        return $permission ;
    }

    /**
     * 导出csv文件
     * @param <array> $data     主体部分内容
     * @param <array> $header   标题
     * @param <sting> $fileName 文件名
     */
    public static function exportCsv($data, $header = null, $fileName = 'new file.csv')
    {
        $csv = new ECSVExport($data);
        if ($header !== null) {
            $csv->setHeaders($header);
        }

        $result = $csv->toCSV();
        $result = iconv('UTF-8', 'gbk', $result);
        Yii::app()->request->sendFile($fileName, $result, null, false);
    }

    /**
     * 获取随机码，3位字母+固定5位长度数字
     * 可调整字母长度
     * @param int $num 字母长度
     * @return string
     * @author bidong 2013-09-11
     */
    public static function makeRandCode($num = 4)
    {
        $re = '';
        $s = 'abcdefghkmnpqrstuvwxyz'; //ABCDEFGHJKMNPQRSTUVWXYZ
        while (strlen($re) < $num) {
            $re .= $s[rand(0, strlen($s) - 1)]; //从$s中随机产生一个字符
        }
        $re .= substr(str_shuffle(str_repeat('23456789', 5)), 0, 5);
        return $re;
    }


    /**
     * 生成指定位数优惠码
     * @param array $param
     * @return bool|int
     * @author daiyihui
     */
    public static function createBonusCode($param = array())
    {
        if (is_array($param) && !empty($param)) {
            if (!empty($param['coupon_rules'])) {
                return self::createRandNumber($param['coupon_rules']);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 产生指定位数优惠码随机数
     * @param $number 位数
     * @return string
     * @author daiyihui
     */
    public static function createRandNumber($number, $type = '')
    {
        $number = (int)$number;
        if ($number === 0) {
            return '';
        } else {
            $rankNumberString = "";
            for ($i = 0; $i < $number + 1; $i++) {
                if ($i !== 0 && $i % 2 === 0) {
                    $rankNumberString .= mt_rand(11, 99);
                }
            }
            if ($number % 2 !== 0) {
                $rankNumberString = $rankNumberString . mt_rand(1, 9);
            }
            if ($type == 1) {
                return $rankNumberString;
            }
            $couponCode = BonusLibrary::model()->find('bonus_sn=:bonus_sn', array(':bonus_sn' => $rankNumberString));
            if (!empty($couponCode)) {
                self::createRandNumber($number);
            } else {
                return $rankNumberString;
            }
        }
    }


    /**
     * 检测升级版本
     * @author zhanglimin 2013-09-16
     * @param string $variant
     * @param int $app_ver
     * @param string $driver_id
     * @return array
     */
    public static function getCheckUpdateVersion($variant = "", $app_ver = 0, $driver_id = "")
    {
        $ret = array(
            'flag' => false,
            'url' => '',
        );
        if ($variant == "test") {
            $ret = self::checkUpdateDriverTestVersion($app_ver);
        } else {
            $ret = self::checkUpdateDriverVersion($app_ver, $driver_id);
        }
        return $ret;
    }

    /**
     * 升级司机端版本
     * @author zhanglimin 2013-09-16
     * @param int $app_ver
     * @param string $driver_id
     * @return array
     */
    public static function checkUpdateDriverVersion($app_ver = 0, $driver_id = "")
    {
        $ret = array(
            'flag' => false,
            'url' => '',
        );

        if ($app_ver > "1.5.0") {
            $ret = self::checkUpdateDriverTestVersion($app_ver);
        } else {
            //黑车版本不强制升级
            if ($app_ver > '1.1.0') {

                $varsion = Yii::app()->params['appVersion']['appVersionDriver'];

                if ($app_ver <= $varsion['deprecated']) {
                    $ret['flag'] = true;
                    $ret['url'] = $varsion['url'];
                }

            }
        }
        return $ret;
    }

    /**
     *
     * @param string $domain 域名
     * @param int $level 域名级别 1是顶级  2是二级
     * @return string
     */
    public static function getDomain($domain, $level = 1)
    {
        $domains = explode("/", $domain);
        $domain = $domains[0];

        if ($level < 1) $level = 1;
        $sp = explode(".", $domain);
        if (count($sp) <= ($level + 1)) {
            return $domain;
        }
        $spp = array_reverse($sp);

        $d = array_slice($spp, 0, $level + 1);
        $dd = array_reverse($d);

        return join(".", $dd);
    }

    /**
     * 升级司机端测试版本
     * @author zhanglimin 2013-09-16
     * @param int $app_ver
     * @return array
     */
    public static function checkUpdateDriverTestVersion($app_ver = 0)
    {
        $ret = array(
            'flag' => false,
            'url' => '',
        );
        if (Yii::app()->params['appVersion']['appVersionDriverTest']['latest'] > $app_ver) {
            $ret['flag'] = true;
            $ret['url'] = Yii::app()->params['appVersion']['appVersionDriverTest']['url'];
        }
        return $ret;
    }


    /**
     * 司机版本部分范围升级
     * @author zhanglimin 2013-09-16
     * @param int $app_ver
     * @param string $driver_id
     * @return array
     */

    public static function checkUpdateDrivers($app_ver = 0, $driver_id = "")
    {
        $ret = array('flag' => false, 'url' => "");
        if ($app_ver == 0 || empty($driver_id)) {
            return $ret;
        }

        $varsion = Yii::app()->params['appVersion']['appVersionRule'];

        if ($varsion['is_enabled'] == 0) {
            return $ret;
        }

        //可以范围升级
        if ($varsion['config']['latest'] > $app_ver && isset($varsion['citys'])) {
            //$driver_id = strtoupper($driver_id);
            $driver_info = DriverStatus::Model()->get($driver_id);
            $city_id = $driver_info->city_id ;
            $driver_city = Dict::item("city_prefix",$city_id);
            //$driver_city = substr($driver_id, 0, 2);
            if (isset($varsion['citys'][$driver_city])) {
                $up_citys = $varsion['citys'][$driver_city];
                $ret = self::updateVersionRule($up_citys, $varsion, $driver_id, $driver_city);
            }
        }
        return $ret;
    }


    /**
     * 升级规则
     * @author zhanglimin 2013-09-16
     * @param $up_citys
     * @param $varsion
     * @param $driver_id
     * @param $driver_city
     * @return array
     */
    public static function updateVersionRule($up_citys, $varsion, $driver_id, $driver_city)
    {

        $ret = array('flag' => false, 'url' => "");

        if (empty($up_citys) || (isset($up_citys['type']) && $up_citys['type'] == 0)) {
            //全部
            $ret['flag'] = true;
            $ret['url'] = $varsion['config']['url'];
        } elseif (isset($up_citys['type']) && $up_citys['type'] == 1) {
            //指定工号
            if (in_array($driver_id, $varsion['citys'][$driver_city]['drivers'])) {
                $ret['flag'] = true;
                $ret['url'] = $varsion['config']['url'];
            }
        } elseif (isset($up_citys['type']) && $up_citys['type'] == 2) {
            if (isset($up_citys['drivers']['start']) && isset($up_citys['drivers']['end'])) {
                for ($i = $up_citys['drivers']['start']; $i <= $up_citys['drivers']['end']; $i++) {
                    $in_array[] = $driver_city . sprintf("%04d", $i);
                }
                if (in_array($driver_id, $in_array)) {
                    $ret['flag'] = true;
                    $ret['url'] = $varsion['config']['url'];
                }
            }
        }
        return $ret;
    }

    /**
     * @param array $rules
     * @param string $areaId
     * @return bool|string
     * @author daiyihui
     */
    public static function getCouponNum($rules = array(), $areaId = '')
    {
        if (!empty($rules) && !empty($areaId)) {
            $randNum = self::createBonusCode($rules);
            $couponNum = $areaId . $randNum;
            $couponCode = BonusLibrary::model()->find('bonus_sn=:bonus_sn', array(':bonus_sn' => $couponNum));
            if (!empty($couponCode)) {
                self::getCouponNum($rules, $areaId);
            } else {
                return $couponNum;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取司机最后星级
     * @param array $star_arr
     * @param int $serviceCount
     * @param int $city_id
     * @return float
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-09-12
     */
    public static function _getDriverLevelNewRule($star_arr, $serviceCount, $city_id)
    {
        $level = 0;
        //城市等级
        $cityLevel = self::getCityLevel($city_id);
        if ($star_arr['comments_num'] > 0) {
            //$level = number_format((($star_five*$five) - ($star_one * $one) - ($star_two * $two) - ($star_three * $three) - ($star_four * $four)) / $star_arr['comments_num'], 1);
            $level = number_format($star_arr['point'] / $star_arr['comments_num'], 1);
        }
        if ($level <= 0) {
            $level = 0;
        }

        if ($level > 5) {
            $level = 5;
        }

        if ($serviceCount > 0) {
            switch ($cityLevel) {
                case 1:
                    if ($serviceCount > 0 && $serviceCount <= 10) {
                        if ($level > 1) {
                            $level = 1;
                        }
                    } else if ($serviceCount > 10 && $serviceCount <= 20) {
                        if ($level > 2) {
                            $level = 2;
                        }
                    } else if ($serviceCount > 20 && $serviceCount <= 80) {
                        if ($level > 3) {
                            $level = 3;
                        }
                    } else if ($serviceCount > 80 && $serviceCount <= 200) {
                        if ($level > 4) {
                            $level = 4;
                        }
                    }
                    break;

                case 2:
                    if ($serviceCount > 0 && $serviceCount <= 10) {
                        if ($level > 1) {
                            $level = 1;
                        }
                    } else if ($serviceCount > 10 && $serviceCount <= 20) {
                        if ($level > 2) {
                            $level = 2;
                        }
                    } else if ($serviceCount > 20 && $serviceCount <= 80) {
                        if ($level > 3) {
                            $level = 3;
                        }
                    } else if ($serviceCount > 80 && $serviceCount <= 150) {
                        if ($level > 4) {
                            $level = 4;
                        }
                    }
                    break;

                default:
                    if ($serviceCount > 0 && $serviceCount <= 10) {
                        if ($level > 1) {
                            $level = 1;
                        }
                    } else if ($serviceCount > 10 && $serviceCount <= 20) {
                        if ($level > 2) {
                            $level = 2;
                        }
                    } else if ($serviceCount > 20 && $serviceCount <= 40) {
                        if ($level > 3) {
                            $level = 3;
                        }
                    } else if ($serviceCount > 40 && $serviceCount <= 100) {
                        if ($level > 4) {
                            $level = 4;
                        }
                    }
                    break;
            }
        } else {
            $level = 0;
        }
        return $level;
    }
	
	
	/**
     * 获取司机最后星级
     * @param array $star_arr
     * @param int $serviceCount
     * @param int $city_id
     * @return float
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-09-12
     */
    public static function _getDriverLevelNewRule2($star_arr, $serviceCount, $city_id)
    {
        $level = 0;
        //城市等级
        $cityLevel = self::getCityLevel($city_id);
        if ($star_arr['comments_num'] >= 0) {
            //$level = number_format((($star_five*$five) - ($star_one * $one) - ($star_two * $two) - ($star_three * $three) - ($star_four * $four)) / $star_arr['comments_num'], 1);
			if($star_arr['comments_num']<=10 ){ //&& $city_id==24
				$level_f = number_format(($star_arr['point']+15) / ($star_arr['comments_num']+5), 1);
				
				
			} else {
				$level_f = number_format($star_arr['point'] / $star_arr['comments_num'], 1);
			}
			
			if($level_f - floor($level_f)==0.5){
				$level = $level_f;
			} else {
				$level_s = round($level_f);
				if($level_s < $level_f){
					$level = $level_s+0.5;
				} else {
					$level = $level_s;
				}
			}
            
        }
        if ($level <= 0) {
            $level = 0;
        }

        if ($level > 5) {
            $level = 5;
        }

        if ($serviceCount >= 0) {
            switch ($cityLevel) {
                case 1:
					
						if ($serviceCount >= 0 && $serviceCount <= 50) {						
							if ($level > 4) {
								$level = 4;
							}                        
						} else if ($serviceCount > 50 && $serviceCount <= 100) {
							if ($level > 4.5) {
								$level = 4.5;
							}
						} else if ($serviceCount > 100 ) {
							if ($level > 5) {
								$level = 5;
							}
						}	
						
						
					 
                    
                    break;

                case 2:
					
						if ($serviceCount >= 0 && $serviceCount <= 40) {						
							if ($level > 4) {
								$level = 4;
							}                        
						} else if ($serviceCount > 40 && $serviceCount <= 80) {
							if ($level > 4.5) {
								$level = 4.5;
							}
						} else if ($serviceCount > 80 ) {
							if ($level > 5) {
								$level = 5;
							}
						}	
						
						
					 
                    break;

                default:
					
						if ($serviceCount >= 0 && $serviceCount <= 30) {						
							if ($level > 4) {
								$level = 4;
							}                        
						} else if ($serviceCount > 30 && $serviceCount <= 60) {
							if ($level > 4.5) {
								$level = 4.5;
							}
						} else if ($serviceCount > 60 ) {
							if ($level > 5) {
								$level = 5;
							}
						}	
						
						
					 
                    break;
            }
        } else {
            $level = 0;
        }
        return $level;
    }
	

    /**
     * 检查输入是否非法，不能包含辱骂性字符
     *
     * @author sunhongjing 2013-11-10
     * @param unknown_type $name
     * @return string
     */
    public static function hasSensitiveWords($name = '', $len = 16)
    {
        $flag = false;
        if (empty($name)) {
            return $flag;
        }

        if ($len < mb_strlen($name)) {
            return $flag = true;
        }

        //非法字符集
        $sensitiveWords = array('大爷', '尼玛', '管理', '垃圾', '狗屎');

        for ($i = 0; $i < count($sensitiveWords); $i++) {
            $content = substr_count($name, $sensitiveWords[$i]);
            if ($content > 0) {
                $flag = $content;
                break;
            }
        }

        if ($flag > 0) {
            //有违法字符
            $flag = true;
        }
        return $flag;
    }


    /**
     * 验证手机号
     * @author zhanglimin 2013-09-29
     *
     * @editor sunhongjing 2013-10-10 利民正则写的太搞笑了
     * @param string $phone
     * @return bool
     */
    public static function  checkPhone($phone = '')
    {
        $flag = false;
        if (empty($phone)) {
            return $flag;
        }

        $reg = "/^1[345678]{1}[0-9]{9}$/";

        if (preg_match($reg, $phone)) {
            $flag = true;
        }
        return $flag;
    }


    /**
     * 验证是否电信手机
     * //133|153|180|181|189|
     * @param $phone
     * @return bool
     * @author bidong 2013-12-11
     */
    public static function isCTCCPhone($phone)
    {
        $flag = false;
        $prefix = array(133, 153, 180, 181, 189);

        if (!empty($phone) && self::checkPhone($phone)) {
            $phone_pre = substr($phone, 0, 3);
            if (in_array($phone_pre, $prefix)) {
                $flag = true;
            }
        }
        return $flag;
    }


    /**
     * 生成call_id
     * @return string $call_id
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-16
     */
    public static function genCallId()
    {
//    	$rand_md5 = md5(uniqid(rand(), true));
//    	$randstr = self::genRandStr(6 , 0);
//    	$call_id = $rand_md5.$randstr;
        $call_id = md5(uniqid(rand(), true));
        return $call_id;
    }

    /**
     * 生成随机字符串方法
     * @return int $length
     * @return int $type
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-16
     */
    public static function genRandStr($length = 6, $type = 0)
    {
        $randstr = '';
        switch ($type) {
            case 0:
                $ascii_start = 97;
                $ascii_end = 122;
                break;
            case 1:
                $ascii_start = 65;
                $ascii_end = 90;
                break;
            case 2:
                $ascii_start = 48;
                $ascii_end = 57;
                break;
            default:
                $ascii_start = 97;
                $ascii_end = 122;
                break;
        }
        for ($i = 0; $i < $length; $i++) {
            $randstr .= chr(mt_rand($ascii_start, $ascii_end));
        }
        return $randstr;
    }

    /**
     * 基础价格
     * Enter description here ...
     * @param unknown_type $city
     * @param unknown_type $booking_time
     * @return int
     */
    public static function base_price($city, $booking_time)
    {
        switch ($city) {
            case 1:
            case 3:
            case 5:
            case 6: //北、上、广、深
                return self::base_3_10($booking_time);
                break;
            case 2: //成都
                return self::base_1_5();
                break;
            case 7: //重庆
                return self::base_1_10();
                break;
            default: //默认
                return self::base_2_10($booking_time);
                break;
        }
    }


    /**
     * 固定收费 基础公里5公里 每五公里加20
     * @return int
     * @author mengtianxue
     */
    public static function base_1_5()
    {
        $return = array();
        $return['price'] = 39;
        $return['km'] = 5;
        $return['Step'] = 5;
        return $return;
    }

    /**
     * 固定收费 基础公里10公里 每五公里加20
     * @return int
     * @author mengtianxue
     */
    public static function base_1_10()
    {
        $return = array();
        $return['price'] = 39;
        $return['km'] = 10;
        $return['Step'] = 5;
        return $return;
    }

    /**
     * 固定收费 基础公里10公里 每五公里加20
     * @param $booking_time
     * @return int
     * @author mengtianxue
     */
    public static function base_2_10($booking_time)
    {
        $return = array();
        $time = date('H', $booking_time);
        if (7 <= $time && $time < 22) {
            $price = 39;
        } else {
            $price = 59;
        }
        $return['price'] = $price;
        $return['km'] = 10;
        $return['Step'] = 5;
        return $return;
    }

    /**
     * 固定收费 基础公里10公里 每十公里加20
     * @param $booking_time
     * @return int
     * @author mengtianxue
     */
    public static function base_3_10($booking_time)
    {
        $return = array();
        $time = date('H', $booking_time);
        if ($time < 7) {
            $price = 99;
        } elseif ($time < 22) {
            $price = 39;
        } elseif ($time == 22) {
            $price = 59;
        } else {
            $price = 79;
        }
        $return['price'] = $price;
        $return['km'] = 10;
        $return['Step'] = 10;
        return $return;
    }

    /**
     * 拼接url
     * @author zhangtingyi
     * @param string $baseURL 基于的url
     * @param array $keysArr 参数列表数组
     * @return string           返回拼接的url
     */
    public static function combineURL($baseURL, $keysArr)
    {
        $combined = strpos($baseURL, "?") == false ? $baseURL . "?" : $baseURL . "&";
        $valueArr = array();

        foreach ($keysArr as $key => $val) {
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&", $valueArr);
        $combined .= ($keyStr);
        return $combined;
    }

    /**
     * 服务器通过get请求获得内容
     * @author zhangtingyi
     * @param string $url 请求的url,拼接后的
     * @return string           请求返回的内容
     */
    public static function get_contents($url)
    {
        /*
        if (ini_get("allow_url_fopen") == "1") {
            $response = file_get_contents($url);
        }else{
        */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);
        //}
        return $response;
    }

    /**
     * get方式请求资源
     * @author zhangtingyi
     * @param string $url 基于的baseUrl
     * @param array $keysArr 参数列表数组
     * @return string         返回的资源内容
     */
    public static function get($url, $keysArr)
    {
        $combined = self::combineURL($url, $keysArr);
        return self::get_contents($combined);
    }

    /**
     * post方式请求资源
     * @author zhangtingyi
     * @param string $url 基于的baseUrl
     * @param array $keysArr 请求的参数列表
     * @param int $flag 标志位
     * @return string           返回的资源内容
     */
    public static function post($url, $keysArr, $flag = 0)
    {
        $ch = curl_init();
        if (!$flag) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    /**
     * 验证身份证号
     * @author zhangtingyi at 2013-11-29
     * @param $id_card
     * @return bool
     */
    public static function checkIdCard($id_card)
    { // 检查是否是身份证号
        if (strlen($id_card) != 15 && strlen($id_card) != 18) {
            return false;
        }
        $id_card = self::parseIDCard($id_card);
        // 转化为大写，如出现x
        $number = strtoupper($id_card);
        //加权因子
        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码串
        $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        //按顺序循环处理前17位
        $sigma = 0;
        for ($i = 0; $i < 17; $i++) {
            //提取前17位的其中一位，并将变量类型转为实数
            $b = (int)$id_card{$i};
            //提取相应的加权因子
            $w = $wi[$i];
            //把从身份证号码中提取的一位数字和加权因子相乘，并累加
            $sigma += $b * $w;
        }
        //计算序号
        $snumber = $sigma % 11;

        //按照序号从校验码串中提取相应的字符。
        $check_number = $ai[$snumber];
        if ($id_card{17} == $check_number) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据身份证号获得年龄
     * @author zhangtingyi at 2013-11-29
     * @param $id_card
     * @return bool|string
     */
    public static function getAgeByIdCard($id_card)
    {
        if (self::checkIdCard($id_card)) {
            $sub_str = substr($id_card, 6, 4);
            $now = date("Y", time());
            return $now - $sub_str;
        } else {
            return false;
        }
    }

    /**
     * 根据驾驶证申领日期获得驾龄
     * @author zhangtingyi at 2013-11-29
     * @param $application_date 驾驶证申领日期
     * @return bool|string
     */
    public static function getDriverYear($application_date)
    {
        if (time() > $application_date) {
            $sub_str = date('Y', strtotime($application_date));
            $now = date("Y", time());
            $driver_year = $now - $sub_str;
            return $driver_year;
        } else {
            return false;
        }
    }

    /**
     * 根据身份证号获得出生日期
     * @author zhangtingyi at 2013-11-29
     * @param $id_card
     * @return string
     */
    public static function getBirthDayByIdCard($id_card)
    {
        $id_card = self::parseIDCard($id_card);
        $birth_day = substr($id_card, 6, 8);
        return $birth_day;
    }


    public static function downloadFile($from_address,$save_dir, $to_name){
        $file = self::get_contents($from_address);
        $fp = fopen($save_dir.'/'.$to_name,'w');
        if($fp){
            $res = fwrite($fp, $file);
            fclose($fp);
            return $res;
        }
        return false;

    }

    /**
     * 将15位身份证号转成18位
     * @author zhangtingyi at 2013-11-29
     * @param $idCard
     * @return string
     */
    public static function parseIDCard($idCard)
    {
        // 若是15位，则转换成18位；否则直接返回ID
        if (15 == strlen($idCard)) {
            $W = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1);
            $A = array("1", "0", "X", "9", "8", "7", "6", "5", "4", "3", "2");
            $s = 0;
            $idCard18 = substr($idCard, 0, 6) . "19" . substr($idCard, 6);
            $idCard18_len = strlen($idCard18);
            for ($i = 0; $i < $idCard18_len; $i++) {
                $s = $s + substr($idCard18, $i, 1) * $W [$i];
            }
            $idCard18 .= $A [$s % 11];
            return $idCard18;
        } else {
            return $idCard;
        }
    }

    /**
     * 根据身份证号获得地址（省、市） 注意：此方法依赖百度接口，仅做为临时使用，不要用于长期项目
     * @author zhangtingyi by 2014-01-02
     * @param $id_card
     * @return array|bool
     */
    public static function getAddressByIdCard($id_card)
    {
        if (self::parseIDCard($id_card)) {
            $result = self::getIdCardInfo($id_card);
            if ($result['success']) {
                $address = $result['address'];
                /*
                if (strpos($address, '省')) {
                    $arr = explode('省', $address);
                } else if (strpos($address, '自治区')) {
                    $arr = explode('自治区', $address);
                } else {
                    $arr = explode('市', $address);
                }
                $arr[1] = str_replace(array('市','区','县'), '' , $arr[1]);
                return $arr;
                */
                return $address;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获得身份证信息   注意：此方法依赖百度接口，仅做为临时使用，不要用于长期项目
     * @author zhangtingyi
     * @param $id_card
     * @return bool|mixed
     */
    public static function getIdCardInfo($id_card)
    {
        if (self::parseIDCard($id_card)) {
            $url = 'http://baidu.uqude.com/baidu_mobile_war/idcard/dishi.action?cardNO=' . $id_card;
            $data = Common::get_contents($url);
            $result = json_decode($data, true);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 根据时间戳获取哪一年的第几周（年末年初有交叉的算是下一年的第一周）
     * @param <int> $time
     * @return <array>  array('Y'=>年份, 'W'=>第几周)
     * @author liuxiaobo
     * @since 2014-1-17
     */
    public static function getWeekth($time)
    {
        $week = date('W', $time);
        $month = date('m', $time);
        $year = date('Y', $time);
        if (1 == $week && 12 == $month) {
            $year += 1;
        }
        return array(
            'Y' => $year,
            'W' => $week,
        );
    }

    /**
     * 判断两个时间戳是不是同一周
     * @param <int> $time1
     * @param <int> $time2
     * @return <bool>
     * @author liuxiaobo
     * @since 2014-1-17
     */
    public function isSameWeek($time1, $time2)
    {
        $week1 = Common::getWeekth($time1);
        $week2 = Common::getWeekth($time2);
        return ($week1['Y'] == $week2['Y'] && $week1['W'] == $week2['W']) ? TRUE : FALSE;
    }

    /**
     * 用 mb_strimwidth 来截取字符，使中英尽量对齐。
     * @author liuxiaobo
     * @since 2014-1-17
     */
    public static function wsubstr($str, $start, $width, $trimmarker = '...')
    {
        $_encoding = mb_detect_encoding($str, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        return mb_strimwidth($str, $start, $width, $trimmarker, $_encoding);
    }

    /**
     * 可逆加密，返回加密后的字符串
     * @param <str> $data   加密前的字符串
     * @param <str> $key    密钥（加密、解密 时应该是同一个密钥）
     * @return <str>
     * @link Common::decrypt($data, $key) 解密函数
     * @author liuxiaobo
     * @since 2014-2-27
     */
    public static function encrypt($data, $key)
    {
        $key = md5($key);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        return base64_encode($str);
    }

    /**
     * 可逆加密，返回解密后的字符串
     * @param <str> $data   加密后的字符串
     * @param <str> $key    密钥（加密、解密 时应该是同一个密钥）
     * @return <str>
     * @link Common::encrypt($data, $key) 加密函数
     * @author liuxiaobo
     * @since 2014-2-27
     */
    public static function decrypt($data, $key)
    {
        $key = md5($key);
        $x = 0;
        $data = base64_decode($data);
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }

    /**
     * 可逆加密，返回 订单id 加密后的字符串
     * @param <str> $orderId    订单id
     * @return <str>
     * @link Common::decryptOrderId($data, $key) 解密函数
     * @author liuxiaobo
     * @since 2014-2-27
     */
    public static function encryptOrderId($orderId)
    {
        $orderId = (string)$orderId;
        $key = 'edj';
        return self::encrypt($orderId, $key);
    }

    /**
     * 可逆加密，返回 订单id 解密后的字符串
     * @param <str> $orderId    订单id
     * @return <str>
     * @link Common::encryptOrderId($data, $key) 加密函数
     * @author liuxiaobo
     * @since 2014-2-27
     */
    public static function decryptOrderId($encryptStr)
    {
        $encryptStr = (string)$encryptStr;
        $key = 'edj';
        return self::decrypt($encryptStr, $key);
    }

    public static function get_extension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }


    public static function get_current_time()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)$usec + (float)$sec;

    }


    public static function get_time_intv($start_time)
    {
        $end_time = Common::get_current_time();
        return sprintf("%0.4f", ($end_time - $start_time) * 1000);
    }


    public static function get_gpid($run_time = '')
    {
        $local_ip = getHostByName(getHostName());
        if (empty($run_time))
            $run_time = time();

        $pid = getmypid();

        $gpid = "$local_ip/$run_time/$pid";

        return $gpid;

    }


    /**
     * @desc  获取百度坐标
     * @param $lng
     * @param $lat
     * @param $gps_type
     * @return array
     */
    public static function getBaiduGps($lng , $lat , $gps_type) {

        switch ($gps_type) {
            case 'wgs84' :
                //把google座标转换为百度座标后查询最近司机
                $baidu = GPS::model()->Wgs2Baidu($lng, $lat);
                $longitude = sprintf('%.6f', $baidu['longitude']);
                $latitude = sprintf('%.6f', $baidu['latitude']);
                break;
            case 'google' :
                $baidu = GPS::model()->Google2Baidu($lng, $lat);
                $longitude = sprintf('%.6f', $baidu['longitude']);
                $latitude = sprintf('%.6f', $baidu['latitude']);
                break;
            case 'baidu' :
                $longitude = $lng;
                $latitude = $lat;
                break;
            default :
                $longitude = $lng;
                $latitude = $lat;
                break;
        }
        $gps = array(
            'lng' => $longitude,
            'lat' => $latitude,
        );
        return $gps;

    }

    /**
     * 补贴活动（按司机接单到就位的时间和公里数给司机或客户补贴:）
     *         南京司机9分45秒内到达给司机补贴（0~1km:5元 、 1~2km:16元 、2~3km:19元 、3~4km:29 、4~5km:39元 、5km以上:59元）
     *         南京司机超过9分45秒到达 客户免单
     * @param int $city_id
     * @param string $time
     * @param float $distance
     * @param float $income
     * @return array $result
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-04-03
     */
    public static function orderSubsidy($city_id, $time, $distance, $income)
    {
        $activity_config = Yii::app()->params['activity'];
        $subsidy = isset($activity_config[$city_id]['subsidy']) ? $activity_config[$city_id]['subsidy'] : array();
        $result = array(
            'driver_subsidy_money' => 0,
            'customer_subsidy_money' => 0,
        );

        //城市没有补贴活动
        if (empty($subsidy)) {
            return $result;
        }

        //城市补贴活动已关闭
        if (!$subsidy['turn_on']) {
            return $result;
        }

        if ($time < $subsidy['ready_time']) {

            //补偿司机
            foreach ($subsidy['driver_subsidy'] as $key => $driver_subside) {

                if ($distance >= $driver_subside['start_distance'] && $distance < $driver_subside['end_distance']) {
                    $result['driver_subsidy_money'] = $driver_subside['subsidy'];
                }

            }

        } else {

            //客户免单
            $result['customer_subsidy_money'] = $income;

        }

        return $result;
    }

    /*
     * 通过司机的城市ID 和工号 检查是否是返程车司机 是返回true 不是返程车司机返回false
     */
    public static function checkBlackCar($driver_id, $city = ''){
        if($city && $city == 1){
            $driver_id_num = substr($driver_id,2);
            if ($driver_id_num >= 9800 && $driver_id_num < 10000) {
                return true;
            }
        }
        else{
            $driver_id_num = substr($driver_id,2);
            if ($driver_id_num >= 9800 && $driver_id_num < 10000) {
                return true;
            }
        }
        return false;
    }    

    /**
     * 通过城市id 查看该城市是否上线 ，
     * @param $city_id
     * @param string $key
     * @param string $score
     * @return bool
     */
    public static function checkOpenScoreCity($city_id, $key = '', $score = ''){
        //推广到全国，目前所有城市配置信息一样
        $city_id=1;

        $city_open = Yii::app()->params['driverScore'];
//echo  'aaa';echo $keys;echo 'bbb';die;
        if(isset($city_open['scoreCity'][$city_id])){
            if($key){
                if($key == 'all'){
                    return $city_open[$city_open['scoreCity'][$city_id]];
                }
                if($score){
                    //echo $city_open['scoreCity'][$city_id];die;
                    return isset($city_open[$city_open['scoreCity'][$city_id]]['block'][$score] )
                        ? $city_open[$city_open['scoreCity'][$city_id]]['block'][$score]:false;
                }else {
                    return isset($city_open[$city_open['scoreCity'][$city_id]][$key])
                        ? $city_open[$city_open['scoreCity'][$city_id]][$key] : false;
                }
            }else return true;
        }else return false;

    }

    /**
     *
     * @param string $city_id 司机所在城市id eg:'BJ9003'
     * @param time stemp $compare_time 订单时间或者投诉时间 eg: 14012341234
     * @return bool
     */
    public static function checkOpenScore($city_id, $compare_time){
        $city_open = Yii::app()->params['driverScore'];
        if(isset($city_open['scoreCity'][$city_id])){ //最先开通的5个城市
            return ($compare_time > $city_open[$city_open['scoreCity'][$city_id]]['online_time']);
        }else{ //全国其他城市
            return ($compare_time > $city_open[$city_open['scoreCity'][1]]['all_online_time']);
        }
    }

    /**
     *
     * 获取所有代驾分开通城市id
     * @return list
     */
    public static function getScoreOpenCitys(){
        $openCtiy= RCityList::model()->getOpenCityList();
        return $openCtiy;
    }

    //获取城市每天兑换的皇冠限制
    public static function getCityCrownLimit($city){
        $limit_num = DriverWealthProduct::DEFAULT_CROWN_NUM;// 默认
        $city_crown = Yii::app()->params['driverCrown'];
        if(isset($city_crown['crownCity'][$city])){
            $limit_num = $city_crown[$city_crown['crownCity'][$city]]['dayLimitNum'];
        }
        return $limit_num;
    }
    //获取v2后台兑换皇冠限制
    public static function getCityV2CrownLimit($city){
        $limit_num = 30;// 默认
        $city_crown = Yii::app()->params['driverCrown'];
        if(isset($city_crown['crownCity'][$city])){
            $limit_num = $city_crown[$city_crown['crownCity'][$city]]['v2CrownLimitNum'];
        }
        return $limit_num;
    }

    //获取v2后台兑换e币限制
    public static function getCityV2ELimit($city){
        $limit_num = 20000;// 默认
        $city_crown = Yii::app()->params['driverCrown'];
        if(isset($city_crown['crownCity'][$city])){
            $limit_num = $city_crown[$city_crown['crownCity'][$city]]['v2ELimitNum'];
        }
        return $limit_num;
    }

    public function getCityIdByAddress($address){
        $mod = GPS::model();
        $gps_info = $mod->geocoding($address);
        if($gps_info && isset($gps_info['location'])){
            $gps = $gps_info['location'];
            $city_name = $mod->getCityByBaiduGPS($gps['lng'],$gps['lat']);

            $city_id = array_search(trim($city_name),RCityList::model()->getOpenCityList());

            return $city_id;
        }
        else return false;
        //getCityByBaiduGPS
    }

    /**
     * 通过城市id 查看该城市是否上线 ，
     * @param $city_id
     * @param string $key
     * @param string $score
     * @return bool
     */
    public static function getLongDistanceCity($city_id){
        $config = array(
            'flag'=>0,
            'max_tip'=>50,
            'per_kilometer_price'=>2,
            'per_kilometer_time'=>240,
            'start_kilometer'=>5,
            'reach_time'=>360
          );
        $close_city = CityConfig::getIdByName("未开通");
        if($city_id == $close_city){ //未开通城市设置不支持
            return $config;
        }
        $city_open = Yii::app()->params['long_distance'];
        //先采用城市A、B、C类获取城市配置
        $cityType=RCityList::model()->getOpenCityByID($city_id,'city_level');
        $cityType= $cityType ? trim(substr($cityType, 0,1)) : '';
        //A类也算3块，跟B一样
        if($cityType == 'A' || $cityType == 'S'){
            $cityType = 'B';
        }
        if(isset($city_open[$cityType])){
            $city_config = $city_open[$cityType];
            $config['flag']=1; //设置为开通
            $config['max_tip']=$city_config['max_tip'];
            $config['per_kilometer_price']=$city_config['per_kilometer_price'];
            $config['per_kilometer_time']=$city_config['per_kilometer_time'];
            $config['start_kilometer']=$city_config['start_kilometer'];
            $config['reach_time']=$city_config['reach_time'];
        }
        //再根据城市具体配置覆盖
        if(isset($city_open['open_city'][$city_id])){
            $config['flag']=1; //设置为开通
            $max_tip = $city_open[$city_open['open_city'][$city_id]]['max_tip'];
            $per_kilometer_price = $city_open[$city_open['open_city'][$city_id]]['per_kilometer_price'];
            $per_kilometer_time = $city_open[$city_open['open_city'][$city_id]]['per_kilometer_time'];
            $start_kilometer = $city_open[$city_open['open_city'][$city_id]]['start_kilometer'];
            $reach_time = $city_open[$city_open['open_city'][$city_id]]['reach_time'];
            $config['max_tip']=$max_tip;
            $config['per_kilometer_price']=$per_kilometer_price;
            $config['per_kilometer_time']=$per_kilometer_time;
            $config['start_kilometer']=$start_kilometer;
            $config['reach_time']=$reach_time;
        }
        return $config;
    }




    public static function DaytimeCastDefault(){
        return 0;
    }

    public static function DaytimeCastTenpercent(){
        return 0.1;
    }

    /**
    * 根据城市获取价格表
    */
    public static function getPriceListByCity($city_id)
    {
        $daytime_price = Yii::app()->params['daytime_price_old'];
        if(RCityList::model()->isOpenDayTime($city_id)){ //新日间业务价格表 客户端先上线杭州 徐州
            $daytime_price = Yii::app()->params['daytime_price_new'];
        }
        return $daytime_price;
    }

   public static function getParam($name, $default = '', $method = 'get', $required = FALSE) {
      switch ($method) {
      case 'post':
          $result = isset($_POST[$name]) ? $_POST[$name] : NULL;
          break;
      case 'get':
          $result = isset($_GET[$name]) ? $_GET[$name] : NULL;
          break;
      case 'cookie':
          $result = isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL;
          break;
      case 'all':
          $result = isset($_REQUEST[$name]) ? $_REQUEST[$name] : NULL;
      }

      //如果是必选参数没有传，抛异常
      if(empty($result) && TRUE == $required){
          throw new Exception('invalid params');
      }

      //设置默认值
      $result = (is_null($result) || $result == '') ? $default : $result;
      $result = is_null($result) ? $result : trim($result);
      return $result;
  }

    public static function outputJson($data = NULL, $msg = 'success', $code = 0){
        header('Content-Type: application/json; charset=utf-8');
            $return = array(
                'code' => $code,
                'message' => $msg,
                'data' => $data,
            );
            echo json_encode($return);
            Yii::app()->end();
        }

}
