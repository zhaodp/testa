<?php

/**
 *  API 接口错误码定义
 *
 *  5XX 订单错误
 */

class ApiErrorCode
{
    const ORDER_SOURCE_ERROR = 506;  // 订单来源错误
    const ORDER_CITY_ERROR = 507;  // 城市未开通业务
    const ORDER_TIME_ERROR = 508;  // 下单时间错误
    const ORDER_CHANNEL_ERROR = 509;  // 不支持订单类型
}
