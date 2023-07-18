<?php
/**
 * 用户组管理（角色管理）列表动作action
 * @author sunhongjing 2013-03-01
 */
class ListAction extends CAction
{

    public function run()
    {
        $selParentid = '';
        $model = new AdminGroup('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['AdminGroup'])){
            $model->attributes = $_GET['AdminGroup'];
            if(isset($_GET['AdminGroup']['parentid'])||$_GET['AdminGroup']['parentid']!=''){
                $selParentid=$_GET['AdminGroup']['parentid'];
            }
        }



        $criteria = new CDbCriteria();
        $criteria->addCondition('parentid=:parentid');
        $criteria->params[':parentid']=0;
        $criteria->order='position';
        $parents = AdminGroup::model()->findAll($criteria);
        $parentsArr = array(""=>"全部",0=>"一级");

        foreach($parents as $item){
            $parentsArr[$item['id']]=$item['name'];
        }

        $this->controller->render('group_list', array ('model'=>$model,'parents'=>$parentsArr,'selParentid'=>$selParentid));
    }
}