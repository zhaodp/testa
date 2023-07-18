<?php

class CcicagentController extends Controller {
    public $layout = '//layouts/column2';

    public function actionAgentlist() {
        $sql = "SELECT agent_num,agent_name,password,name FROM t_ccic_agent";
	$and_flag = 0;

	if(!empty($_REQUEST['agent_num'])) {
	    $sql .= ' WHERE agent_num="'.$_REQUEST['agent_num'].'"';
	    $and_flag = 1;
	}
	if(!empty($_REQUEST['name'])) {
	    if($and_flag == 0) {
	        $sql .= ' WHERE name like "%'.$_REQUEST['name'].'%"';
	    }
	    else {
	        $sql .= ' AND name like "%'.$_REQUEST['name'].'%"';
	    }
	    $and_flag = 1;
	}

        $sql .= " ORDER BY agent_num";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $result = $command->queryAll();
        $data = new CArrayDataProvider($result, array (
                'id'         => 'ccic_agent',
                'keyField'   => 'agent_num',
                'pagination' => array('pageSize' => 50),
            )
        );
        $this->render('agent_list', array(
		                  'dataProvider' => $data,
	));
    }

    public function actionDelagent() {
        if (!empty($_REQUEST['agent_num']) ) {
            $sql = "DELETE FROM t_ccic_agent WHERE agent_num = :agent_num";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":agent_num", $_REQUEST['agent_num']);
            $command->execute();
        }
        $this->redirect(Yii::app()->createUrl('ccicagent/agentlist'));
    }

    public function actionAddagent() {
        if (!empty($_POST)) {
            $sql = "INSERT INTO t_ccic_agent(`agent_num`,`agent_name`,`name`,`password`,`phone`) VALUES(:agent_num,:agent_name,:name,:password,:phone)";
            $count = count($_POST['agent_num']);
            $i=0;
            for($i=0; $i<$count; $i++) {
                $agent_num = isset($_POST['agent_num'][$i]) ? $_POST['agent_num'][$i] : '';
                $name = isset($_POST['name'][$i]) ? $_POST['name'][$i] : '';
                $agent_name = $agent_num;
                $password = isset($_POST['password'][$i]) ? $_POST['password'][$i] : '';
		$phone = '';

                $sql_check = "SELECT agent_num FROM t_ccic_agent WHERE agent_num='".$agent_num."'";
                $result = Yii::app()->db_readonly->createCommand($sql_check)->queryRow();
                if(empty($result)) {
                    $command = Yii::app()->db->createCommand($sql);
                    $command->bindParam(":agent_num", $agent_num);
                    $command->bindParam(":agent_name", $agent_name);
                    $command->bindParam(":name", $name);
                    $command->bindParam(":password", $password);
                    $command->bindParam(":phone" , $phone);
                    $command->execute();
                    $command->reset();
                }
            }
            $this->redirect(Yii::app()->createUrl('ccicagent/agentlist'));
        }
    	$this->render('add_agent');
    }
}
