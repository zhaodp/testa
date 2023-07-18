<?php
/**
 * 日程安排
 * actionApi是唯一对外的接口
 * 定义一个api时，需要传递一个初始化的response，最后再返回这个response
 * @author liuxiaobo
 */
class AdminEventController extends Controller
{
    public $api_header;
    public $api_request;
    public $api_action;
    public $api_method;
    public $api_params;
    public $api_jsonp_callback;
    public $api_status = 's';
    public $api_count;
    
    public function actionIndex(){
        $this->render('index');
    }

    /**
     * 获取/修改 错误信息
     * @param <int> $code
     * @param <string> $message
     * @return \stdClass
     */
    public function getError($code = null, $message = ''){
        $error = new stdClass;
        $error->code = $code;
        $error->message = $message;
        
        return $error;
    }

    public function getApiHeader(){
        $header = new stdClass;
        $header->local = $this->api_header['local'];

        return $header;
    }
    /**
     * 解析参数
     * @throws CHttpException
     */
    public function parseUrl(){
        if(!isset($_GET['data'])){
            echo '缺少参数';
            Yii::app()->end();
        }
        $data = json_decode($_GET['data'],true);
        $this->api_header = $data['header'];
        $this->api_request = $data['request'];
        $this->api_params = $this->api_request[0]['params'];
        $this->api_action = $this->api_request[0]['action'];
        $this->api_method = $this->api_request[0]['method'];
        $this->api_count = $this->api_request[0]['count'];
        $this->api_jsonp_callback = isset($_GET['callback']) ? $_GET['callback'] : '';

    }
    
    public function beforeAction($action) {
        //return parent::beforeAction($action);

        // 验证访问api是否登录 -- start
		$controller_id = $this->getUniqueId();
        $loginUrl = $this->createAbsoluteUrl('site/login', array ('controller'=>$controller_id));
        
        if($this->action->id == 'api'){
            $this->parseUrl();
            if (Yii::app()->user->isGuest) {
                $this->api_status = 'error';
                $result = new stdClass;
                $header = $this->getApiHeader();
                $response = $this->initResponse();
                $response->error = $this->getError(400, $loginUrl);

                $result->header = $header;
                $result->response = array($response);
                if (isset($_GET['callback'])) {

                    echo $this->api_jsonp_callback ? $this->api_jsonp_callback . '(' . json_encode($result) . ');' : json_encode($result);
                }
                return FALSE;
            }
        }
        // 验证访问api是否登录 -- end

        return parent::beforeAction($action);
    }

    /**
     * 解析执行哪个api
     */
    public function parseApi($response = null){
        $a = ucfirst($this->api_action);
        $actionId = 'api'.$a;
        if(!method_exists($this, $actionId)){
            throw new CHttpException('403','请求的api没有找到'.$a);
        }
        return $this->$actionId($response);
    }

    /**
     * api入口
     */
    public function actionApi(){
        $this->layout = FALSE;
        $result = new stdClass;
        $header = $this->getApiHeader();
        $initResponse = $this->initResponse();
        $response = $this->parseApi($initResponse);

        $result->header = $header;
        $result->response = array($response);
        
        echo $this->api_jsonp_callback ? $this->api_jsonp_callback.'('.json_encode($result).');' : json_encode($result);
    }
    
    /**
     * 初始化response
     * @return \stdClass
     */
    public function initResponse(){
        $result = new stdClass();
        $result->action = $this->api_action;
        $result->method = $this->api_method;
        $result->count = $this->api_count;
        $result->status = $this->api_status;
        $result->error = $this->getError();

        return $result;
    }

    /**
     * 删除待办事项
     * @param <obj> $result
     * @return <obj>
     */
    public function apiDelete($result = null){
        $id = isset($this->api_params['id']) ? $this->api_params['id'] : '';
        
        $model = $this->loadModel($id);
        if(!empty($model) && $model->author == Yii::app()->user->user_id){
            $model->delete();
        }
        return $result;
    }
    
    /**
     * 修改待办事项
     * @param <obj> $result
     * @return <obj>
     */
    public function apiUpdate($result = null){
        return $this->apiCreate($result);
    }

    /**
     * 创建 待办事项
     * @param <obj> $result
     * @return <obj>
     */
    public function apiCreate($result = null){
        if(isset($this->api_params['id'])){
            $model = $this->loadModel($this->api_params['id']);
        }else{
            $model = new AdminEvent();
        }
        isset($this->api_params['title']) && $model->title = $this->api_params['title'];
        isset($this->api_params['begin_date']) && $model->begin = date('Y-m-d H:i:s', strtotime($this->api_params['begin_date']));
        isset($this->api_params['end_date']) && $model->end = date('Y-m-d H:i:s', strtotime($this->api_params['end_date'])+86400-1);
        isset($this->api_params['explain']) && $model->description = $this->api_params['explain'];
        isset($this->api_params['status']) && $model->status = $this->api_params['status'];
        $saveOk = $model->save() ? true : false;
        if(!$saveOk){
            $this->api_status = 'error';
            $error_code = 1;
            $error_message = $this->getModelErrorStr($model);
        }
        $result->error = $this->getError(isset($error_code)?$error_code:null, isset($error_message)?$error_message:'');
        $createResult = new stdClass;
        $createResult->title = $saveOk ? "保存成功" : '保存失败';
        $result->result = $createResult;
        return array($result);
    }

    /**
     * 查询 待办事项
     * @param <obj> $result
     * @return <obj>
     */
    public function apiQuery($result = null){
        $queryResult = new stdClass;
        $queryResult->title = "";
        if(!isset($this->api_params['begin_date']) || !isset($this->api_params['begin_date'])){
            $this->api_status = 'error';
            $result->error = $this->getError(2, '缺少参数');
            return $result;
        }
        $btime = strtotime($this->api_params['begin_date']);
        $etime = strtotime($this->api_params['end_date']);
        if($etime - $btime > (86400*31)){
            $this->api_status = 'error';
            $result->error = $this->getError(3, '时间间隔最大一个月');
            return $result;
        }
        $models = AdminEvent::model()->getMyEventBetweenTimes($btime - 86400*14, $etime + 86400*14);     //页面中会显示出上个月和下个月的部分时间
        $listResult = array();
        foreach($models as $model){
            $list = new stdClass;
            $list->id = $model->id;
            $list->title = CHtml::encode($model->title);
            $list->start = $model->begin;
            $list->end = $model->end;
            $list->status = $model->status;
            $list->klass = $model->status == AdminEvent::STATUS_FINISHED ? 'public-out' : AdminEvent::model()->getRandKlass();
            $listResult[] = $list;
        }
        $queryResult->list = $listResult;
        $result->result = $queryResult;
        return $result;
    }

    /**
     * 列出 待办事项 列表
     * @param <obj> $result
     * @return <obj>
     */
    public function apiList($result = null){
        $content = array();
        $listResult = new stdClass;
        if(!isset($this->api_params['begin_date']) || !isset($this->api_params['begin_date'])){
            $this->api_status = 'error';
            $result->error = $this->getError(2, '缺少参数');
            return $result;
        }
        $btime = strtotime($this->api_params['begin_date']);
        $etime = strtotime($this->api_params['end_date']);
        if($etime - $btime > (86400*31)){
            $this->api_status = 'error';
            $result->error = $this->getError(3, '时间间隔最大一个月');
            return $result;
        }
        $dayEvents = AdminEvent::model()->listEventByDays($btime, $etime);
        $i = 0;
        foreach($dayEvents as $day => $events){
            $i++;
            if($i > 31){
                break;
            }
            $listArr = array();
            $dayContent = new stdClass();
            $a = mb_substr('日一二三四五六', date('w', strtotime($day)), 1, 'utf-8');
            $dayContent->name = CHtml::encode($day.' 星期'.$a);
            foreach($events as $event){
                $list = new stdClass;
                $list->id = $event->id;
                $list->title = CHtml::encode($event->title);
                $begin = $day.' 00:00:00';
                $end = $day.' 23:59:59';
                $btime = $event->begin < $begin ? $begin : $event->begin;
                $etime = $event->end > $end ? $end : $event->end;
                $list->date = substr($btime,11,5).'-'.substr($etime,11,5);
                $listArr[] = $list;
            }
            $dayContent->list = $listArr;
            $content[] = $dayContent;
        }
        $listResult->content = $content;
        $result->result = $listResult;
        return $result;
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=AdminEvent::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    /**
     * 获取model的错误信息字符串
     * @param <obj> $model
     * @return <string>
     */
    public function getModelErrorStr($model){
        $e = $model->errors;
        $er = '';
        foreach($e as $eitem){
            $er .= $eitem[0];
        }

        return $er;
    }
}
