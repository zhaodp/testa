<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-6-14
 * Time: 上午11:56
 * To change this template use File | Settings | File Templates.
 */

class ComplainListAction extends CAction{

    public function run()
    {
        if(isset($_GET['attention_id']) && $_GET['attention_id']>0 && isset($_GET['attention_status'])){
            $model = CustomerComplain::model()->findByPk($_GET['attention_id']);
            $model->attention = (int)$_GET['attention_status'];
            $updateOk = $model->save();
            if($updateOk){
            $url = $model->attention ;
            echo json_encode(array('success'=>1,'item_id'=>$model->id,'attention'=>$model->attention,'url'=>$url));
            }else{
            $msg = '';
            foreach($model->errors as $error){
                $msg = $error[0];
                break;
            }
            echo json_encode(array('success'=>0,'msg'=>$msg));
            }
            Yii::app()->end();
        }
        //获取投诉任务组
        $task_gid = isset($_GET['group_id'])?$_GET['group_id']:-1;
        $task_uid = isset($_GET['user_id'])?$_GET['user_id']:-1;
        $userArr = array('-1' => '---全部---');
        $complainGroup = CustomerComplainGroup::model()->getAllGroup();
        $groupArr = array('-1' => '全部');
        foreach ($complainGroup as $item) {
            $groupArr[$item['id']] = $item['name'];
        }

        $complainType=CustomerComplainType::model()->getComplainTypeByID(0);
        $typeArr=array('-1'=>'全部');
        foreach($complainType as $item){
            $typeArr[$item->id]=$item->name;
        }
        //控制搜索定位，默认是父节点,子节点为空
        $parent_id='-1';
        $child_id = ''; //子分类默认select
        $child = array('-1' => '---全部---');

        $model=new CustomerComplain();
        $attArr=$model->attributeLabels();
        $criteria = new CDbCriteria();
        $params=array();
        $model->unsetAttributes();  // clear any default values
        $start_time=$end_time=$handle_start_time=$handle_end_time='';

        if (isset($_GET['search'])) {
            if ($task_gid != -1) {
                $users = CustomerComplainGroupUser::model()->getAllGroupUser($task_gid);
                if (!empty($users)) {
                    foreach ($users as $user) {
                        $userArr[$user['uid']] = $user['uname'];
                    }
                }
            } else {
                $_GET['group_id'] = '';
            }
            if ($task_uid == -1) {
                $_GET['user_id'] = '';
            }


            //重写 分类搜索逻辑。cap wanglonghuan 2014.2.18 16:21
            if (isset($_GET['complain_maintype'])) {
                $_GET['complain_type'] = '';
                $parent_id = $complain_maintype = $_GET['complain_maintype'];
                $data = array();
                $child_id = -1;
                if ($complain_maintype != -1) {
                    $data_complain_types = $data = CustomerComplainType::model()->getComplainTypeByID((int)$complain_maintype);
                    $child = array('-1' => '---全部---');
                    if (!empty($data)) {
                        foreach ($data as $d) {
                            $child[$d->id] = $d->name;
                        }
                    }
                }

                //存在一级分类搜索
                if (isset($_GET['sub_type'])) {
                    if ($_GET['sub_type']=='-1') {
                        //按一级分类查询
                        if (!empty($data)) {
                            $complain_types = array();
                            foreach ($data_complain_types as $v) {
                                $complain_types[] = $v->id;
                            }
                            if(!empty($complain_types)) {
                                $criteria->addInCondition('`t`.complain_type', $complain_types);
                                //i don`t know why not use yii search
                                $params = $criteria->params;
                            }
                            $child_id = '-1';
                        }
                    } else {
                        //按具体二级分类查询
                        $child_id = $_GET['complain_type']=$_GET['sub_type'];
                    }
                }
            }

            if (isset($_GET['start_time']) && isset($_GET['end_time']) ) {
                $start_time = $_GET['start_time'];
                $end_time = $_GET['end_time'];
                $cstr = '';
                if ($start_time) {
                    $cstr = 'create_time>=:s_time';
                    $params[':s_time'] = $start_time;
                }
                if ($end_time) {
                    if ($cstr) {
                        $cstr .= ' and create_time<=:e_time';
                        $params[':e_time'] = $end_time;
                    } else {
                        $cstr .= 'create_time<=:e_time';
                        $params[':e_time'] = $end_time;
                    }
                }
                if($cstr != '')
                    $criteria->addCondition($cstr);
            }

            //add by aiguoxin
            if (isset($_GET['handle_start_time']) && isset($_GET['handle_end_time']) ) {
                $handle_start_time = $_GET['handle_start_time'];
                $handle_end_time = $_GET['handle_end_time'];
                $cstr = '';
                if ($handle_start_time) {
                    $cstr = 'update_time>=:h_s_time';
                    $params[':h_s_time'] = $handle_start_time;
                }
                if ($handle_end_time) {
                    if ($cstr) {
                        $cstr .= ' and update_time<=:h_e_time';
                        $params[':h_e_time'] = $handle_end_time;
                    } else {
                        $cstr .= 'update_time<=:h_e_time';
                        $params[':h_e_time'] = $handle_end_time;
                    }
                }
                if($cstr != '')
                    $criteria->addCondition($cstr);
            }
        }else{
            if (Yii::app()->user->city!=0){
                $criteria->addCondition('city_id=:city_id');
                $params[':city_id'] = Yii::app()->user->city;
            }
            //默认设置为待品监处理
            if(empty($_GET['status'])){
                $criteria->addCondition('status=1');
                $model->status = CustomerComplain::STATUS_CS;

            }
        }

        foreach($_GET as $k=>$v){
            if(array_key_exists($k,$attArr) && (!empty($v) || ($k == 'attention' && $v !== ''))) {
                if($k == 'status' && $_GET['status'] == CustomerComplain::STATUS_EFFECT){
                    $status = array(2,3,4,8);
                    $criteria->addInCondition('`t`.status', $status);
                    $params=array_merge($params,$criteria->params);
                } else {
                    $criteria->addCondition($k.'=:'.$k);
                    $params[':'.$k]=trim($v);
                }
                $model->$k=trim($v);
            }
        }
        if(!empty($_GET['status'])){
            $model->status=$_GET['status'];
        }

        if(isset($_GET['id_tail']) && is_numeric($_GET['id_tail'])){ //投诉id 尾号搜索 added by duke
            $criteria->addCondition(' right(id,1) =:id_tail ');
            $params[':id_tail'] = $_GET['id_tail'];
        }

        if (isset($_GET['complain_id'])) {
            $criteria = new CDbCriteria();//重新new条件，只处理按id查询，其他的条件不管
            $ids = explode(',',trim($_GET['complain_id'],','));
            $criteria->addInCondition('`t`.id', $ids);
            $params = $criteria->params;
            $_GET['complain_id'] = '';
        }

        $criteria->params = $params;
        $criteria->order = 'create_time';
        $dataProvider = new CActiveDataProvider('CustomerComplain', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>20,
            ),
        ));

        $this->controller->render('index',array(
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'handle_start_time'=>$handle_start_time,
            'handle_end_time'=>$handle_end_time,
            'model'=>$dataProvider,
            'vmodel'=>$model,
            'typelist'=>$typeArr,
            'parent_id'=>$parent_id,
            'child'=>$child,
            'child_id' => $child_id,
            'task_gid' => $task_gid,
            'grouplist' => $groupArr,
            'task_uid' => $task_uid,
            'userlist' => $userArr

        ));

    }

}
