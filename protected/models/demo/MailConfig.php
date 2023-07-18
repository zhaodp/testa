<?php
/**
 * Created by PhpStorm.
 * User: hesongtao
 * Date: 15/4/22
 * Time: 10:58
 */

class MailConfig extends FinanceActiveRecord{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getMailToUsers($fname,$method) {
        $fname = empty($fname)?'':$fname;
        $method = empty($method)?'':$method;

        $sql = "SELECT
					mailto
				FROM
					t_mail_config
				WHERE
					fname = :fname
					and method = :method
                limit 1 ";

        $command = Yii::app()->db_finance->createCommand($sql);

        $command->bindParam(":fname", $fname);
        $command->bindParam(":method", $method);

        $result = $command->queryScalar();
        $resArr = explode(",",$result);

        return $resArr;
    }
}