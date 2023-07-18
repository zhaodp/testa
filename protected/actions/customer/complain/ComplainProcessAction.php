<?php
/**
 * 投诉确认 补偿、扣款申请
 * User: Bidong
 * Date: 13-6-17
 * Time: 下午2:02
 * To change this template use File | Settings | File Templates.
 */

class ComplainProcessAction extends CAction
{

    public function  run()
    {

        $complain_id = '';
        $urlReferrer = '';
        $order_id = '';
        $isLook = '';

        if ($_GET) {
            if (isset($_GET['re']))
                $urlReferrer = $_GET['re'];
            if (isset($_GET['cid']))
                $complain_id = $_GET['cid'];
            if (isset($_GET['oid']))
                $order_id = $_GET['oid'];
            if (isset($_GET['isLook']))
                $isLook = $_GET['isLook'];//用于区分是否点击的处理或是查看按钮 1代表查看
        }
        $complainModel = new CustomerComplain();
        if ($complain_id)
            $complainModel = $complainModel->findByPk($complain_id);

        $firstSort='';//一级分类
        $secondSort='';//二级分类
        if($isLook){//如果是点击查看进入则直接显示一二级分类
            $complainType = CustomerComplainType::model()->getComplainType($complainModel->complain_type);
            if($complainType){
                $secondSort = $complainType[0]->name;
                $complainTypeSecond = CustomerComplainType::model()->getComplainType($complainType[0]->parent_id);
                if($complainTypeSecond){
                    $firstSort=$complainTypeSecond[0]->name;
                }
            }
        }
        $is_vip='(非VIP)';
        $vipModel = Vip::model()->getPrimary($complainModel->customer_phone);
        if($vipModel)
            $is_vip='(VIP '.$vipModel->id.')';

        $complainModel->order_id = $order_id;
        $process_time = date('Y-m-d H:i:s', time());
        $operator = Yii::app()->user->id;


        if ($_POST) {
            $complain_id = $_POST['cid'];
            $order_id = $_POST['order_id'] ? $_POST['order_id'] : '';
            $complain_type_id = $_POST['sub_type']? $_POST['sub_type'] : '';
            $cus_process_type=$_POST['cus_process_type'];
            $dri_process_type=$_POST['dri_process_type'];

            $status = CustomerComplain::STATUS_EFFECT;
            //涉及到钱，需要财务审核
            if($cus_process_type!=1 || $dri_process_type!=1){
                $status = CustomerComplain::STATUS_MONEY_CONFIRM;
            }
            //只有投诉没有处理的时候，才能再处理
            $res = CustomerComplain::model()->setHandled($complain_id,$status);
            //检测投诉是否已经处理
            if(empty($res)){
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript'>alert('该投诉已经被处理不能重复处理同一投诉。');history.back();</script>";
                Yii::app()->end();
            }

            if(!$complain_type_id) {
                return false;
            }

            //处理投诉修改
            if ($_POST['complain_maintype'] == '-1' || $complain_type_id == '-1') {
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript'>alert('请选择投诉分类');history.back();</script>";
                Yii::app()->end();
            }
            if (empty($_POST['mark'])) {
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript' charset='utf-8'>alert('请填写处理意见');history.back();</script>";
                Yii::app()->end();
            }


            //客户扣款 补偿
            $this->fillDeduction($cus_process_type,$dri_process_type,$complain_id,$operator,$process_time);

            //更改投诉状态和记录日志
            $this->updateStatusAndWriteLog($operator,$process_time,$status,$complain_id,$order_id);


            if (isset($_POST['closing']) && $_POST['closing'] == 1) {
                CnodeLog::model()->pushCnodeLog($complain_id,CnodeLog::FINISH,'完结投诉');
            }

            //确认投诉后 扣除司机对应的代价分 add by duke
            if($status == CustomerComplain::STATUS_EFFECT) {
                EdjLog::info('complain='.$complain_id.'不涉及到补扣款，直接扣分...');
                $driver_id = isset($_POST['driver_id']) ? trim($_POST['driver_id']) : ''; //司机工号
                if ($complain_id && $driver_id) { //先查询是否该城市已经配置了扣分功能
                    //$complainModel = $complainModel->findByPk($complain_id);
                    EdjLog::info('1---------------driver_id='.$driver_id);
                    $driver_info = Driver::model()->getProfile($driver_id); //
                    if ($driver_info) {
                        //开始扣分
                        $this->deductScore($driver_id,$driver_info->city_id,$order_id,$complain_type_id,
                            $complain_id,$complainModel->create_time,$driver_info->ext_phone,$driver_info->phone);
                    }
                    $this->controller->redirect($_POST['re']);
                } else {
                    var_dump($complainModel->getErrors());
                    Yii::app()->end();
                }
                $this->controller->redirect($_POST['re']);
            } else {
                $this->controller->redirect($_POST['re']);
            }
        }


        $complainType = CustomerComplainType::model()->getComplainTypeByID(0);
        $typeArr = array('-1' => '全部');
        foreach ($complainType as $item) {
            $typeArr[$item->id] = $item->name;
        }

        //add by aiguoxin 2014-07-08 对于驳回的，需要恢复以前的处理意见
        //分类
        $firstComplain=-1;
        $secondComplain=-1;
        $lastMark='';
        $secondTypeArr=array('-1' => '全部');
        $recoupModel = new CustomerComplainRecoup;
        if($complainModel->status ==  CustomerComplain::STATUS_REJECT ){
            $complainType = CustomerComplainType::model()->getComplainType($complainModel->complain_type);
            if($complainType){
                $secondComplain = $complainType[0]->id;
                $firstComplain=$complainType[0]->parent_id;
            }
            $complainModel->complain_type=$firstComplain;
            $complainType = CustomerComplainType::model()->getComplainTypeByID($firstComplain);
            foreach ($complainType as $item) {
                $secondTypeArr[$item->id] = $item->name;
            }

            //处理意见
            $handleContent='';
            $complainLog = CustomerComplainLog::model()->getComplainLogList($complain_id);
            if($complainLog){
                $handleContent = $complainLog[0]->mark;
            }
            //涉及补偿
            $command = Yii::app()->db_readonly->createCommand();
            //最后一条品监处理意见
            $lastMark = $command->select('mark')
                ->from('t_customer_complain_log')
                ->where('process_type=1 and complain_id=:cid', array(':cid' => $complain_id))
                ->order('create_time DESC')
                ->queryRow();

            $lastMark = $lastMark['mark'];
            //获取补偿信息
            $recoupModel = CustomerComplainRecoup::model()->getComplainRecoup($complain_id);

        }
        if($complainModel->status ==  CustomerComplain::STATUS_MONEY_CONFIRM  && $isLook ){
            $recoupModel = CustomerComplainRecoup::model()->getComplainRecoup($complain_id);//说明确认了投诉但是涉及到了钱所以需要财务审核isLook=1的时候说明是点击查看按钮显示补偿信息
        }
        $taModel = CtrafficAccident::model();
        $trafficInfo = $taModel -> find("customer_id=:customer_id",array(':customer_id'=>$complain_id));//根据客户投诉id得到交通事故案件信息
        if(!empty($trafficInfo)){
            $taModel = $trafficInfo;
        }

        $ciModel = CustcarInsure::model();
        $carInsureInfo = $ciModel -> find("customer_id=:customer_id",array(':customer_id'=>$complain_id));//根据客户投诉id得到客户车辆保险信息
        if(!empty($carInsureInfo)){
            $ciModel = $carInsureInfo;
        }

        $ccModel = CaccidentCost::model();
        $costInfo = $ccModel -> find("customer_id=:customer_id",array(':customer_id'=>$complain_id));//根据客户投诉id得到交通事故涉及费用信息
        if(!empty($costInfo)){
            $ccModel = $costInfo;
        }

        $this->controller->render('process', array(
            'model' => $complainModel,
            'typelist' => $typeArr,
            'secondtypelist'=>$secondTypeArr,
            'secondComplain' => $secondComplain,
            'show_complain' => $order_id ? true : true,
            're' => $urlReferrer,
            'cid' => $complain_id,
            'vip'=>$is_vip,
            'mark'=>$lastMark,
            'recoupModel' => $recoupModel,
            'taModel' => $taModel,
            'ciModel' => $ciModel,
           'ccModel' => $ccModel,
           'isLook' => $isLook,
           'firstSort' => $firstSort,
           'secondSort' => $secondSort,
        ));
    }


    /**
     * @param $cus_process_type
     * @param $dri_process_type
     * @param $complain_id
     * @param $operator
     * @param $process_time
     * 补扣款
     */
    private function fillDeduction($cus_process_type,$dri_process_type,$complain_id,$operator,$process_time){
        $confirmLog = "";//确认投诉后记录进CnodeLog表的日志内容
        if($cus_process_type !=1 || $dri_process_type !=1){
            $process_type='';
            //recoup_type只针对用户了，如果补偿司机，这个为0,导致搜索不到
            $recoup_type = CustomerComplainRecoup::RECOUP_TYPE1;
            if($cus_process_type == 1){
                //不处理客户
                $confirmLog .= '[不处理客户]';
                if($dri_process_type == 2){
                    //补偿司机
                    $confirmLog .= '[补偿司机'.trim($_POST['new_driver_id']).' ('.trim($_POST['driver_cash']).') 元]';
                    $process_type=CustomerComplainRecoup::PROCESS_TYPE4;
                }elseif ($dri_process_type == 3) {//扣款司机
                    $confirmLog .= '[扣款司机'.trim($_POST['new_driver_id']).' ('.trim($_POST['driver_cash']).') 元]';
                    $process_type=CustomerComplainRecoup::PROCESS_TYPE5;
                }elseif ($dri_process_type == 1) {
                    $confirmLog .= '[不处理司机]';
                }
            }else{
                if($cus_process_type==2){
                    //补偿用户
                    $confirmLog .= '[补偿客户'.trim($_POST['binding_phone']).' ('.(trim($_POST['vip_cash'])!='' ? trim($_POST['vip_cash']):'0').') 元 ('.$_POST['bonus'].") 优惠券]";
                    $process_type=CustomerComplainRecoup::PROCESS_TYPE2;
                    if($dri_process_type == 2){
                        //补偿司机 也补偿司机
                        $confirmLog .= '[补偿司机'.trim($_POST['new_driver_id']).' ('.trim($_POST['driver_cash']).') 元]';
                        $process_type=CustomerComplainRecoup::PROCESS_TYPE1AND3;
                    }elseif ($dri_process_type == 3) {
                        //补偿用户,司机扣款
                        $confirmLog .= '[扣款司机'.trim($_POST['new_driver_id']).' ('.trim($_POST['driver_cash']).') 元]';
                        $process_type=CustomerComplainRecoup::PROCESS_TYPE1AND4;
                    }elseif ($dri_process_type == 1) {
                        $confirmLog .= '[不处理司机]';
                    }
                }
                if($cus_process_type==3){
                    //扣款客户
                    $confirmLog .= '[扣款客户'.trim($_POST['binding_phone']).' ('.(trim($_POST['vip_cash'])!='' ? trim($_POST['vip_cash']):'0').') 元 ('.$_POST['bonus'].")优惠券]";
                    $process_type=CustomerComplainRecoup::PROCESS_TYPE3;    //用户扣款
                    if($dri_process_type == 2){
                        //用户扣款,司机补偿
                        $confirmLog .= '[补偿司机'.trim($_POST['new_driver_id']).trim($_POST['driver_cash']).'元]';
                        $process_type=CustomerComplainRecoup::PROCESS_TYPE2AND3;
                    }elseif ($dri_process_type == 3) {
                        //用户扣款,司机扣款
                        $confirmLog .= '[扣款司机'.trim($_POST['new_driver_id']).' ('.trim($_POST['driver_cash']).') 元]';
                        $process_type=CustomerComplainRecoup::PROCESS_TYPE2AND4;
                    }elseif ($dri_process_type == 1) {
                        $confirmLog .= '[不处理司机]';
                    }
                }
            }
            //插入补偿流水
            $data['complain_id']=$complain_id;
            $data['recoup_type']=$recoup_type;   //补偿扣款类型，现金or优惠券
            $data['customer']=$data['recoup_customer']=trim($_POST['binding_phone']);     //用户手机号、VIP卡
            $data['driver_id']=$data['recoup_driver']=trim($_POST['new_driver_id']);
            $data['amount_driver']=trim($_POST['driver_cash']);
            $data['amount_customer']=trim($_POST['vip_cash']);
            $data['process_type']=$process_type;
            $data['mark']=trim($_POST['mark']);
            $data['user']=$operator;
            $data['create_time']=$process_time;
            CustomerComplainRecoup::model()->addComplainRecoup($data);
            //节点日志
            CnodeLog::model()->pushCnodeLog($complain_id,CnodeLog::CONFIRM_COMPLAIN,$confirmLog);
        }
    }

    private function updateStatusAndWriteLog($operator,$process_time,$status,$complain_id,$order_id){
        $complainLog = new CustomerComplainLog();
        $process_result = '';
        if(!empty($_POST['vip_cash'])){
            $process_result = CustomerComplain::SP_PROCESS_T2;
        }
        if(!empty($_POST['bonus'])){
            $process_result = CustomerComplain::SP_PROCESS_T1;
        }

        $complainLog->mark = trim($_POST['mark']);
        $complainLog->result = $process_result;
        $complainLog->complain_id = $_POST['cid'];
        $complainLog->process_type = $complainLog::PROCESS_ONE; //品监处理
        $complainLog->operator = $operator;
        $complainLog->create_time = $process_time;
        $complainLog->recoup_type = $complainLog->result; //补偿方式


        //更新complain 状态
        $complainModel = CustomerComplain::model()->findByPk($_POST['cid']);
        $type = $_POST['sub_type'] ? $_POST['sub_type'] : $_POST['complain_maintype'];// 如果第二级分类没选则添加第一级分类
        $complainModel->attributes = $_POST['CustomerComplain'];
        $complainModel->complain_type = $type;
        $complainModel->source = $_POST['source'];

        $complainModel->sp_process = $process_result; //品监处理结果
        $complainModel->operator = $operator;
        $complainModel->update_time = $process_time;
        $complainModel->detail = htmlspecialchars(trim($_POST['detail']));
        $complainModel->driver_id=htmlspecialchars(trim($_POST['driver_id']));
        $complainModel->order_id=htmlspecialchars(trim($_POST['order_id']));


        //处理司机扣分程序，插入数据 by 曾志海
        if(isset($_POST['confirm_btn_d'])&&$_POST['confirm_btn_d']==1
            && $status == CustomerComplain::STATUS_EFFECT){
            $OneComplain_id = Yii::app()->db_readonly->createCommand()
                ->select('id')
                ->from('{{customer_complain_deduct}}')
                ->where('complain_id=:complain_id', array(':complain_id'=>$complain_id))
                ->queryScalar();
            //存在投诉id不让再写入数据了
            if(!$OneComplain_id){
                $customerComplainDedict=new CustomerComplainDeduct();
                $customerComplainDedict->complain_id=$complain_id;
                if(!empty($_POST['driver_id'])){
//                            $citys=Dict::items('city_prefix');
//                            $citys=array_flip($citys);
//                            $pre=strtoupper(substr($_POST['driver_id'],0,2));
                    $customerComplainDedict->city_id=DriverStatus::model()->getItem(trim($_POST['driver_id']),'city_id');
                    $customerComplainDedict->driver_id=$_POST['driver_id'];
                }
                $customerComplainDedict->complain_type_id=$_POST['sub_type'];
                $customerComplainDedict->order_id=$order_id;

                $TypePerformance=$Performance='';
                $Performance=CustomerComplainType::model()->getComplainType($_POST['sub_type']);
                if($Performance){
                    $TypePerformance= $Performance[0]->performance;
                }

                $customerComplainDedict->mark=$TypePerformance;
                $customerComplainDedict->create_time=date('Y-m-d H:i:s',time());
                $customerComplainDedict->insert();
            }

        }

        $complainModel->status = $status;
        EdjLog::info('complain='.$complain_id.'状态更改成功...status='.$status);
        $complainModel->save();
        //最后记录操作日志
        $complainLog->insert();
    }


    private function deductScore($driver_id,$city_id,$order_id,$complain_type_id,$complain_id,$complain_time,$ext_phone,$phone){
        EdjLog::info('1---------------driver_id='.$driver_id);
        $driver_city_id = $city_id;
        //如果投诉有订单则查询订单ID 否则查询投诉创建时间
        $order_info = Order::model()->getOrdersById($order_id);
        if ($order_info) {
            $compare_time = $order_info['created'];
        } else {
            $compare_time = strtotime($complain_time);
        }

        if (Common::checkOpenScore($driver_city_id, $compare_time)) {
            //var_dump($_POST['sub_type']);die;
            $complain_type = CustomerComplainType::model()->findByPk($complain_type_id);
            //print_r($complain_type);die;
            $deduct_score = $complain_type->score;
            if ($deduct_score) { //如果对应的投诉有扣分项
                EdjLog::info('complain='.$complain_id.'开始扣分score='.$deduct_score);
                $driver_ext_mod = new DriverExt(); //扣除司机对应分数 、 查看扣分后是否应该屏蔽司机、 发送扣分短信，屏蔽短信
                $res = $driver_ext_mod->scoreDeduct($driver_id, -$deduct_score, $complain_type_id);
                $block_day = $res['update_res'] && $res['had_punished'] ? $res['block_day'] : 0; //司机是否被屏蔽了
                $comment_sms_id = 0;
                if ($order_id) {
                    $complain_mod = CommentSms::model()->getCommandSmsByOrderId($order_id);
                    if (!empty($complain_mod)) {
                        $comment_sms_id = $complain_mod['id'];
                    }
                }

                //print_r($complain_mod);die;


                $param = array(
                    'driver_id' => $driver_id,
                    'customer_complain_id' => $complain_id,
                    'complain_type_id' => $complain_type_id,
                    'operator' => Yii::app()->user->id,
                    'driver_score' => -$deduct_score,
                    'block_day' => $block_day,
                    'comment_sms_id' => $comment_sms_id,
                    'city_id' => $driver_city_id,
                    'create_time' => date('Y-m-d H:i:s'),
                    'deduct_reason' => $complain_type->name,
                    'revert' => DriverPunishLog::REVERT_NO_EXECUTE,

                );
                DriverPunishLog::model()->addData($param);
                EdjLog::info('complain='.$complain_id.'扣分成功...');
                //var_dump($driver_punish_log_res);die;
                if ($res['had_punished'] == false) {

                    $message = $driver_id . ' 师傅,您由于 ' . $complain_type->name . ',被扣 ' . $deduct_score . ' 分。';
                    if ($order_id) {
                        $message .= '订单号：' . $order_id;
                    }
                    $message .= '(三日内可申述)';//http://jira.edaijia.cn/browse/UPDATE-2831
                    $i_phone = ($ext_phone) ? $ext_phone : $phone;
                    Sms::SendSMS($i_phone, $message);
                }
            }
        }
    }
}