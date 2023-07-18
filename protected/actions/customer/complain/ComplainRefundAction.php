<?php
/**
 * 司机退费
 * User: Bidong
 * Date: 13-6-20
 * Time: 下午10:02
 * To change this template use File | Settings | File Templates.
 */

class ComplainRefundAction extends CAction
{

    public function run()
    {

        $urlReferrer = $complain_id = '';

        if (isset($_GET['re']))
            $urlReferrer = $_GET['re'];
        if (isset($_GET['cid']))
            $complain_id = $_GET['cid'];

        $this->controller->renderPartial('_form_re_driver',
            array(
                'cid' => $complain_id,
                're' => $urlReferrer));

        if ($_POST) {
            $complainLog = new CustomerComplainLog();

            if (isset($_POST['re']))
                $urlReferrer = $_POST['re'];
            if (isset($_POST['cid'])) {
                $complain_id = $_POST['cid'];
            } else {
                return;
            }


            $complainLog->complain_id = $complain_id;

            $complainLog->process_type = $complainLog::PROCESS_THREE; //财务处理
            $complainLog->operator = Yii::app()->user->id;
            $complainLog->create_time = date('Y-m-d H:i:s', time());
            $complainLog->mark = trim($_POST['mark']);

            $finance_process = 0;

            if (isset($_POST['driver_save'])) {

                if (isset($_POST['cast']))
                    $complainLog->cast = trim($_POST['cast']);
                if (isset($_POST['clothing_fee']))
                    $complainLog->clothing_fee = trim($_POST['clothing_fee']);
                if (isset($_POST['card_fee']))
                    $complainLog->card_fee = trim($_POST['card_fee']);
                if (isset($_POST['other_fee']))
                    $complainLog->other_fee = trim($_POST['other_fee']);
                if (isset($_POST['mark']))
                    $complainLog->mark = trim($_POST['mark']);
                $complainLog->result = 12; //解约退费
                $finance_process = 1; //财务退费司机
            }

            if ($complainLog->insert()) {

                $complain = CustomerComplain::model()->findByPk($complain_id);

                $complain->status = CustomerComplain::STATUS_FC;
                $complain->finance_process = $finance_process; //财务已处理
                $complain->save();


                $this->controller->redirect(Yii::app()->createUrl($_POST['re']));
            }


        }
    }


}