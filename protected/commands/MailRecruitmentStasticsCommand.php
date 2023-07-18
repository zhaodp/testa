<?php
/*
 * php sp_edaijia/protected/yiic.php mailrecruitmentstastics SendCountry prod
 * php sp_edaijia/protected/yiic.php mailrecruitmentstastics SendCity prod
 */

class MailRecruitmentStasticsCommand extends LoggerExtCommand{
    private $_to = array(
        'country' => array(
            'prod' => array(
                "zhouyihua@edaijia-inc.cn",
                "lijun@edaijia-inc.cn",
                "yangmingzhi@edaijia-inc.cn",
                "wangmo@edaijia-inc.cn",
                "quyuzongjian@edaijia-inc.cn",
                "xiaolulu@edaijia-inc.cn",
                "zhangjunbiao@edaijia-inc.cn",
                "quanchangjun@edaijia-inc.cn",
                "xuyuanyuan@edaijia-inc.cn",
                "yuanrong@edaijia-inc.cn",
                "tl@edaijia-inc.cn",
                ),
            'test' => array('tongjishi@edaijia-inc.cn', 'chenxin@edaijia-inc.cn'),
        ),
        'city' => array(
            'pord' => array('wangmo@edaijia-inc.cn'),
            'test' => array('tongjishi@edaijia-inc.cn', 'chenxin@edaijia-inc.cn'),
        ),
    );

    private $_env = 'test';
    public function init(){
        global $argv;
        if(isset($argv[3])){
            $this->_env = $argv[3];
        }
    }
    public function actionSendMail(){
        echo Common::jobBegin("全国司机招募数据日报");
        $to = $this->_to['country'][$this->_env];
        $body = $this->_getCountryData();
        $body .= $this->_getCityData();
        $subject = "全国司机招募数据日报_" . date('Ymd', strtotime('-1 days'));
        Mail::sendMail($to, $body, $subject);
        echo Common::jobEnd("全国司机招募数据日报");
    }
    private function _getCountryData(){
        $kpi = CompanyKpiCommon::getMonthRecruitmentKpi(date('Ym', strtotime('-1 days')), 'all', TRUE);
        $achieve = CompanyKpiCommon::getMonthRecruitmentCount(date('Ym', strtotime('-1 days')), 'all', TRUE);
        $subject = "全国司机招募数据日报_" . date('Ymd', strtotime('-1 days'));

        if(0 ==$kpi){
            $body = "kpi 总数设定为 0";
        }else{
            $date = date('Y-m-d', strtotime('-1 days'));
            $precent = round($achieve / $kpi * 100, 2);
            $body = <<<HTML
<h2>全国的招募数据</h2>
<table border='1' cellpadding="0" cellspacing="0">
<tr><th>日期</th><th>本月计划招聘数量</th><th>本月已完成招聘数量</th><th>本月完成率</th></tr>
<tr><td>{$date}</td><td>{$kpi}</td><td>{$achieve}</td><td>{$precent}%</td></tr>
</table>
HTML;
        }
        $this->_clearFunnelData('all');
        return $body;
    }

    private function _getCityData(){
        echo Common::jobBegin("城市司机招募数据日报");
        $sql = "SELECT province_id,t_city_config.city_id as id, t_city_config.city_name name, t_city_config.region_id c_rid, t_city_province.region_id p_rid FROM t_city_config left join t_city_province on t_city_province.id = t_city_config.province_id;";
        $cities = Yii::app()->db_readonly->createCommand($sql)->queryAll();
        $sql = "SELECT * FROM t_region";
        $result = Yii::app()->db_readonly->createCommand($sql)->queryAll();
        $regions = array();
        foreach($result as $region){
            $regions[$region['id']] = $region['name'];
        }
        foreach($cities as &$city){
            if(empty($city['c_rid'])){
                if(!isset($regions[$city['p_rid']])){
                    $city['region'] = 'null';
                    $city['rid'] = PHP_INT_MAX;
                }else{
                    $city['region'] = $regions[$city['p_rid']];
                    $city['rid'] = $city['p_rid'];
                }
            }else{
                $city['region'] = $regions[$city['c_rid']];
                $city['rid'] = $city['c_rid'];
            }
        }
        usort($cities, function($a, $b){
            if($a['rid'] == $b['rid']){
                return 0;
            }
            return $a['rid'] < $b['rid'] ? -1 : 1;
        });
        $body = <<<HTML
<h2>城市司机招募数据</h2>
<table  border='1' cellpadding="0" cellspacing="0">
<tr>
<td>大区</td>
<td>城市</td>
<td>本月计划招聘数量</td>
<td>本月已完成招聘数量</td>
<td>本月完成率</td>
</tr>
HTML;

        foreach($cities as $city){
            $kpi = CompanyKpiCommon::getMonthRecruitmentKpi(date('Ym', strtotime('-1 days')), $city['id'], TRUE);
            $achieve = CompanyKpiCommon::getMonthRecruitmentCount(date('Ym', strtotime('-1 days')), $city['id'], TRUE);
            if(0 == $kpi){
                $precent = 'n/a';
            }else{
                $precent = round($achieve / $kpi * 100 , 2);
            }
            $body .= <<<HTML
<tr>
<td>{$city['region']}</td>
<td>{$city['name']}</td>
<td>{$kpi}</td>
<td>{$achieve}</td>
<td>{$precent}</td>
</tr>
HTML;
        $this->_clearFunnelData($city['id']);
        }

        $body .="</table>";
        return $body;

    }

    //清除缓存并加载新数据写入缓存
    private function _clearFunnelData($city){
        CompanyKpiCommon::getFunnelChartData(date('Y-m-1'), $city, TRUE);
        CompanyKpiCommon::getFunnelChartDataV2(date('Y-m-1'), $city, TRUE);
    }
}
