<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 2015/3/25
 * Time: 14:29
 */
Yii::import('application.models.pomo.*');
class DianpingCommand extends LoggerExtCommand{
    /**
     * 导入点评点评信息
     * php yiic.php Dianping Import --pid=101
     */
    public function actionImport($pid){

        try {
            $model_list=new ProcessList();
            $model=$model_list->getTail($pid);
            if(empty($model)){
                echo '没有记录:'.$pid;
                return;
            }

            $result= $this->get($model);

            echo $result['status'];

        } catch (HttpException $ex) {
            echo $ex;
        }
    }


    private function  get($model){
        $url='http://api.dianping.com/v1/business/find_businesses';
        $params=array();
        $appkey='325212731';
        $secret='a4e2c40ccd524599bb5f4872917c9f79';
        $params['limit']=1;
        $params['page']=$model->tail+1;
        $params['city']=$model->name;
        $params['category']=$model->category;
        ksort($params);

        $key_values=$appkey;
        foreach($params as $key=>$value){
            $key_values=$key_values.$key.$value;
        }
        $key_values=$key_values.$secret;

        $params['sign']=sha1($key_values);
        $params['appkey']=$appkey;

        $params['city']=urlencode($model->name);
        $params['category']=urlencode($model->category);

        return Common::get($url,$params);
    }
}