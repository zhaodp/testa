<?php

class CustomerService extends BaseService
{
    public static function service($className = __CLASS__)
    {
        return parent::service($className);
    }

    /**
     * 一个手机号是否在黑名单里面
     *
     * @param $phone
     * @return mixed
     */
    public function isBlack($phone)
    {
        return CustomerStatus::model()->is_black($phone);
    }

    /**
     * 校验 token是否合法
     *
     * @param $token
     * @return bool
     */
    public function validateToken($token)
    {
        return CustomerToken::model()->validateToken($token);
    }


    /** BCustomers model */

    /**
     * 组织条件
     * @param $items 表attributes
     * @param $params 要查询的参数
     * @return CDbCriteria
     * @auther mengtianxue
     */
    public function conditions($items, $params)
    {
        return BCustomers::model()->conditions($items, $params);
    }

    /**
     * 用户查询
     * @param array $params
     * @return array
     * $type=0,使用缓存,%type=1,不使用缓存
     * @auther mengtianxue
     */
    public function getCustomerInfo($params,$type=0)
    {
        if (is_array($params)) {
            return BCustomers::model()->getCustomerInfo($params);
        } else {
            if($type==0){
                return CustomerMain::model()->getCustomerInfo($params);
            }else{
                return CustomerMain::model()->getCustomer($params);
            }
        }
    }


    /**
     * 初始化客户电话
     * @param string $phone 手机号码
     */
    public function initCustomer($phone)
    {
        return CustomerMain::model()->initCustomer($phone);
    }


    /**
     * 登陆的时候  修改last_login和初次登录修改status
     * Enter description here ...
     * @param unknown_type $phone
     */
    public function updateLastLoginAndStatus($phone)
    {
        return CustomerMain::model()->updateLastLoginAndStatus($phone);
    }

    /** 获取app 版本
     * @param $phone
     * @return mixed
     */
    public function getAppversion($phone)
    {
        return CustomerService::service()-> getAppversion($phone);
    }

    /** 强制刷新用户信息
     * @param $phone
     * @return bool|object
     */
    public function forceGetCustomerInfo($phone)
    {
        return CustomerMain::model()->forceGetCustomerInfo($phone);
    }



    /**
     * 获取黑名单列表
     * @param string $phone
     * @return object $dataProvider
     * @author AndyCong<congming@edaijia.cn> 2013-04-17
     */
    public function getBlackCustomerList($phone = '') {
        //TODO ... 这里返回的是一个 dataProvider 并没有太高的通用性,需要迁移
        return Customer::model()->getBlackCustomerList($phone);
    }
}
