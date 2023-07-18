<?php

class MenuController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * 创建一级菜单
	 * @author bidong
	 */
	public function actionCreate()
	{
		$model=new Menu;

        $position=0;
        $model->parentid=0;
        if(isset($_GET['pid'])){
            $model->parentid=$_GET['pid'];
        }
        $menuCount=$model->getMaxSequence($model->parentid);
        $position=$menuCount+1;
        $model->position=$position;

		if(isset($_POST['Menu']))
		{
            if(!isset($_POST['Menu']['name']) || !$_POST['Menu']['name']) {
                Helper::alert('请输入菜单名称');exit;
            }

            if($model->parentid>0){
		    if(!isset($_POST['AdminAction']) || !$_POST['AdminAction'] ) {
			Helper::alert('请选择对应功能模块');exit;
		    }
	    }
			$model->attributes=$_POST['Menu'];
            $model->operator=Yii::app()->user->id;
            $model->create_time=date('Y-m-d H:i:s');
            if($model->parentid>0){
                $radios = $_POST['AdminAction'];
                if($radios['id']){
                    $model->roles_id=$radios['id'];
                }else{
                    echo CHtml::script("alert('请选择一个入口功能');");
                    exit;
                }
            }

            if($model->position <> $position && $position>0){
                //不是默认排序,且指定排序小于最大排序，更新排序.
                if($model->position <= $position){
                    $model->changeSequence($model->parentid,$model->position,$position);
                }else{
                    echo CHtml::script("alert('排序设置,不能大于当前最大排序');");
                    exit;
                }
            }



          	if($model->save()){
                $this->redirect(array('admin'));
            }

		}

        //取上一级菜单
        $parentArr=array();
        $isSub=intval($model->parentid) != 0 ? true : false;
        if($isSub){
            $parent_info = Menu::model()->findByPk($model->parentid);
            $menus= Menu::model()->getMenuListByPid($parent_info->parentid);
            foreach($menus as $item){
                $parentArr[$item['id']]=$item['name'];
            }
        }
        $action_info = AdminActions::model()->getAllAction();

        $this->render('create',array(
                'model'=>$model,
                'parents'=>$parentArr,
                'action_info'=>$action_info,
        ));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
        $position_old=$model->position;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Menu']))
		{
			$model->attributes=$_POST['Menu'];
            $maxSeq=$model->getMaxSequence($model->parentid);

            if($model->position <> $position_old && $position_old>0){
                //不是默认排序,且指定排序小于最大排序，更新排序.
                if($model->position <= $maxSeq){
                    $model->changeSequence($model->parentid,$model->position,$position_old);
                }else{
                    echo CHtml::script("alert('排序设置,不能大于当前最大排序');");
                    exit;
                }
            }

            if($model->parentid<>0){

                $radios = $_POST['AdminAction'];
                if($radios['id']){
                    $model->roles_id=$radios['id'];
                }else{
                    echo CHtml::script("alert('请选择一个入口功能');");
                    exit;
                }
            }
			if($model->save()){
                $this->redirect(array('admin'));
            }

		}
        $isMain=intval($model->parentid) > 0 ? false : true;
        $parentArr=array();
        if(!$isMain){
            $parent_info = Menu::model()->findByPk($model->parentid);
            $menus= Menu::model()->getMenuListByPid($parent_info->parentid);
           foreach($menus as $item){
              $parentArr[$item['id']]=$item['name'];
           }
        }
        //getAllNormalAction
        //print_r($parentArr);die;
        $action_info = AdminActions::model()->getAllAction();
        //print_r($action_info);die;
		$this->render('update',array(
			'model'=>$model,
            'parents'=>$parentArr,
            'action_info' =>$action_info,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        $menus=Menu::model()->getMenuListByPid($id);
        if($menus){
            //当存在二级菜单时，主菜单不可删除
            echo CHtml::script("alert('当存在子菜单时，主菜单不可删除');");
            exit;
        }else{
            $this->loadModel($id)->delete();
        }

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $model=new Menu('search');
        $model->unsetAttributes();
		$this->render('index',array(
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
       $ret= Menu::model()->getMenuList();

		$model=new Menu('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Menu']))
			$model->attributes=$_GET['Menu'];

        $dataProvider=new CArrayDataProvider($ret, array(
            'id'=>'id',
            'sort'=>array(),
            'pagination'=>array(
                'pageSize'=>100,
            ),
        ));
        $dataProvider->keyField=false;
		$this->render('admin',array(
            'model'=>$model,
            'dataProvider'=>$dataProvider
		));
	}

    public function actionGetRoles($data){
       //$rolesInfo= AdminRoles::model()->getRolesInfo($data['roles_id']);
        $rolesInfo = AdminActions::model()->getInfo($data['roles_id']);

        if($rolesInfo){
            $ca=$rolesInfo['controller'].'/'.$rolesInfo['action'];
            $url=Yii::app()->createUrl($ca);
            $name=$rolesInfo['name'].'&nbsp;'.$ca;
            echo CHtml::link($name,$url) ;
        }
    }

    public function actionOptUrl($data){
        $createStr='';
        $editStr=Yii::app()->createUrl('menu/update',array('id'=>$data['id']));
        $delStr=Yii::app()->createUrl('menu/delete',array('id'=>$data['id']));
        if($data['parentid']==0){
            $createStr=CHtml::link('新建二级菜单',Yii::app()->createUrl('menu/create',array('pid'=>$data['id'])));
        }
        else{
            $parent_info = Menu::model()->findByPk($data['parentid']);
            if (is_object($parent_info) && $parent_info->parentid == 0) {
                $createStr = CHtml::link('┖新建三级菜单',Yii::app()->createUrl('menu/create',array('pid'=>$data['id'])));
            }
        }
        echo CHtml::link('编辑',$editStr).'&nbsp;&nbsp;'.CHtml::link('删除',$delStr).'&nbsp;&nbsp;'.$createStr ;
    }

    public function showStep($data){
        $str = '' ;
        if($data['parentid'] == 0){
            $str = $data["position"];
        }else{
            $parent_info = Menu::model()->findByPk($data['parentid']);
            if($parent_info->parentid == 0){
                $str = '┖'.$data["position"];
            }
            else {
                $str = '&nbsp;&nbsp;&nbsp;&nbsp;└'.$data["position"];
            }
        }
        echo $str;
    }
    //''

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Menu the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Menu::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Menu $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='menu-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
