<?php
/**
 * 撤销投诉
 * User: aiguoxin
 * Date: 2014-05-26
 */

class ComplainRevertAction extends CAction
{

    public function  run()
    {

        $complain_id = '';
        $urlReferrer = '';
        $order_id = '';

        if ($_GET) {
            if (isset($_GET['re']))
                $urlReferrer = $_GET['re'];
            if (isset($_GET['cid']))
                $complain_id = $_GET['cid'];
            if (isset($_GET['oid']))
                $order_id = $_GET['oid'];
        }
        $complainModel = new CustomerComplain();
        if ($complain_id)
            $complainModel = $complainModel->findByPk($complain_id);
        $complainModel->order_id = $order_id;
        $process_time = date('Y-m-d H:i:s', time());
        $operator = Yii::app()->user->id;
        //print_r($_POST);die;
        if ($_POST) {
            $complain_id = $_POST['cid'];
            //检测投诉是否已经处理
            $checkrepeat = DriverPunishLog::model()->checkPunishRepeat($complain_id,1);
            if($checkrepeat){
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript'>alert('该投诉已经被撤销不能重复撤销同一投诉。');history.back();</script>";
                Yii::app()->end();
            }

            //validate revert reason
            if (empty($_POST['reason'])) {
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript'>alert('请填写撤销原因');history.back();</script>";
                Yii::app()->end();
            }
            // echo 'update CustomerComplain status=6 ok'.PHP_EOL;
            //更新处罚日志t_driver_punish_log
            $punishLog = DriverPunishLog::model()->getPunishByComplainId($complain_id);
            
            $complain = CustomerComplain::model()->getComplainById($complain_id);
            if(empty($complain)){
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript'>alert('找不到对应的投诉');history.back();</script>";
                // Yii::app()->end();
            }
            

            $driver_id = $punishLog['driver_id'];
            $DriverExt = DriverExt::model()->getDriverExt($driver_id);
            if(empty($DriverExt)){
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript'>alert('找不到DriverExt');history.back();</script>";
                Yii::app()->end();
            }
            $currentScore = $DriverExt['score'];
            $driver_info = Driver::model()->getProfile($driver_id);
            $blockflag = false;
            $score = -$punishLog['driver_score'];
            $city_open_config = Common::checkOpenScoreCity($driver_info['city_id'],'all');
            $revertScore=$currentScore+$score;
            //fix bug array index is too big
            if($currentScore < 0){
                $currentScore = 0;
            }
            if($currentScore > 12){
                $currentScore = 12;
            }
            if($revertScore < 0){
                $revertScore = 0;
            }
            if($revertScore > 12){
                $revertScore = 12;
            }
            $now_score_punish = $city_open_config['block'][$currentScore]; //Common::checkOpenScoreCity($driver_info->city_id,'block',$driver_now_score);
            $before_punish=$city_open_config['block'][$revertScore];
            $disableScore=Common::checkOpenScoreCity($driver_info['city_id'],'disable_score');

            //1---------------------补偿或补扣司机费用
            $money = $punishLog['driver_money'];
            if($money != 0){
                $money = -$money;
                $driverBalance = array();
                $driverBalance['user'] = $driver_id;
                $driverBalance['cast'] = $money;
                $driverBalance['order_id'] = $complain['order_id'];
                $driverBalance['type'] = EmployeeAccount::TYPE_INFOMATION;
                $res = DriverBalance::model()->updateBalance($driverBalance, $driverBalance['type']);
                
                //set liushui
                $table_date = date('Ym');
                $data['settle_date'] = date('Ym');
                $data['created'] = time();
                EmployeeAccount::$table_name = $table_date;
                $employeeAccount = new EmployeeAccount();
                $data['user']=$driver_id;
                $data['city_id']=$driver_info['city_id'];
                $data['channel']=0;
                $data['type']=EmployeeAccount::TYPE_FORFEIT;
                $data['order_id']=$complain['order_id'];
                $data['cast']=$money;
                $data['comment']=$_POST['reason'];
                $data['balance']=$res;
                $employeeAccount->refreshMetaData();
                $employeeAccount->attributes = $data;
                $employeeAccount->insert();
            }
            
            
            //2-----------------------解除系统屏蔽
            if($punishLog['revert'] == DriverPunishLog::REVERT_NO){//保证这条投诉是进行过投诉屏蔽生效的
                if($now_score_punish>0){
                    $block_day = 0;
                    $lastPunishLog = DriverPunishLog::model()->getLastPunishLog($driver_id);
                    if($lastPunishLog && ($lastPunishLog['id'] == $punishLog['id'])){
                        $block_day = -$lastPunishLog['block_day'];
                    }else{
                        $driverpunish = DriverPunish::getHandledPunish($driver_id);
                        if($driverpunish){
                            $block_day = $before_punish-$driverpunish['limit_time'];
                        }
                    }
                    DriverPunish::model()->disable_driver($driver_id,
                        $punishLog['complain_type_id'],$_POST['reason'],$block_day);
                }else{
                    DriverPunish::model()->enable_driver($driver_id,
                        $punishLog['complain_type_id'],$_POST['reason'],$operator);
                        $blockflag = true;
                }
            }
            

            //3-----------------------revert score
            $res = DriverExt::model()->addScore($driver_id,$score);
            // if($res < 1){
            //     echo "<meta charset='utf-8'/>";
            //     echo "<script type='text/javascript'>alert('恢复分数失败');history.back();</script>";
            //     Yii::app()->end();
            // }            

            //4------------------------短信评价失败，不在app显示
            $res = CommentSms::model()->setLevelZero($complain['order_id']);
            // if($res < 1){
            //     echo "<meta charset='utf-8'/>";
            //     echo "<script type='text/javascript'>alert('取消短信评价失败');history.back();</script>";
            //     Yii::app()->end();
            // }    
            //5------------------------取消培训通知
            if(($revertScore+$disableScore) >12 && ($currentScore+$disableScore) <=12){
                $res = DriverExt::model()->revertUnTrain($driver_id);
                // if($res < 1){
                //     echo "<meta charset='utf-8'/>";
                //     echo "<script type='text/javascript'>alert('取消司机培训失败');history.back();</script>";
                //     Yii::app()->end();
                // } 
            }

            //6------------------------更新撤销状态t_customer_complain#status->6
            //revert subcompany kpi(status >1 and status<5)
            $res = CustomerComplain::model()->setStatus($complain_id,CustomerComplain::STATUS_REVERT);
            // if($res < 1){
            //     echo "<meta charset='utf-8'/>";
            //     echo "<script type='text/javascript'>alert('设置投诉状态为取消失败');history.back();</script>";
            //     Yii::app()->end(); 
            // }

            //7------------------------add处罚日志t_driver_punish_log
            $param = array(
                                    'driver_id' => $punishLog['driver_id'],
                                    'customer_complain_id' => $punishLog['customer_complain_id'],
                                    'parent_id' => $punishLog['id'],
                                    'complain_type_id' => $punishLog['complain_type_id'],
                                    'operator' => $operator,
                                    'driver_score'=>$punishLog['driver_score'],
                                    'block_day' =>$punishLog['block_day'],
                                    'comment_sms_id' => $punishLog['comment_sms_id'],
                                    'create_time' => date('Y-m-d H:i:s'),
                                    'deduct_reason' => $punishLog['deduct_reason'],
                                    'revert' => 1,
                                    'revert_reason' => trim($_POST['reason']),

                                );
            $res = DriverPunishLog::model()->addData($param);
            //update parentID
            $res = DriverPunishLog::model()->updatePunish($punishLog['id'],$punishLog['id']);
            // if($res < 1){
            //     echo "<meta charset='utf-8'/>";
            //     echo "<script type='text/javascript'>alert('更新处罚日志失败');history.back();</script>";
            //     Yii::app()->end();
            // }            

            //8------------------------send message
            $message = $driver_id.'师傅,您申诉成功,代驾证分已恢复';
            if($blockflag){
                $message=$message.',且已解除相应屏蔽';
            }
            if($money > 0){
                $message=$message.',返还'.$money.'元补偿';
            }elseif ($money < 0) {
                $message=$message.',扣除'.$money.'元';
            }
            if($complain['order_id']){
                $message = $message.',订单号：'.$complain['order_id'].',请退出重登录查看';
            }
            $i_phone = ($driver_info->ext_phone) ? $driver_info->ext_phone : $driver_info->phone;
            $res = Sms::SendSMS($i_phone, $message);
            //Sms::SendSMS($driver_info['phone'], $message);

            //9--------------记录操作日志
            CustomerComplainLog::$db=Yii::app()->db;
            $complainLog = new CustomerComplainLog();
            $process_result = CustomerComplain::SP_PROCESS_T13;
            $complainLog->mark = trim($_POST['reason']);
            $complainLog->result = $process_result;
            $complainLog->complain_id = $complain_id;
            $complainLog->process_type = $complainLog::PROCESS_ONE; //品监处理
            $complainLog->operator = $operator;
            $complainLog->create_time = date('Y-m-d H:i:s');
            $complainLog->recoup_type = $complainLog->result; //补偿方式
            $complainLog->insert();
            CnodeLog::model()->pushCnodeLog($complain_id,CnodeLog::REVERT_COMPLAIN,trim($_POST['reason']));//撤销投诉添加到最新日志
            //add by aiguoxin  reload driver info for money
            DriverStatus::model()->reload($driver_id,false);
            //10-------------redirect
            $this->controller->redirect($_POST['re']);
 
        }

        //type add by aiguoxin
        $firstComplain='';
        $secondComplain='';
        $complainType = CustomerComplainType::model()->getComplainType($complainModel->complain_type);
        if($complainType){
            $secondComplain = $complainType[0]->name;
            $complainTypeSecond = CustomerComplainType::model()->getComplainType($complainType[0]->parent_id);
            if($complainTypeSecond){
                $firstComplain=$complainTypeSecond[0]->name;
            }
        }
        //处理意见
        $handleContent='';
        $complainLog = CustomerComplainLog::model()->getComplainLogList($complain_id);
        if($complainLog){
            $handleContent = $complainLog[0]->mark;
        }
        //driver money
        $punishLog = DriverPunishLog::model()->getPunishByComplainId($complain_id);
        if (empty($punishLog)) {
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript'>alert('找不到对应的处罚日志');history.back();</script>";
                // Yii::app()->end();
            }
        $money = -$punishLog['driver_money'];

        $this->controller->render('revert', array(
            'model' => $complainModel,
            'show_complain' => $order_id ? true : true,
            're' => $urlReferrer,
            'cid' => $complain_id,
            'firstComplain'=>$firstComplain,
            'secondComplain'=>$secondComplain,
            'handleContent'=>$handleContent,
            'money'=>$money,
        ));
    }
}