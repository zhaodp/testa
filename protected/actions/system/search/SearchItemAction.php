<?php

/*
 * 特定搜索入口
 */

class SearchItemAction extends CAction {
    /**
     * 搜索
     * @param <string> $q
     * @param <string> $item 搜索类型
     * @param <sting> $goto  是否跳转，1，跳转； 0，不跳转
     */
    public function run($q = '', $item = '', $goto = 1) {
        $this->controller->layout = 'main_no_nav';
        $widgets = $this->getWidgets($q);
        if (empty($q) || empty($item) || !isset($widgets[$item])) {
            $this->controller->renderText('');
            return;
        }
        //处理跳转
        if ($goto == 1) {
            $this->controller->layout = FALSE;
            $urlParams = $_GET;
            $urlParams['goto'] = 0;
            $gotoUrl = Yii::app()->createUrl($this->controller->route, $urlParams);
            $wait = isset($widgets[$item]['wait']) ? $widgets[$item]['wait'] : 0;
            $this->controller->renderText('<script>'.($wait ? 'setTimeout(function(){' : '').'location.href="'.$gotoUrl.'"'.($wait ? '},'.$wait.')' : '').'</script>');
            return;
        }
        
        $view = 'search/search_item';
        $this->controller->render($view, $widgets[$item]);
    }
    
    /**
     * 特定条件搜索时，参数对应的配置信息（item是widget的位置，params是调用widget时传递的参数, wait是等待时间）
     * @param <string> $q
     * @return <array>
     */
    public function getWidgets($q = ''){
        return array(
//            'OrderPosition'=>array(
//                'item'=>'application.widgets.position.info.PositionWidget',
//                'params'=>array('orderId'=>$q),
//            ),
            'OrderComplainInfo'=>array(
                'item'=>'application.widgets.complain.ComplainInfoWidget',
                'params'=>array('orderId'=>$q),
            ),
            'OrderCommentSms'=>array(
                'item'=>'application.widgets.commentSms.CommentSmsWidget',
                'params'=>array('orderId'=>$q),
            ),
            'OrderInfo'=>array(
                'item'=>'application.widgets.order.OrderInfoWidget',
                'params'=>array('orderId'=>$q),
            ),
            'customerInfo'=>array(
                'item'=>'application.widgets.customer.CustomerInfoWidget',
                'params'=>array('phone'=>$q),
            ),
            'customerOrder'=>array(
                'item'=>'application.widgets.order.OrderInfoWidget',
                'params'=>array('phone'=>$q),
                'wait'=>300,
            ),
            'customerComplainInfo'=>array(
                'item'=>'application.widgets.complain.ComplainInfoWidget',
                'params'=>array('phone'=>$q),
            ),
            'customerCommentSms'=>array(
                'item'=>'application.widgets.commentSms.CommentSmsWidget',
                'params'=>array('phone'=>$q),
            ),
            'customerBonusCode'=>array(
                'item'=>'application.widgets.bonusCode.BonusCodeWidget',
                'params'=>array('phone'=>$q),
            ),
        );
    }

}
