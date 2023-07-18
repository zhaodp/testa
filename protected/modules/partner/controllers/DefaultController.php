<?php
Yii::import("application.components.Lucifer");
class DefaultController extends Controller
{
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'Lucifer',
                'backColor' => 0xFFFFFF,
                'maxLength' => '4', // 最多生成几个字符
                'minLength' => '4', // 最少生成几个字符
                'height' => '45',
                'width' => '110',
                'padding' => '5',
                'offset' => '8',
                //'testLimit'=>0,
                //'fixedVerifyCode' => substr(md5(time()),0,4),
            ));
    }

	public function actionIndex()
	{
        $this->redirect(array('dispatch'));
		//$this->render('index');
	}

    /**
     * 座席登录
     */
    public function actionLogin() {

        $this->layout = '//layouts/main_no_nav';
        $model=new PartnerLoginForm();
        $id = isset($_GET['id']) ? trim($_GET['id']) : null;
        $partner_name = '合作商家';
        if ($id) {
            $common = new PartnerCommon();
            $partner_id = $common->loginDecrypt($id);
            if (intval($partner_id)>0) {
                $partner = Partner::model()->findByPk($partner_id);
                $partner_name = $partner ? $partner['name'] : $partner_name;
            }
        }
        // collect user input data
        if(isset($_POST['PartnerLoginForm']))
        {
            $model->attributes=$_POST['PartnerLoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login()) {
                $params = array (
                    'username'=>Yii::app()->partner->name,
                    'ip'	=> Yii::app()->request->getUserHostAddress(),
                    'agent' => Yii::app()->request->getUserAgent(),
                    'status'=> 2,
                    'url'   => Yii::app()->request->getUrl(),
                    'created'=>date(Yii::app()->params['formatDateTime'], time())
                );
                //纪录访问日志
                //访问人的ip,user_id,访问url,访问时间
                /*
                $task=array(
                    'method'=>'admin_opt_log',
                    'params'=>$params
                );
                Queue::model()->dumplog($task);
                */
                $this->redirect(array('index'));
            }
        }
        // display the login form
        $this->render('login2',array('model'=>$model, 'partner_name'=>$partner_name));
    }

    /**
     * 座席退出
     */
    public function actionLogout()
    {
        Yii::app()->partner->logout(true);
        $this->redirect(Yii::app()->createUrl('business/default/login'));
    }

    /**
     * 派单
     */
    public function actionDispatch()
    {
        $model=new OrderQueue();
        $model->name = '先生';
        $partner = Yii::app()->partner->info;
        $show_preferential = false;
		$partnerBonusSn = '';
        if ($partner) {
            $pay_sort = $partner['pay_sort'];
            if ($pay_sort == Partner::PAY_SORT_BONUS) {
                $common = new PartnerCommon();
//                $bonus_surplus = $common->getBonusSurplus($partner['id']);
//                $show_preferential = $bonus_surplus>0 ? true : false;
//				$partnerBonusSn = $partner['bonus_sn'];
				//从原来的是否有使用优惠券改为是否有优惠券可以使用 2014-09-19 by liutuanwang
				$bonusStatus = $common->isBonusIllegal($partner['id'], '');//暂时一个商家就一个优惠券,不传入第二个参数
				$code = isset($bonusStatus['code']) ? $bonusStatus['code'] : -1;
				$partnerBonusSn = isset($bonusStatus['bonusSn']) ? $bonusStatus['bonusSn'] : '';
				$show_preferential = (0 == $code) ;
//				EdjLog::info('partner is --- '.serialize($partner). 'return bonus Status is --'.json_encode($bonusStatus));
            }
        }
        $driver=$vip=$vipPhone=null;
        if (isset($_REQUEST['phone'])) {
            $phone=trim($_REQUEST['phone']);
            $city_id=1;
            //号码归属地查询
            $city_id=Helper::PhoneLocation($phone);
            $model->phone=$phone;
            $model->city_id=$city_id;

            //检查是否司机电话
            $driver=Driver::getDriverByPhone($phone);
            if (!$driver) {
                $customer=Customer::getCustomer($phone);
                if ($customer) {
                    $model->name=$customer->name;
                } else {
                    $model->name='先生';
                }
            }
        }

        $model->callid=isset($_REQUEST['callid']) ? $_REQUEST['callid'] : md5(time());
        $model->number=isset($_REQUEST['number']) ? $_REQUEST['number'] : 1;
        //TODO 修改为每刻钟步进
        $time_step=20;
        $datetime=date('Y-m-d H:i', time()+$time_step*60);
        $model->booking_time=$datetime; //预约时间 =当前时间+20分钟 BY AndyCong 2013-07-03
        //		$model->booking_time=substr_replace($datetime, '0', strlen($datetime)-1, 1);
        $model->agent_id=Yii::app()->user->id;

        if (isset($_REQUEST['OrderQueue'])) {
            $_REQUEST['OrderQueue']['comments'] = isset($_REQUEST['OrderQueue']['comments']) ? trim($_REQUEST['OrderQueue']['comments']) : $partner['name'];
            $preferential = isset($_REQUEST['preferential']) ? intval($_REQUEST['preferential']) : 0;
            switch($partner['pay_sort']) {
                case Partner::PAY_SORT_DIVIDED:
                    $_REQUEST['OrderQueue']['phone'] = $_REQUEST['OrderQueue']['contact_phone'];
                    //$_REQUEST['OrderQueue']['comments'] = '';
                    break;

                case Partner::PAY_SORT_BONUS:
                    $_REQUEST['OrderQueue']['phone'] = $preferential&&$partner['bonus_phone'] ? $partner['bonus_phone'] : $_REQUEST['OrderQueue']['contact_phone'];
                    //$_REQUEST['OrderQueue']['comments'] = $preferential;
                    break;

                case Partner::PAY_SORT_VIP:
                    $_REQUEST['OrderQueue']['phone'] = $partner['vip_card'];
                    //$_REQUEST['OrderQueue']['comments'] = $preferential;
                    break;
            }

            //$_REQUEST['OrderQueue']['comments'] = isset($_REQUEST['OrderQueue']['comments']) ? trim($_REQUEST['OrderQueue']['comments']) : $partner['name'];
            //检查城市是否选择
            if ($_REQUEST['OrderQueue']['city_id']==0) {
                Yii::app()->clientScript->registerScript('alert', 'alert("客户所在城市必须选择。");');
            } else {
                //订单保存到队列表

                //电话号过滤空格
                $_REQUEST['OrderQueue']['phone'] = trim($_REQUEST['OrderQueue']['phone']);
                $_REQUEST['OrderQueue']['contact_phone'] = trim($_REQUEST['OrderQueue']['contact_phone']);
                //电话号过滤空格 END

                if (empty($_REQUEST['OrderQueue']['contact_phone'])) {
                    $_REQUEST['OrderQueue']['contact_phone']=$_REQUEST['OrderQueue']['phone'];
                }

                unset($model->attributes);

                $model->attributes=$_REQUEST['OrderQueue'];
                $model->created=date(Yii::app()->params['formatDateTime'], time());

                //检测当前地址是否存在地址库中,如存在则更新flag为1  zhanglimin 2013-5-7
                $model->address=strtoupper(trim($model->address)); //强转大写字母
                $model->agent_id = Yii::app()->partner->name;




                $model->channel = $partner['channel_id'];

                //增加验证city_id,modify by sunhongjing 2013-06-06
                $city_id=isset($city_id) ? $city_id : $_REQUEST['OrderQueue']['city_id'];
                if (!empty($model->address)&&AddressPool::model()->checkAddressExists($model->address, $city_id)) {
                    $model->flag=OrderQueue::QUEUE_WAIT_COMFIRM;

                    //更新地址池使用次数
                    AddressPool::model()->putUpdateUseCount($model->address, $city_id, trim($model->number));

                    //回写位置
                    $model->lng=0;
                    $model->lat=0;

                    $gps=AddressPool::model()->getAddressFromPool($model->address, $city_id);
                    if (!empty($gps)) {
                        $model->lng=$gps['lng'];
                        $model->lat=$gps['lat'];
                    }
                }
                //校验是否可以生成日间订单	把 带下单也看做一种来自于400的订单
                $orderArr = Order::model()->CheckSpecialOrderSource(Order::SOURCE_DAYTIME_CALLCENTER, $model->city_id, strtotime($model->booking_time));
                if ($orderArr['code'] == 0) {
                    $model->type = Order::SOURCE_DAYTIME_CALLCENTER;
                }
                if ($model->save()) {
					if(!empty($partnerBonusSn)){
						$bonus_result = BonusLibrary::model()->merchantsBind($partnerBonusSn, $model->phone);
						EdjLog::info('bonus binding ok,bonus_sn is '.$partnerBonusSn.'merchant phone is '.$model->phone.'bonus result is '
							.serialize($bonus_result));
					}else{
						EdjLog::info('bonus_sn is empty, partner is '.serialize($partner));
					}
                    echo "<script> alert('订单已经提交到队列'); window.location.href=window.location.href;</script>";
                } else {
                    $message='';
                    foreach($model->errors as $error) {
                        $message.=$error[0]."\n";
                    }
                }
            }
        }

        $this->render('dispatch', array(
            'model'=>$model,
            'driver'=>$driver,
            'vip'=>$vip,
            'vipPhone'=>$vipPhone,
            'partner' => $partner,
            'show_preferential' =>$show_preferential
        ));

    }

    public function actionClientQueue($phone) {
        $partner = Yii::app()->partner->info;
        if (!$partner && !$partner['channel_id']) {
            throw new Exception('miss');
        }
        $this->layout = '/layouts/main_no_nav';
        $queue=new OrderQueue();
        $queue->unsetAttributes();

        $criteria = new CDbCriteria();
        $criteria->compare('contact_phone',$phone);
        $criteria->compare('channel', $partner['channel_id']);
        $criteria->order = 'flag,booking_time,created,number desc';
        $dataProvider = new CActiveDataProvider('OrderQueue', array (
            'criteria'=>$criteria)
        );

        $this->render('_client_queue', array(
            'dataProvider' => $dataProvider
        ));
    }

    public function actionAjax() {
        $phone = isset($_REQUEST['phone']) ? intval($_REQUEST['phone']) : null;
        $act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : null;
        $partner_id = isset($_REQUEST['partner_id']) ? intval($_REQUEST['partner_id']) : null;
        $result = array();
        try{
            switch($act) {

                case 'get_phone_location':
                    $city_id=Helper::PhoneLocation($phone);
                    $result['status'] = $city_id ? true : false;
                    $result['msg'] = $city_id ? $city_id : 0;
                break;

                case 'get_surplus':
                    if (!$partner_id) {
                        throw new Exception('2001');
                    }
                    $partner = Partner::model()->findByPk($partner_id);
                    if (!$partner) {
                        throw new Exception('2002');
                    }
                    switch($partner->pay_sort) {
                        case Partner::PAY_SORT_BONUS:
                            $data =  CustomerBonus::model()->getBonusUsedSummary($partner->bonus_phone, $partner->bonus_sn);
                            $result['status'] = is_array($data) && count($data) ? true : false;
                            $result['surplus'] = intval($data['totle_num'] - $data['used_num']);
                            $result['total'] = $data['totle_num'];
                            $result['used'] = $data['used_num'];
							//2014-09-19 先不展示优惠券张数
//                            $result['str'] = '优惠劵还有'.$result['surplus'].'张';
                            break;
                        case Partner::PAY_SORT_VIP:
                            $vip = VipPhone::model()->getPrimary($partner->vip_card);
                            if (!$vip) {
                                throw new Exception('3001');
                            }
                            $vip_info = Vip::model()->findByPk($vip['vipid']);
                            $result['status'] = true;
                            $result['surplus'] = $vip_info->balance;
                            $result['str'] = '账户余额还有'.$result['surplus'].'元';
                            break;

                    }
                    break;

                case 'get_bonus_used_num':
                    if (!$phone) {
                        throw new Exception('4001');
                    }
                    $partner = Yii::app()->partner->info;

                    if (!$partner['channel_id']) {
                        throw new Exception('4002');
                    }
                    $common = new PartnerCommon();
                    $result['status'] = true;
                    $result['msg'] = $common->getUsedBonusTotal($phone, $partner['channel_id']);
                    break;

                default :
                    throw new Exception('miss');
            }
        } catch(Exception $e) {
            $result['status'] = true;
            $result['msg'] = $e->getMessage();
        }
        echo json_encode($result);
    }

    protected function queueStatus($row, $data) {
        switch ($data->flag) {
            case 0 :
                $css_class='alert';
                break;
            case 3 :
                $css_class='alert-error';
                break;
            default :
                $css_class='alert-success';
                break;
        }
        return $css_class;
    }

    protected function queueDispatchStatus($data) {
        switch ($data->flag) {
            case 0 :
                $driver_state='等待派单';
                break;
            case 1 :
                $driver_state='已发调度';
                break;
            case 2 :
                $driver_state='调度接单';
                break;
            case 3 :
                $driver_state='取消';
                break;
            case 4 :
                $driver_state='已派单';
                break;
        }

        return $driver_state;
    }

    public function queueCancel($data) {
        //&&$data->agent_id==Yii::app()->user->id
        if ($data->flag==0) {
            return CHtml::link('取消', "javascript:cancelQueue('$data->id')");
        } else {
            return '';
        }
    }

    public function actionCancelQueue($id) {
		if (Yii::app()->request->isAjaxRequest) {
			$model = OrderQueue::model()->findByPk($id);
			if ($model) {
				$attributes = array (
					'flag'=>OrderQueue::QUEUE_CANCEL,
					'comments'=>$model->comments."\n".Yii::app()->user->id.' 取消订单',
					'update_time'=>date(Yii::app()->params['formatDateTime'], time()));
				$model->updateByPk($id, $attributes);

				//取消未派出的订单 BY AndyCong 2013-07-28
				Push::model()->cancelNoDispatchOrder($id);
                if ($model->channel == '03001' && time()<strtotime('2014-01-31')) {
                    BonusLibrary::model()->cancelBonus('39438', $model->contact_phone);
                }
		//TODO 更新队列，发送订单取消消息
			}
		} else
			throw new CHttpException(400, '无效请求. Please do not repeat this request again.');
    }


    public function actionSendPrice() {
        $this->layout=false;
        if (isset($_REQUEST['phone']) && !Yii::app()->partner->isGuest) {
            $city_id = trim($_REQUEST['city_id']);
            $phone=$_REQUEST['phone'];


            //修改价格表短信，默认值改为HZ系列城市, add by sunhongjing 2013-06-30
            switch ( $city_id ) {
                case 7 : //重庆
                    $app_message = '欢迎致电e代驾!收费标准(10公里以内): 39元 每超过5公里加收20元，服务监督电话:4006913939';
                    //'欢迎致电e代驾!收费标准(5公里以内): 39元 每超过5公里加收20元，服务监督电话:4006913939';
                    break;
                case 2 : //成都
                    $app_message=MessageText::getFormatContent(MessageText::DRIVER_PRICE_LIST_CQ);
                    //'欢迎致电e代驾!收费标准(5公里以内): 39元 每超过5公里加收20元，服务监督电话:4006913939';
                    break;
                case 4 : //杭州
                case 8 : //南京
                case 10 : //武汉
                case 11 : //西安
                case 15 : //济南
                case 18 : //郑州
                    $app_message=MessageText::getFormatContent(MessageText::DRIVER_PRICE_LIST_HZ);
                    //'欢迎致电e代驾!收费标准(10公里以内):07:00-22:00 39元 22:00-07:00 59元 每超过5公里加收20元，服务监督电话:4006913939';
                    break;
                case 1 : //北京
                case 3 : //上海
                case 5 : //广州
                case 6 : //深圳
                    $app_message=MessageText::getFormatContent(MessageText::DRIVER_PRICE_LIST);
                    //'欢迎致电e代驾!收费标准(10公里以内):07:00-22:00 39元 22:00-23:00 59元 23:00-00:00 79元 00:00-07:00 99元 每超过10公里加收20元，服务监督电话:4006913939';
                    break;
                default :
                    $app_message=MessageText::getFormatContent(MessageText::DRIVER_PRICE_LIST_HZ);
                    break;

            }

            //短信返回值格式变化
            $sms_ret=Sms::SendSMS($phone, $app_message);
            if ($sms_ret) {
                echo $phone;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }}
