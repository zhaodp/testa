/**
 * 用户信息表
 * 记录用户的基本信息
 */

CREATE TABLE IF NOT EXISTS `t_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `description` varchar(512) NOT NULL DEFAULT '',
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/**
 * 用户车型信息
 * 
 * brand：车型品牌的字典库稍后建立
 * status：车况
 */

CREATE TABLE IF NOT EXISTS `t_user_car` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `brand` varchar(10) NOT NULL,
  `status` smallint(6) NOT NULL,
  `number` varchar(10) NOT NULL,
  PRIMARY KEY (`car_id`),
  KEY `user_id` (`user_id`),
  KEY `brand` (`brand`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/**
 * 代驾订单表
 */
CREATE TABLE IF NOT EXISTS `t_order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `imei` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `call_time` int(11) NOT NULL,
  `booking_time` int(11) NOT NULL,
  `reach_time` int(11) NOT NULL,
  `reach_distance` smallint(6) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `distance` smallint(6) NOT NULL,
  `charge` smallint(6) NOT NULL,
  `location_start` varchar(20) NOT NULL,
  `location_end` varchar(20) NOT NULL,
  `income` smallint(6) NOT NULL,
  `cast` smallint(6) NOT NULL,
  `description` varchar(512) NOT NULL DEFAULT '',
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `imei` (`imei`),
  KEY `phone` (`phone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;