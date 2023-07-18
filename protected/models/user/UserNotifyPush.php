<?php
/**
 * Created by PhpStorm.
 * User: liufugang
 * Date: 2015/4/9
 * Time: 14:23
 */
class UserNotifyPush {
    /*
     * c.nearby 调用
     */
    public function nearByPush($params){
        EdjLog::info('UserNotifyPush nearByPush,params:'.json_encode($params));
        $customer_info = self::getCustomerInfoByToken($params);
        if(!$customer_info){
            return false;
        }
        $phone = $customer_info->phone;
        $userNotifyId = self::isUserDoNotify($customer_info,UserNotify::$NOTIFY_TYPE_MSG);
        if($userNotifyId){
            $phoneUserNotifyVal = UserNotifyRedis::model()->getUserNotify($phone,$userNotifyId);
            if($phoneUserNotifyVal){
                EdjLog::info("UserNotifyPush nearByPush,redis exits.phone:".$phone.",userNotifyId:".$userNotifyId.",val:".$phoneUserNotifyVal);
                return false;
            }
            $msg = self::initMsg($userNotifyId);
            if($msg){
                EdjLog::info("UserNotifyPush nearByPush:phone:".$phone.",UserNotifyMsg:".json_encode($msg));
                ClientPush::model()->userNotifyPush($phone,$msg);
                UserNotifyRedis::model()->setUserNotify($phone,$userNotifyId);
                EdjLog::info("UserNotifyPush nearByPush,redis set.phone:".$phone.",userNotifyId:".$userNotifyId);
            }else{
                EdjLog::info("UserNotifyPush nearByPush:UserNotifyMsg is empty,phone:".$phone);
            }
        }else{
            EdjLog::info("UserNotifyPush nearByPush:userNotifyId is empty,phone:".$phone);
        }
    }


    public static function immediatelyPush($userNotifyMsg){
        if(!$userNotifyMsg||!isset($userNotifyMsg->t_user_notify_id)){
            EdjLog::info("UserNotifyPush immediatelyPush userNotifyMsg is empty");
            return false;
        }
        $params = array();
        $params['id']=isset($userNotifyMsg->Id)?$userNotifyMsg->Id:"";
        $params['word']=isset($userNotifyMsg->word)?$userNotifyMsg->word:"";
        $params['word']=isset($userNotifyMsg->word)?$userNotifyMsg->word:"";
        $params['title']=isset($userNotifyMsg->title)?$userNotifyMsg->title:"";
        $params['content']=isset($userNotifyMsg->content)?$userNotifyMsg->content:"";
        $params['button_url']=isset($userNotifyMsg->button_url)?$userNotifyMsg->button_url:"";
        $params['client_page']=isset($userNotifyMsg->client_page)?$userNotifyMsg->client_page:"";
        $params['button_text']=isset($userNotifyMsg->button_text)?$userNotifyMsg->button_text:"";
        $params['t_user_notify_id']=$userNotifyMsg->t_user_notify_id;
        EdjLog::info ( 'UserNotifyPush immediatelyPush - add task to queue, params are ' . json_encode ( $params ) );
        $task = array (
            'method' => 'immediatelyPush',
            'params' => $params
        );
        Queue::model ()->putin ( $task, 'imm_user_push' );
    }

    /*
     * 页面调用立即发送
     */
    public static function doImmediatelyPush($params){
        //发送消息体内容
        $smsContent = array();
        $msgId=isset($params["Id"])?$params["Id"]:"";
        $smsContent['message']=isset($params["word"])?$params["word"]:"";
        $smsContent['title']=isset($params["title"])?$params["title"]:"";
        $smsContent['content']=isset($params["content"])?$params["content"]:"";
        $smsContent['url']=isset($params["button_url"])?$params["button_url"]:"";
        $smsContent['show_page']=isset($params["client_page"])?$params["client_page"]:"";
        $smsContent['btn_name']=isset($params["button_text"])?$params["button_text"]:"";
        //过来用户条件bean
        $userNotifyId = isset($params["t_user_notify_id"])?$params["t_user_notify_id"]:"";
        EdjLog::info("UserNotifyPush immediatelyPush, userNotifyId:$userNotifyId, msgId:$msgId, sms comtent:".json_encode($smsContent));
        $userNotify = UserNotify::model()->findByPk($userNotifyId);
        if(!$userNotify){
            EdjLog::info("UserNotifyPush immediatelyPush userNotify empty");
            return false;
        }
        $client_version_lowest = isset($userNotify->client_version_lowest)?$userNotify->client_version_lowest:"";
        $city_id = isset($userNotify->city_id)?$userNotify->city_id:"";
        $client_os_type = isset($userNotify->client_os_type)?$userNotify->client_os_type:"-1";
        $user_type = isset($userNotify->user_type)?$userNotify->user_type:"";
        $client_version_lowest = isset($userNotify->client_version_lowest)?$userNotify->client_version_lowest:"";
        $param = array("client_version_lowest"=>$client_version_lowest,"city_id"=>$city_id,"client_os_type"=>$client_os_type,"user_type"=>$user_type);
        EdjLog::info("UserNotifyPush immediatelyPush user notify condition:".json_encode($param));
        $criteria=new CDbCriteria;
        if(isset($param["client_version_lowest"])){
            $criteria->addCondition("(app_ver>=:client_version_lowest)"); //查询条件，即where id = 1
            $criteria->params[':client_version_lowest'] = $param['client_version_lowest'];
        }
        if (isset($param['city_id'])&&$param['city_id']!=-1) {
            $cityIds = explode(",",$param['city_id']);  
            $criteria->addInCondition('city_id', $cityIds);
            $criteria->addCondition("city_id != ''");
        }
        $onlyNewUser = 0;
        if (isset($param['user_type'])) {
            $uts = explode(",",$param['user_type']);
            $in = in_array(1,$uts);
            if($in){
                $onlyNewUser = 1;
            }
        }
        //$criteria->order='id asc';
        $totalCount=CustomerMain::model()->count($criteria);
        $pageSize = 1000;
        $pageCount = intval($totalCount/$pageSize);
        if($totalCount%$pageSize!=0){
            $pageCount++;
        }
        EdjLog::info("UserNotifyPush immediatelyPush totalCount:$totalCount,pageCount:$pageCount,pageSize:$pageSize");
        if($onlyNewUser){
            for ($i = 1; $i <=$pageCount ; $i++){
                $offset = ($i-1)*$pageSize;
                $criteria->limit = $pageSize; //取1条数据，如果小于0，则不作处理
                $criteria->offset = $offset; //两条合并起来，则表示 limit 10 offset 1,或者代表了。limit 1,10
                $beanList=CustomerMain::model()->findAll($criteria);
                EdjLog::info("UserNotifyPush immediatelyPush current page:$i,offset:$offset,pageCount:$pageCount");
                foreach($beanList as $bean){
                    $phone = $bean->phone;
                    $customer_order_report = CustomerOrderReport::model()->getCustomerOrder(array('phone' => $phone));
                    if($customer_order_report) {
                        // Old customer,老用户过滤掉
                        EdjLog::info("UserNotifyPush immediatelyPush,old user.phone:".$phone.",userNotifyId:".$userNotifyId.", msgId:".$msgId);
                        continue;
                    }
                    self::doSend($phone,$userNotifyId,$msgId,$smsContent,$client_os_type);
                }
            }
        }else{
            for ($i = 1; $i <=$pageCount ; $i++){
                $offset = ($i-1)*$pageSize;
                $criteria->limit = $pageSize; //取1条数据，如果小于0，则不作处理
                $criteria->offset = $offset; //两条合并起来，则表示 limit 10 offset 1,或者代表了。limit 1,10
                $beanList=CustomerMain::model()->findAll($criteria);
                EdjLog::info("UserNotifyPush immediatelyPush current page:$i,offset:$offset,pageCount:$pageCount");
                foreach($beanList as $bean){
                    $phone = $bean->phone;
                    self::doSend($phone,$userNotifyId,$msgId,$smsContent,$client_os_type);
                }
            }
        }
        EdjLog::info("UserNotifyPush immediatelyPush end");
    }


    public static function doSend($phone,$userNotifyId,$msgId,$smsContent,$client_os_type){
        $phoneUserNotifyVal = UserNotifyRedis::model()->getImmUserNotify($phone,$userNotifyId);
        if($phoneUserNotifyVal){
            EdjLog::info("UserNotifyPush immediatelyPush,redis exits.phone:".$phone.",userNotifyId:".$userNotifyId.",val:".$phoneUserNotifyVal);
            return false;
        }
        if($client_os_type!="-1"){
            $customer_client = CustomerClient::model()->getByPhoneAndLast($phone);
            if(empty($customer_client)){
                EdjLog::info("Match UserNotify,phone=".$phone.',customer_client is empty');
                return false;
            }
            $type = $customer_client['type'];
            if($type!=$client_os_type){
                EdjLog::info("UserNotifyPush immediatelyPush,phone:$phone,type:$type not match client_os_type:$client_os_type");
                return;
            }
        }
        ClientPush::model()->userNotifyPush($phone,$smsContent);
        UserNotifyRedis::model()->setImmUserNotify($phone,$userNotifyId);
        EdjLog::info("UserNotifyPush immediatelyPush,redis set.phone:".$phone.",userNotifyId:".$userNotifyId.", msgId:".$msgId);
    }


    /*
     * c.order.picture,主动调用
     */
    public function userNotifyApi($params){
        EdjLog::info('UserNotifyPush userNotifyApi,params:'.json_encode($params));
        $customer_info = self::getCustomerInfoByToken($params);
        if(!$customer_info){
            return false;
        }
        $orderId = isset($params['order_id'])?$params['order_id']:"";
        $userNotifyId = self::isUserDoNotify($customer_info,UserNotify::$NOTIFY_TYPE_BANNER);
        $phone = $customer_info->phone;
        if($userNotifyId){
            $orderInfo = self::getOrderInfo($userNotifyId,$orderId);
            EdjLog::info('UserNotifyPush userNotifyApi:phone:'.$phone.',orderInfo:'.json_encode($orderInfo));
            return $orderInfo;
        }else{
            EdjLog::info('UserNotifyPush userNotifyApi:userNotifyId is empty,phone:'.$phone);
        }
        return false;
    }

    private static function getCustomerInfoByToken($params){
        $token = isset($params['token'])?$params['token']:"";
        if(empty($token)){
            EdjLog::info('UserNotifyPush getCustomerInfoByToken:token is empty');
            return false;
        }
        $validate = CustomerToken::model()->validateToken($token);
        if (!$validate) {
            EdjLog::info('UserNotifyPush getCustomerInfoByToken:invalid token,'.$token);
            return false;
        }
        $phone = trim($validate['phone']);
        EdjLog::info('UserNotifyPush getCustomerInfoByToken,token:'.$token.',phone:'.$phone);
        $customer_info = CustomerMain::model()->getCustomer($phone);
        if(!$customer_info || empty($customer_info)){
            EdjLog::info('UserNotifyPush getCustomerInfoByToken:customer_info empty,token:'.$token.',phone:'.$phone);
            return false;
        }
        $id=$customer_info->id;
        if(!$id){
            EdjLog::info('UserNotifyPush getCustomerInfoByToken:customer_info id empty,token:'.$token.',phone:'.$phone);
            //$customer_info=CustomerMain::model()->forceGetCustomerInfo($phone);
            return false;
        }
        return $customer_info;
    }

    //判断用户是否通知
    //$notifyType = 1;//0 nearby,1.banaer
    private static function isUserDoNotify($customer_info,$notifyType){
        $phone = $customer_info->phone;
        $customer_client = CustomerClient::model()->getByPhoneAndLast($phone);
        if(empty($customer_client)){
            EdjLog::info("Match UserNotify,phone=".$phone.',customer_client is empty');
            return false;
        }
        $type = $customer_client['type'];
        $cityId = $customer_info->city_id;
        $appVer = $customer_info->app_ver;
        $isNewUser = 1; //0:所有，1，新用户
        //$notifyType = 1;//0 nearby,1.banaer
        $customer_order_report = CustomerOrderReport::model()->getCustomerOrder(array('phone' => $phone));
        if($customer_order_report) {
            // Old customer
            $isNewUser = 0;
        }
        $params = array("phone"=>$phone,"city_id"=>$cityId,"type"=>$type,"app_ver"=>$appVer,"is_new_user"=>$isNewUser,"notify_type"=>$notifyType);
        EdjLog::info("Match UserNotify,params:".json_encode($params));
        $userNotify = UserNotify::model()->getUserNotify($params);
        if($userNotify){
            EdjLog::info("Match UserNotify,".json_encode($params).",return id:".$userNotify->Id);
            return $userNotify->Id;
        }else{
            EdjLog::info("Match UserNotify,".json_encode($params).",return empty.");
        }
        return 0;
    }

    private static function initMsg($userNotifyId){
        //$params = array('message'=>"用户提醒",'title'=>"用户提醒Title",'content'=>"内容",'url'=>"www.edaijia.cn",'show_page'=>14);
        $userNotifyMsg = UserNotifyMsg::model()->itemsMsg($userNotifyId,UserNotify::$TRIGGER_CONDITION_NEARBY);
        if($userNotifyMsg){
            $params = array();
            $params['message']=isset($userNotifyMsg->word)?$userNotifyMsg->word:"";
            $params['title']=isset($userNotifyMsg->title)?$userNotifyMsg->title:"";
            $params['content']=isset($userNotifyMsg->content)?$userNotifyMsg->content:"";
            $params['url']=isset($userNotifyMsg->button_url)?$userNotifyMsg->button_url:"";
            $params['show_page']=isset($userNotifyMsg->client_page)?$userNotifyMsg->client_page:"";
            $params['btn_name']=isset($userNotifyMsg->button_text)?$userNotifyMsg->button_text:"";
            return $params;
        }else{
            return 0;
        }
    }

    /*
     * 针对API
     */
    private static function getOrderInfo($userNotifyId,$orderId){
        $status201 = array("status_code"=>"201","content"=>"","pic_url"=>"","act_url"=>"");
        $status301 = array("status_code"=>"301","content"=>"","pic_url"=>"","act_url"=>"");
        $status302 = array("status_code"=>"302","content"=>"","pic_url"=>"","act_url"=>"");
        $status303 = array("status_code"=>"303","content"=>"","pic_url"=>"","act_url"=>"");
        $status304 = array("status_code"=>"304","content"=>"","pic_url"=>"","act_url"=>"");
        $status500 = array("status_code"=>"500","content"=>"","pic_url"=>"","act_url"=>"");
        $words = UserNotifyBanner::model()->itemsWord($userNotifyId);
        $banners = UserNotifyBanner::model()->itemsBanner($userNotifyId);
        $allStatus = array("101", "201", "301", "302", "303", "304", "500", "501");
        if($words){
            EdjLog::info('UserNotifyPush getOrderInfo words not empty.');
            foreach($words as $w){
                $word_order_status = isset($w["word_order_status"])?$w["word_order_status"]:"";
                if(!in_array($word_order_status,$allStatus)){
                    continue;
                }
                $word = isset($w["word"])?$w["word"]:"";
                if(!empty($word)){
                    switch ($word_order_status){
                        case 101;
                            $status201["content"] = $word;
                            break;
                        case 201;
                            $status201["content"] = $word;
                            break;
                        case 301;
                            $status301["content"] = $word;
                            break;
                        case 302;
                            $status302["content"] = $word;
                            break;
                        case 303;
                            $status303["content"] = $word;
                            break;
                        case 304;
                            $status304["content"] = $word;
                            break;
                        case 500;
                            $status500["content"] = $word;
                            break;
                        case 501;
                            $status500["content"] = $word;
                            break;
                        default;
                            $status201["content"] = $word;
                            break;
                    }
                }
            }
        }else{
            EdjLog::info('UserNotifyPush getOrderInfo words empty.');
        }
        if($banners){
            EdjLog::info('UserNotifyPush getOrderInfo banners not empty.');
            foreach($banners as $b){
                $banner_order_status = isset($b["banner_order_status"])?$b["banner_order_status"]:"";
                if(!in_array($banner_order_status,$allStatus)){
                    continue;
                }
                $pic_url = isset($b["banner_picture_url"])?$b["banner_picture_url"]:"";
                $act_url = isset($b["banner_jump_url"])?$b["banner_jump_url"]:"";
                switch ($banner_order_status){
                    case 101;
                        $status201["pic_url"] = $pic_url;
                        $status201["act_url"] = $act_url;
                        break;
                    case 201;
                        $status201["pic_url"] = $pic_url;
                        $status201["act_url"] = $act_url;
                        break;
                    case 301;
                        $status301["pic_url"] = $pic_url;
                        $status301["act_url"] = $act_url;
                        break;
                    case 302;
                        $status302["pic_url"] = $pic_url;
                        $status302["act_url"] = $act_url;
                        break;
                    case 303;
                        $status303["pic_url"] = $pic_url;
                        $status303["act_url"] = $act_url;
                        break;
                    case 304;
                        $status304["pic_url"] = $pic_url;
                        $status304["act_url"] = $act_url;
                        break;
                    case 500;
                        $status500["pic_url"] = $pic_url;
                        $status500["act_url"] = $act_url;
                        break;
                    case 501;
                        $status500["pic_url"] = $pic_url;
                        $status500["act_url"] = $act_url;
                        break;
                    default;
                        $status201["pic_url"] = $pic_url;
                        $status201["act_url"] = $act_url;
                        break;
                }
            }
        }else{
            EdjLog::info('UserNotifyPush getOrderInfo banners empty.');
        }
        $statusInfo = array($status201,$status301,$status302,$status303,$status304,$status500);
        $order = array("order_id"=>$orderId, "status_info"=>$statusInfo);
        return $order;
    }

}