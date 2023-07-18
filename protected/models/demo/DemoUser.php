<?php

/**
 * business model
 *
 */
Yii::import('application.models.schema.CarDemo');

class DemoUser extends CarDemo
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

	/**
	 * 返回用户名，
	 */
    public function getName($user_id)
    {
        $user = self::model()->find('user_id=:user_id', array(
            ':user_id' => $user_id));
        if ($user) {
            return $user->name;
        }
        return '';
    }

	/**
	 * 重置密码
	 */
    public function resetPassword($user_id){
        $new_pwd='';
        $user=self::model()->findByPk($user_id);
        if($user){
            //生成新密码
            $new_pwd=rand(1000,9999);
			$user->pass = $new_pwd;
			$user->save();
        }

        return $new_pwd;
    }

}