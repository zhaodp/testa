<?php

Yii::import('application.models.activity.BActivity');

class BActivityBonus extends BActivity {

    const BONUS_USER_LIMITED_NONE = 100;        //没有使用人数限制

    public $bonusRule;       //优惠券使用规则. @link $this->getBonusRules()
    private $_extraIni;     //扩展信息字段

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 生成扩展信息字段参数
     * @param type $params
     * @return type
     * @throws CHttpException
     */
    public function buildExtraIni($params) {
        $res = array(
            '_type' => 'bonus',
            '_info' => '优惠券相关的活动',
            'bonusRule' => $this->bonusRule,
        );
        if ($this->bonusRule == self::BONUS_USER_LIMITED_NONE) {
            if (!isset($params['bonusSn'])) {
                throw new CHttpException(401, '缺少参数 优惠券');
            }
            $res = array_merge($res, $params);
        }

        return $res;
    }
    
    public function afterFind() {
        parent::afterFind();
        $extra = $this->getExtraIni();
        $this->bonusSn = $extra['bonusSn'];
        $this->bonusWorkTime = $extra['bonusWorkTime'];
    }

    /**
     * 优惠券使用规则
     * @return string
     */
    public function getBonusRules() {
        $res = array(
            self::BONUS_USER_LIMITED_NONE => '共用一个优惠券',
        );

        return $res;
    }

    /**
     * 获取此活动的优惠券号
     * @return type
     * @throws CHttpException
     */
    public function getBonusSn() {
        $extr = $this->getExtraIni();
        if (!$extr || !isset($extr['bonusSn'])) {
            throw new CHttpException(404, '缺少配置信息');
        }
        $bonusSn = $extr['bonusSn'];
        return $bonusSn;
    }

}
