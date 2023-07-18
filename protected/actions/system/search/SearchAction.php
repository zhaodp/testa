<?php

/*
 * 全局搜索入口
 */

class SearchAction extends CAction {
    /**
     * 搜索
     * @param <string> $q
     */
    public function run($q = '') {
        $q = trim($q);
        if (empty($q)) {
            $this->controller->renderText('请输入搜索内容！');
            Yii::app()->end();
        }

        $this->controller->q = $q;
        //获取搜索内容所匹配到的类型
        $type = $this->getType($q);
        //特定类型跳转页面
        $this->toRedirect($type, $q);

        if (!$type) {
            $this->controller->renderText('sorry，没有匹配到对应的信息！');
            Yii::app()->end();
        }
        //视图命名方法：search_' . $type (例如：search_isOrder)
        $view = 'search/search_' . $type;
        $this->controller->render($view, array('q'=>$this->controller->q));
    }

    /**
     * 特定类型的搜索，跳转到已有的页面
     * @param <string> $type    类型
     * @param <string> $q
     */
    public function toRedirect($type, $q) {
        if ($type == 'isDriver' && $this->isPhone($q)) {
            $driver = Driver::model()->findByAttributes(array('phone'=>$q));
            if ($driver) {
                $q = $driver->user;
            }
        }
        if ($redirectUrl = $this->getRedirect($type, $q)) {
            $this->controller->redirect($redirectUrl);
            Yii::app()->end();
        }
    }

    /**
     * 获取跳转页面的配置信息
     * @param <string> $q   搜索内容
     * @return <string>     返回要跳转的URL
     */
    public function getRedirects($q) {
        return array(
            'isDriver' => Yii::app()->createUrl('driver/archives', array('id'=>$q)),
            'isOrder' => Yii::app()->createUrl('order/view', array('id'=>$q)),
        );
    }

    /**
     * 根据类型获取跳转页面URL
     * @param <string> $type    类型
     * @param <string> $q       搜索内容
     * @return <mixed>          返回对应的URL，如果没有对应的URL则返回false
     */
    public function getRedirect($type,$q) {
        $redirects = $this->getRedirects($q);
        if (isset($redirects[$type])) {
            return $redirects[$type];
        }
        return FALSE;
    }

    /**
     * 根据搜索内容获取匹配到的类型
     * @param <string> $q   搜索内容
     * @return <string>
     */
    public function getType($q) {
        $type = '';
        $types = $this->getTypes();
        if (is_array($types)) {
            foreach ($types as $item) {
                if ($this->$item($q)) {
                    $type = $item;
                    break;
                }
            }
        }
        return $type;
    }

    /**
     * 返回可搜索的类型，按照权重排序
     * @return <array>
     */
    public function getTypes() {
        $arr = array(
            '1' => 'isDriver',
            '2' => 'isCustomer',
            '3' => 'isOrder',
        );
        ksort($arr, SORT_NUMERIC);
        return $arr;
    }

    /**
     * 判断是否是手机号码
     * @param <string> $q
     * @return <bool>
     */
    public function isPhone($q) {
        $reg = '/^1\d{10}$/';
        $match = preg_match($reg, $q);
        return $match ? TRUE : FALSE;
    }

    /**
     * 是否是司机工号
     * @param <string> $q
     * @return <bool>
     */
    public function isDriverNum($q) {
        $reg = '/^[a-zA-Z]{2}\d{4,5}$/';
        $match = preg_match($reg, $q);
        return $match ? TRUE : FALSE;
    }

    /**
     * 是否是司机
     * @param <string> $q
     * @return <bool>
     */
    public function isDriver($q) {
        $isPhone = $this->isPhone($q);
        if ($isPhone) {
            $driver = Driver::model()->exists('phone = :phone', array(':phone' => $q));
            if ($driver) {
                return TRUE;
            }
        } else if ($this->isDriverNum($q)) {
            $driver = Driver::model()->exists('user = :user', array(':user' => $q));
            if ($driver) {
                return TRUE;
            }
        } else {
            return FALSE;
        }
        return FALSE;
    }

    /**
     * 是否是客户
     * @param <string> $q
     * @return <bool>
     */
    public function isCustomer($q) {
        $isPhone = $this->isPhone($q);
        $isDriver = $this->isDriver($q);
        if ($isPhone && !$isDriver) {
            $customerMain = CustomerMain::model()->exists('phone = :phone', array(':phone' => $q));
            if ($customerMain) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 是否是订单
     * @param <string> $q
     * @return <bool>
     */
    public function isOrder($q) {
        $isOrderNum = $this->isOrderNum($q);
        $isOrderId = $this->isOrderId($q);
        if ($isOrderId) {
            $order = Order::model()->exists('order_id = :order_id', array(':order_id' => $q));
            if ($order) {
                return TRUE;
            }
        }
        if ($isOrderNum) {
            $order = Order::model()->exists('order_number = :order_number', array(':order_number' => $q));
            if ($order) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 是否是订单号
     * @param <string> $q
     * @return <bool>
     */
    public function isOrderNum($q) {
        $reg = '/^[a-zA-Z]{2}\d{14,15}$/';
        $match = preg_match($reg, $q);
        return $match ? TRUE : FALSE;
    }

    /**
     * 是否是订单流水号
     * @param <string> $q
     * @return <bool>
     */
    public function isOrderId($q) {
        $reg = '/^\d{1,}$/';
        $match = preg_match($reg, $q);
        return $match ? TRUE : FALSE;
    }

}
