<?php
/**
 * 订单分析
 * @author liuxiaobo
 */
class TimeOrderAction extends CAction {

    public function run() {
        $model = new BOrderTrend('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_POST['BOrderTrend'])) {
            $model->attributes = $_POST['BOrderTrend'];
        }
        if($model->day === NULL){
            $model->day = date('Ymd', time());
        }
        if($model->city_id === NULL){
            $model->city_id = 0;
        }
        $this->controller->render('order/time_order', array(
            'model' => $model,
        ));
    }

}
