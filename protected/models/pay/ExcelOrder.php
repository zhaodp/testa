<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 6/12/14
 * Time: 14:43


 */

class ExcelOrder extends CActiveRecord {
	/** 充值的阈值(单位为元), 低于这个值,认为是测试 */
	const LOW_IN = 10.00;
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "{{excel_order}}";
    }

    public function getDetailByDate($date){
        $model = self::model();
        $criteria = new CDbCriteria();
        $criteria->select = "*";
        $criteria->condition = "clearing_date = :date  order by check_status asc";
        $criteria->params = array(":date"=> $date);
        $orders = $model->findAll($criteria);
        return $orders;

    }

    /**
     * 查询某个时间内
     *
     * @param $dateStart
     * @param $dateEnd
     * @return mixed
     */
    public function queryByDate($dateStart, $dateEnd){
        $model = self::model();
        $criteria = new CDbCriteria();
        $criteria->select = "*";
        $criteria->condition = "clearing_date between :dateStart and :dateEnd  order by clearing_date desc";
        $criteria->params = array(":dateStart"=> $dateStart, ":dateEnd"=> $dateEnd );
        $orders = $model->findAll($criteria);
        $dataProvider = new CArrayDataProvider($this->genMailContentMuilt($orders),
            array(
                "id"        => "id",
                'pagination'=>array (
                'pageSize'  =>50),));
        return $dataProvider;
    }

    public function attributeLabels(){

        return array(
            "order_id"      =>  "订单号",
            "income"        =>  "银行入账",
            "fee"           =>  "手续费",
            "balance"       =>  "实收款",
            "bank_card"     =>  "银行卡号",
            "clearing_date" =>  "清算日期",
            "trade_date"    =>  "交易日期",
            "trade_time"    =>  "交易时间",
            "trace_id"      =>  "跟踪号",
            "check_status"  =>  "对账结果",
            "db_count"      =>  "数据库数",
            "user_id"       =>  "用户id",
        );
    }

    /**
     *
     * 把单个条目按照天得粒度进行整理
     *
     * @param $arr
     * @return array
     */
    public function genMailContentMuilt($arr){
        $nextDate = "";

        $driverIn			= 0.00;
        $customerIn			= 0.00;
        $testDriverIn		= 0.00;
        $testCustomerIn		= 0.00;
        $sumIn				= 0.00;
        $sumFee				= 0.00;
        $sumBalance			= 0.00;

        $sumDbCount         = 0.00;


        $driverTmp		= 0.00;
        $customerTmp	= 0.00;
        $sumTest		= 0.00;


        $ret = array();
        $index = 0;
        $i = 0;
        $count = count($arr);
        while( $index < $count){
            $item = $arr[$index];
            $date = $item["clearing_date"];
            if($nextDate == $date){
                $in				=  $item["income"];
				$dbCount		= $item['db_count'];

                //
                $sumIn			+= $in;
                $sumFee			+= $item["fee"];
                $sumBalance		+= $item["balance"];
				//用于数据库如果不存在,是用-1表示的,不能用来计算总和
				if($dbCount > 0){
					$sumDbCount     += $item["db_count"];
				}else{
					$sumDbCount     += 0;
				}
                //
                if($item["isDriver"] == 1){
                    $driverTmp	= $in;
                    $customerTmp= 0.00;
                }else {
                    $customerTmp = $in;
                    $driverTmp	= 0.00;
                }

                if($this->isTest($item)){
                    $testDriverIn	+= $driverTmp;
                    $testCustomerIn	+= $customerTmp;
                    $sumTest		+= $in;
                } else {
                    $driverIn		+= $driverTmp;
                    $customerIn		+= $customerTmp;
                }
                $index += 1;

            } else {
                if( $i != 0){
                    $ret[$i] = array(
                        "id"            => $i,
                        "date"			=> $nextDate,
                        "sumIn"			=> $sumIn,
                        "customerIn"	=> $customerIn,
                        "driverIn"		=> $driverIn,
                        "sumTest"		=> $sumTest,
                        "testDriverIn"	=> $testDriverIn,
                        "testCustomerIn"=> $testCustomerIn,
                        "sumFee"		=> $sumFee,
                        "sumBalance"	=> $sumBalance,
                        "sumDbCount"    => $sumDbCount,
                    );
                }
                $driverIn			= 0.00;
                $customerIn			= 0.00;
                $testDriverIn		= 0.00;
                $testCustomerIn		= 0.00;
                $sumIn				= 0.00;
                $sumFee				= 0.00;
                $sumBalance			= 0.00;
                $sumDbCount         = 0.00;
                //
                $driverTmp		= 0.00;
                $customerTmp	= 0.00;
                $sumTest		= 0.00;
                $i  += 1;
                $nextDate = $date;
            }
            if( $index == $count - 1 ){
                $ret[$i] = array(
                    "id"            => $i,
                    "date"			=> $nextDate,
                    "sumIn"			=> $sumIn,
                    "customerIn"	=> $customerIn,
                    "driverIn"		=> $driverIn,
                    "sumTest"		=> $sumTest,
                    "testDriverIn"	=> $testDriverIn,
                    "testCustomerIn"=> $testCustomerIn,
                    "sumFee"		=> $sumFee,
                    "sumBalance"	=> $sumBalance,
                    "sumDbCount"    => $sumDbCount,
                );
            }

        }
        return $ret;
    }

    /**
     * 返回某个时间区间的概要信息
     *
     * @param $dateStart
     * @param $dateEnd
     */
    public function getSummary($dateStart, $dateEnd){
        $sql = 'select sum(income) as totalIncome, '
            .'sum(fee) as totalFee,'
            .'sum(balance) as totalBalance,'
            .'sum(if(isDriver=1, income, 0)) as totalDriver,'
            .'sum(if(isTest=1 || income < 10, income, 0)) as totalTest'
            .' from  t_excel_order '
            ." where clearing_date between '".$dateStart ."' and '".$dateEnd."'";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $summary = $command->queryAll();
        return $summary;
    }

    public function search(){
        $criteria = new CDbCriteria();
        $criteria->select = "*";
        $criteria->condition = "clearing_date between :dateStart and :dateEnd  order by clearing_date desc";
        //$criteria->params = array(":dateStart"=> $this->dateStart, ":dateEnd"=> $this->dateEnd );

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

	/**
	 * @param $item
	 * @return 如果 is test == 1 或者低于充值阈值,就认为是测试
	 */
	private function isTest($item){
		return ($item['isTest'] == 1) || ($item["income"] <= self::LOW_IN);
	}
} 
