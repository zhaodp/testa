<?php
/**
 * 查看投诉反馈回复详情
 * User: aiguoxin
 * Date: 2014-09-22
 * Time: 上午10:48
 */

class ComplainViewReplyAction  extends CAction{
    public function run(){

        if(isset($_GET['cid']) && isset($_GET['type'])){

            $complain_id=intval(trim($_GET['cid']));
            $type = intval(trim($_GET['type']));
            $suggestion = CustomerSuggestion::model()->findSuggestionByTypeAndOpinionId($type,$complain_id);
            if($suggestion){
                //获取所有回复
                $msg_models = CustomerSuggestionReply::model()->findAllBySuggestionId($suggestion['id']);
                $this->controller->render('view_reply',array(
                    'msg_models' => $msg_models,
                    'suggestion_id'=> $suggestion['id'],
                    'cid'=>$_GET['cid'],
                    'type'=>$type,
                    ));
            }else{
                if($type == CustomerSuggestion::TYPE_COMPLAIN){
                    echo "<meta charset='utf-8'/>";
                    echo "<script type='text/javascript'>alert('没有找到相关记录');location.href='/index.php?r=complain/list';</script>";
                    Yii::app()->end();
                }else{
                    echo "<meta charset='utf-8'/>";
                    echo "<script type='text/javascript'>alert('没有找到相关记录');location.href='/index.php?r=client/feedback&Feedback[source]=other';</script>";
                    Yii::app()->end();
                }
                
            }
        }

        if(isset($_POST['content']) && isset($_POST['suggestion_id'])
         && isset($_POST['type']) && isset($_POST['cid'])){
            //插入回复
            $suggestion_id = $_POST['suggestion_id'];
            $content = $_POST['content'];
            $type = $_POST['type'];
            $cid = $_POST['cid'];
            $role = CustomerSuggestionReply::ROLE_SYSTEM;
            $user = Yii::app()->user->name;
            $res = CustomerSuggestionReply::model()->addSuggestionReply($suggestion_id,$content,$role,$user);
            if($res > 0){
                $ret['code'] =  1;
                $ret['msg'] = "回复成功!";
                //更改建议状态为已处理
                CustomerSuggestion::model()->updateStatus($suggestion_id,CustomerSuggestion::STATUS_FINISH);
                //发送推送
                CustomerMessage::model()->addFeedBackMsg($suggestion_id,$content);
                //更新记录为已回复
                if($type == CustomerSuggestion::TYPE_COMPLAIN){
                    CustomerComplain::model()->updateReplyStatus($cid,CustomerFeedback::REPLY_STATUS);
                }else{
                    CustomerFeedback::model()->updateReplyStatus($cid,CustomerFeedback::REPLY_STATUS);
                }
            }else{
                $ret['code'] =  0;
                $ret['msg'] = "回复失败!";
            }
            
            echo json_encode($ret);
        }
    }
}