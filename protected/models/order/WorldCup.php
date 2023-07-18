<?php
class WorldCup extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{worldcup_setting}}';
    }

    public function rules()
    {
        return array(
            array('country_1, country_2, begin_time','required'),
	);
    }

    public function attributeLabels()
    {
        return array();
    }
	
   public function getWorldCupSettingList(){
	$criteria = new CDbCriteria;
	$criteria->order(' begin_time asc');
	return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));

   }
const COUNTRY_32 = 32;
const COUNTRY_1 = 1;
const COUNTRY_2 = 2;
const COUNTRY_3 = 3;
const COUNTRY_4 = 4;
const COUNTRY_5 = 5;
const COUNTRY_6 = 6;
const COUNTRY_7 = 7;
const COUNTRY_8 = 8;
const COUNTRY_9 = 9;
const COUNTRY_10 = 10;
const COUNTRY_11 = 11;
const COUNTRY_12 = 12;
const COUNTRY_13 = 13;
const COUNTRY_14 = 14;
const COUNTRY_15 = 15;
const COUNTRY_16 = 16;
const COUNTRY_17 = 17;
const COUNTRY_18 = 18;
const COUNTRY_19 = 19;
const COUNTRY_20 = 20;
const COUNTRY_21 = 21;
const COUNTRY_22 = 22;
const COUNTRY_23 = 23;
const COUNTRY_24 = 24;
const COUNTRY_25 = 25;
const COUNTRY_26 = 26;
const COUNTRY_27 = 27;
const COUNTRY_28 = 28;
const COUNTRY_29 = 29;
const COUNTRY_30 = 30;
const COUNTRY_31 = 31;
static $country = array(
self::COUNTRY_32 => '巴西',
self::COUNTRY_1 => '喀麦隆',
self::COUNTRY_2 => '墨西哥',
self::COUNTRY_3 => '克罗地亚',
self::COUNTRY_4 => '西班牙',
self::COUNTRY_5 => '智利',
self::COUNTRY_6 => '澳大利亚',
self::COUNTRY_7 => '荷兰',
self::COUNTRY_8 => '哥伦比亚',
self::COUNTRY_9 => '科特迪瓦',
self::COUNTRY_10 => '日本',
self::COUNTRY_11 => '希腊',
self::COUNTRY_12 => '乌拉圭',
self::COUNTRY_13 => '英格兰',
self::COUNTRY_14 => '哥斯达黎加',
self::COUNTRY_15 => '意大利',
self::COUNTRY_16 => '瑞士',
self::COUNTRY_17 => '厄瓜多尔',
self::COUNTRY_18 => '洪都拉斯',
self::COUNTRY_19 => '法国',
self::COUNTRY_20 => '阿根廷',
self::COUNTRY_21 => '尼日利亚',
self::COUNTRY_22 => '伊朗',
self::COUNTRY_23 => '波黑',
self::COUNTRY_24 => '德国',
self::COUNTRY_25 => '加纳',
self::COUNTRY_26 => '美国',
self::COUNTRY_27 => '葡萄牙',
self::COUNTRY_28 => '比利时',
self::COUNTRY_29 => '阿尔及利亚',
self::COUNTRY_30 => '韩国',
self::COUNTRY_31 => '俄罗斯',
);

}
