<?php
/**
 * kpi
 * User: duke
 * Date: 14-09-05
 */

class kpiCommand extends CConsoleCommand
{

    /**
     * 每月3日 定时更新前一个月的数据，保存2个月
     * @use_date eg:201408
     * @author duke 09 05
     */
    public function actionloadCache($use_date = '')
    {
        //$phones=array('13581855712','13241350002','15901530932','18511663962','18911893993','18701552183');
        if(!$use_date){
            $use_date = date('Ym',strtotime('-1 month'));
        }
        $job_title = '缓存上月kpi数据';
        echo Common::jobBegin($job_title);
        $common = new CompanyKpiCommon();

        $business_data = $common->getList(null, CompanyKpiCommon::BACKGROUND_BUSINESS);

        //$setting_data = array_merge($setting_data,$service_data);
        foreach($business_data as $v) {
            $city[$v['city_id']] = $v['city_id'];
        }
        foreach($city as $city_id){
            $common->getServiceData($city_id, $use_date,true);
            $common->getOperateData($city_id, $use_date,true);
            $common->getBusinessData($city_id, $use_date,true);
            echo 'city_id :'.$city_id.'---- type:'.CompanyKpiCommon::BACKGROUND_BUSINESS."over\n";
        }



        $service_data = $common->getList(null, CompanyKpiCommon::BACKGROUND_OPERATE);
        foreach($service_data as $v) {
            $citys[$v['city_id']] = $v['city_id'];
        }

        foreach($citys as $city_id){
            $common->getServiceData($city_id, $use_date,true);
            $common->getOperateData($city_id, $use_date,true);
            echo 'city_id :'.$city_id.'---- type:'.CompanyKpiCommon::BACKGROUND_OPERATE."over\n";
        }

        echo Common::jobEnd($job_title);
    }




}
