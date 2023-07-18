<?php
class DriverAppTrafficService{
    public function insertInfo($params){
        return DriverAppTraffic::model()->insertInfo($params);
    }
    public function updateInfo($params){
        return DriverAppTraffic::model()->updateInfo($params);
    }
}
