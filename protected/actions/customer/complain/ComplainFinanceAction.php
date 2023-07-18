<?php
/**
 * 财务处理页面
 * User: Bidong
 * Date: 13-6-20
 * Time: 下午1:51
 * To change this template use File | Settings | File Templates.
 */

class ComplainFinanceAction extends CAction
{

    public function run()
    {

        $complain = new CustomerComplain();
        $criteria = new CDbCriteria;

        //司管处理结果为解约的
        $params = array();
        $criteria->addCondition('dm_process=:dm');
        $params[':dm'] = CustomerComplain::DM_PROCESS_S;
        $criteria->addCondition('status in (:status)');
        $params[':status'] = CustomerComplain::STATUS_DM; //司管已处理

        if (isset($_GET['search'])) {
            if(!empty($_GET['customer_phone'])){
                $criteria->addCondition('customer_phone=:cp');
                $params[':cp'] = $complain->customer_phone=trim($_GET['customer_phone']);
            }
            if(!empty($_GET['driver_id'])){
                $criteria->addCondition('driver_id=:did');
                $params[':did'] = $complain->driver_id=trim($_GET['driver_id']);
            }
            $sp_pro=0;
            if(isset($_GET['status'])){
                $sp_pro=intval($_GET['status']);
                $complain->finance_process=$sp_pro;
                switch($sp_pro){
                    case -1:
                        $params[':status'] ='3,4';
                        break;
                    case 1:
                        $criteria->addCondition('finance_process=:fp');
                        $params[':fp'] =1;
                        $params[':status'] ='4';
                        break;
                    case 0:
                        $criteria->addCondition('finance_process=:fp');
                        $params[':fp'] =0;
                        $params[':status'] ='3';
                        break;
                    default:
                        break;


                }

            }


        }

        $criteria->params = $params;
        $data = $complain->findAll($criteria);

        $retData = array();
        foreach ($data as $item) {
            $temp = array();
            //司机解约,需要财务处理

            $temp['complain_id'] = $item->id;
            $temp['customer'] = $item->name . '<br/>' . $item->customer_phone;
            $temp['customer_phone'] = $item->customer_phone; //司机或者用户
            $temp['driver_phone'] = $item->driver_phone;
            $driverStr = '<a target="_blank" href="' . Yii::app()->createUrl('driver/archives', array('id' => $item->driver_id)) . '" url="' . Yii::app()->createUrl('driver/view', array('id' => $item->driver_id)) . '"   >' . $item->driver_id . '</a>';
            $temp['driver'] = $driverStr . '<br/>' . $item->driver_phone; //司机工号
            $temp['costs'] = EmployeeAccount::model()->getDriverBalances($item->driver_id); //司机信息费余额

            $status = $item->finance_process == 1 ? '已处理' : '待处理';
            $temp['status'] = $item->status == CustomerComplain::STATUS_END ? '处理完毕' : $status; //待补偿、待退费

            $p_url = Yii::app()->createUrl('complain/status', array('cid' => $item->id));
            $statusStr = '<br/> <a  data-toggle="modal" data-target="" url="' . $p_url . '"  style="display:inline-block;cursor:pointer;">查看</a>';
            $temp['status'] .= $statusStr;
            $temp['mark'] = '';

            $temp['opt'] = ''; //操作

            $urlPara = array('re' => 'complain/finance', 'cid' => $item->id);
            $processUrl = Yii::app()->createUrl('complain/refund', $urlPara);
            $temp['opt'] .= '<a  style="display:inline-block;cursor:pointer;" url="' . $processUrl . '" mewidth="500px"  data-toggle="modal" data-target="" >退费</a>';


            $retData[] = $temp;
        }

        $dataProvider = new CArrayDataProvider($retData, array(
            'id' => 'id',
            'sort' => array(),
            'pagination' => array(
                'pageSize' => 30,
            ),
        ));
        $dataProvider->keyField = false;


        $this->controller->render('finance', array('model' => $complain, 'data' => $dataProvider));
    }
}