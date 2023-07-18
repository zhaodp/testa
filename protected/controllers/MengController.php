<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-16
 * Time: 上午10:06
 * auther mengtianxue
 */
class MengController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * dict 显示
     * @auther mengtianxue
     */
    public function actionDict()
    {
        $dict_name = empty($_GET['dictname']) ? '' : $_GET['dictname'];
        $dict = Dict::items($dict_name);
        print_r($dict);
    }

    public function actionGetCache()
    {
        $key = isset($_GET['id']) ? $_GET['id'] : 0;
        $type = isset($_GET['type']) ? $_GET['type'] : 'get';
        switch ($type) {
            case 'set':
                $cache = Yii::app()->cache->set($key, array(), 10);
                break;
            case 'delete':
                $cache = Yii::app()->cache->delete($key);
                break;
            default:
                $cache = Yii::app()->cache->get($key);
        }

        print_r($cache);
    }


    public function ActionSQLSelect()
    {
        $data = (object)array();
        if (isset($_GET['sql'])) {
            $params = $_GET['sql'];
            $table_name = isset($params['table']) ? $params['table'] : '';
            $select = isset($params['select']) ? $params['select'] : '';
            $where = isset($params['where']) ? $params['where'] : '';
            $criteria = new CDbCriteria;
            if ($select) {
                $criteria->select = "*";
            } else {
                $criteria->select = $select;
            }
            foreach ($where as $k => $v) {
                $criteria->compare($k, $v);
            }
            $data = new CActiveDataProvider($table_name, array(
                'criteria' => $criteria,
            ));
        }

        $this->render('sql_select',
            array('data' => $data)
        );
    }

    /**
     *
     * @auther mengtianxue
     * php yiic.php mtx BonusExport
     */
    public function ActionBonusExport()
    {
        if (!isset($_GET['bonus_id'])) {
            return false;
        }
        $bonus_id = $_GET['bonus_id'];

        $bonus_arr = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from("t_customer_bonus")
            ->where('bonus_type_id = :bonus_id', array(':bonus_id' => $bonus_id))
            ->order("used desc")
            ->queryAll();
        echo "司机工号,";
        echo "司机电话,";
        echo "客户电话,";
        echo "订单id,";
        echo "城市id,";
        echo "公里数,";
        echo "余额,";
        echo "绑定时间,";
        echo "使用时间\n";

        foreach ($bonus_arr as $k => $bonus) {

            if (empty($order_id)) {
                $order_id = "未使用";
                $distance = "";
                $city_id = '';
                $balance = '';
                echo ' , ';
                echo ' , ';

            } else {
                $order_id = $bonus['order_id'];
                $order = Order::model()->getOrdersById($order_id);
                $distance = $order['distance'];
                $city_id = Dict::item('city', $order['city_id']);
                $balance = $order['income'];
                echo $order['driver_id'] . ",";
                echo $order['driver_phone'] . ",";
            }
            echo $bonus['customer_phone'] . ",";
            echo $order_id . ",";
            echo $city_id . ",";
            echo $distance . ",";
            echo $balance . ",";
            echo (empty($bonus['created']) ? '' : date('Y-m-d H:i:s', $bonus['created'])) . ",";
            echo (empty($bonus['used']) ? '' : date('Y-m-d H:i:s', $bonus['used'])) . " \n";
        }
    }


    public function ActionCeshi()
    {
        $fun = $_GET['fun'];
        $params = $_GET['params'];
        $this->$fun($params);
    }

    public function Bonus($params = array()){
        $num = $params['num'];
        $len = $params['len'];
        $max = BonusLibrary::model()->getMaxNumber($num, $len);
        echo $max;
    }

    public function DriverRank($params = array())
    {
        foreach ($params as $k => $v) {
            $k = $v;
        }
    }


    /**
     * @auther mengtianxue
     * 删除订单历史
     */
    public function ActionDelRedis(){
        $phone = isset($_GET['phone']) ? trim($_GET['phone']) : '18511883962';
        $back = ROrderHistory::model()->delOrderHistory($phone);
        echo $back;
    }


}