<?php

class CityConfigController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column1';

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        //$model = $this->loadModel($id);
        $city_cast = Dict::items("city_cast");
        $fee_id_arr = Dict::items("city_fee");
        //print_r($city_cast);print_r($fee_id_arr);die;
        $this->render('view', array(
            'model' => $this->loadModel($id),
            'city_cast' => $city_cast,
            'fee_id_arr' => $fee_id_arr,
        ));
    }

    public function actionGetOpenCityInChina()
    {
        /* 先把缓存去掉，因为缓存更新的机制还没有确定清楚
        $open_city = Yii::app()->cache->get('edaijiaOpenCityInChina');
        if (!empty($open_city)) {
            echo json_encode(array('code' => 0, 'data' => json_decode($open_city, true)));
            return;
        }
        */
    
        $sql = 'select A.city_id, A.city_name, B.id as province_id, B.name as province_name,
            C.id as region_id, C.name as region_name from t_city_config as A inner join 
            t_city_province as B on (A.province_id = B.id) inner join t_region as C on (C.id = B.region_id) where A.status = 1 order by C.id, B.id';

        $operation_cities = Yii::app()->db_readonly->createcommand($sql)->queryAll();
        if (!empty($operation_cities)) {
            $output = array();
            $region = $province = $last_city = null;
            foreach ($operation_cities as $city) {
                if (empty($region) || $region['region_id'] != $city['region_id']) {
                    $region = array(
                        'region_id' => $city['region_id'],
                        'region_name' => $city['region_name'],
                        'province' => array()
                    );
                    $output[] = $region;
                    $last_region_index = count($output) - 1;
                } 

                if (empty($province) || $province['province_id'] != $city['province_id']) {
                    $province = array(
                        'province_id' => $city['province_id'],
                        'province_name' => $city['province_name'],
                        'city' => array()
                    );
                    $output[$last_region_index]['province'][] = $province;
                    $last_province_index = count($output[$last_region_index]['province']) - 1;
                }

                if (empty($last_city) || $city['city_id'] != $last_city['city_id']) {
                    $last_city = array('city_id' => $city['city_id'], 'city_name' => $city['city_name']);
                    $output[$last_region_index]['province'][$last_province_index]['city'][] = $last_city;
                }
            }

            Yii::app()->cache->set('edaijiaOpenCityInChina', json_encode($output), 24*60*60);
            echo json_encode(array('code' => 0, 'data' => $output));
            return;
        }
        
        echo json_encode(array('code' => 1, 'data' => '', 'message' => 'failed'));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new CityConfig;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['CityConfig'])) {
            $model->attributes = $_POST['CityConfig'];
            if ($model->save()){
                if($_GET['back_url']) {
                    $this->redirect($_GET['back_url']);
                }else
                $this->redirect(array('admin'));
            }

        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $model = $this->loadModel($id);
        if (isset($_POST['CityConfig'])) {
            $model->attributes = $_POST['CityConfig'];
            if ($model->save()){
                $this->redirect(array('admin'));
            }

        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        RCityList::model()->load();
        $model = new CityConfig();


        $res = RCityList::model()->getcityGroupByLevel();
        print_r($res);die;
        //echo time();die;
        $function_name = RCityList::model()->getCityByID(1,'cast_id');
        echo $function_name;

        $res = Dict::item('city_prefix', '1');
        print_r($res);die;
        $res = $model->calculatorFee(1,34,'1400659399',2);
        PRINT_R($res);die;
        //$res = RCityList::model()->deleteAll();die;
        //var_dump($res);die;
        //$name = '北京';
        //$res = RCityList::model()->getAllCity($name);
        //print_r($res);
//die;
        $city_arr = $_POST;
        $fun = '';
        if ($city_arr) {
            $fun = $city_arr['city_key'];
        }
        $fun_value = '';
        switch ($fun) {
            case 'getDriverCityLt100':
                $fun_value = RCityList::model()->getDriverCityLt(100);
                break;
            case 'getOpenCityList':
                $fun_value = RCityList::model()->getOpenCityList();
                break;
            case 'getDriverCityLt200':
                $fun_value = RCityList::model()->getDriverCityLt(200);
                break;
            case 'getCityByPrifix':
                if (!empty($city_arr['city_params'])) {
                    $param = $city_arr['city_params'];
                } else {
                    $param = 'bj';
                }
                $fun_value = RCityList::model()->getCityByPrifix($param,'screen_money');
                break;
            case 'getCityByID':
                if (!empty($city_arr['city_params'])) {
                    $param = $city_arr['city_params'];
                } else {
                    $param = '1';
                }
                $fun_value = RCityList::model()->getCityByID($param);
                break;
            case 'getCityFeeEq19':
                $fun_value = RCityList::model()->getCityFeeEq(19);
                break;
            case 'getFee':
                if (!empty($city_arr['city_params'])) {
                    $param = $city_arr['city_params'];
                } else {
                    $param = '1';
                }
                $fun_value = RCityList::model()->getFee($param);
                break;
        }

        $this->render('index', array(
            'model' => $model,
            'fun_value' => $fun_value,
            'city_arr' => $city_arr
        ));
    }

    /**
     * Lists all models.
     */
    public function actionAjax()
    {
        $id = $_GET['id'];
        $fee_str = CityConfig::model()->getfeeall($id);
        echo $fee_str;
    }

    public function actionAjaxCompleteCity(){
        $city_id = isset($_GET['city_id']) ? $_GET['city_id'] :'';
        if(!$city_id) { echo '';die;}

        $city_name = Dict::item('city',$city_id);
        $city_prefix = Dict::items("city_prefix");
        $bonus_city = Dict::items("bonus_city");
        $prefix = isset($city_prefix[$city_id]) ? $city_prefix[$city_id] : '';
        $city_bonus_no = array_search($prefix, $bonus_city);
        //$city_bonus_no = Dict::item('bonus_city',$city_b);
        $city_prefix = Dict::item('city_prefix',$city_id);
        $arr = array('city_name'=>$city_name,'city_bonus_no'=>$city_bonus_no,'city_prefix'=>$city_prefix);

        echo json_encode($arr);
    }



    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new CityConfig('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['CityConfig']))
            $model->attributes = $_GET['CityConfig'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return CityConfig the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = CityConfig::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CityConfig $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'city-config-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
