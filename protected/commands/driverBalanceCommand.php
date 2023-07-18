<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 2/12/15
 * Time: 18:04
 */

//邮件引用
Yii::import('application.models.demo.*');
class DriverBalanceCommand extends LoggerExtCommand{

//    private $mailList = array(
//		'hesongtao@edaijia-inc.cn',
//		'liutuanwang@edaijia-inc.cn'
//    );


    public function actionDriverBalanceReport($days){
        //get driver by city id
        $cityList =$this->getDistinctCityId();
        $head = '该报表每天上午8点统计司机余额';
        $html = "二、司机余额统计数按城市统计：<br>";
        $html  .= "<table border='1'>";
        $html  .= "<tr>";
        $html .= $this->buildCell('城市');
        $html .= '<td colspan=3>司机数（余额充足|余额不足|总数）</td>';
        $html .= $this->buildCell('信息费总值');
        $html .= $this->buildCell('余额平均值');
        $html .= $this->buildCell('余额最大值');
        $html .= $this->buildCell('余额最小值');
        $html .= $this->buildCell('屏蔽底限');
		$html .= "</tr>";
		
		list($total_count,$sum_balance,$more_count,$less_count) = array(0,0,0,0);
        foreach($cityList as $city ){
            $cityId = $city['city_id'];
            $cityInfo  = $this->getCityConfig($cityId);
            $item      = $this->getDriverBalance($cityId, $cityInfo['screen_money'], $cityInfo['city_name']);
            $td        = $this->buildTd($item);
            $html .= $td;
			$total_count += $item['total_count'];
			if(isset($item['sum_balance'])) {
				$sum_balance += $item['sum_balance'];
			}
			$more_count += $item['more_count'];
			$less_count += $item['less_count'];
        }
        //list to html
        $html .= '</table>';
		
		$preMonthBalance = $this->getDriverBalanceCount($days);
		$prehtml = "一、司机余额统计数按日期统计<br>";
        $prehtml .= "<table border='1'>";
		$prehtml .= "<tr>";
		$prehtml .= "<td></td>";
		$prehtml .= "<td>信息费总值</td>";
		$prehtml .= "<td>司机总数</td>";
		$prehtml .= "<td>余额充足司机数</td>";
		$prehtml .= "<td>余额不足司机数</td>";
		$prehtml .= "</tr>";
		
		$prehtml .= "<tr>";
		$prehtml .= "<td>".date('Y-m-d',time())."</td>";
		$prehtml .= "<td>".$sum_balance."</td>";
		$prehtml .= "<td>".$total_count."</td>";
		$prehtml .= "<td>".$more_count."</td>";
		$prehtml .= "<td>".$less_count."</td>";
		$prehtml .= "</tr>";
		
		if(!empty($preMonthBalance)) {
			foreach($preMonthBalance as $pre) {
				$prehtml .= "<tr>";
				$prehtml .= "<td>".$pre['date']."</td>";
				$prehtml .= "<td>".$pre['sum_balance']."</td>";
				$prehtml .= "<td>".$pre['total_count']."</td>";
				$prehtml .= "<td>".$pre['more_count']."</td>";
				$prehtml .= "<td>".$pre['less_count']."</td>";
				$prehtml .= "</tr>";
			}
		}
		
		
		$prehtml .= "</table><br><br>";
		//$html = "信息费总值：".$sum_balance."；司机总数：".$total_count."；余额充足司机数：".$more_count."；余额不足司机数：".$less_count."<br>信息费总值：".$preMonthBalance['sum_banlance']."；司机总数：".$preMonthBalance['total_count']."；余额充足司机数：".$preMonthBalance['more_count']."；余额不足司机数：".$preMonthBalance['less_count']."<br>".$html;
		$html = $prehtml."<br>".$html;
        $mailList = MailConfig::model()->getMailToUsers(__CLASS__, __FUNCTION__);
        Mail::sendMail($mailList, $html, '司机信息费余额情况报表'.date('Y-m-d'));
		
		$this->insertTodayDriverBalance($sum_balance,$total_count,$more_count,$less_count);
    }

    private function getCityConfig($cityId){
        $criteria = new CDbCriteria();
        $criteria->compare('city_id', $cityId);
        $cityConfig = CityConfig::model()->find($criteria);
        return array(
            'city_name' => $cityConfig['city_name'],
            'screen_money' => $cityConfig['screen_money'],
        );
    }

    private function listToHtml($list = array()){

        $html = '';
        foreach($list as $item){
            $html .= $this->buildTd($item);
        }
    }

    private function buildTd($item){
        $keyList = array(
            'city_name',
			'more_count',
            'less_count',
            'total_count',
            'sum_balance',
            'avg_balance',
            'max_balance',
            'min_balance',
            'screen_money'
        );
        $tmp = '<tr>';
		
        foreach($keyList as $k){
            if($k == 'city_name'){
                $tmp .= $this->buildCell(($item[$k]));
                continue;
            }
            $tmp .= $this->buildCell(ceil($item[$k]));
        }
        return $tmp.'</tr>';
    }

    private function buildCell($value){
        $tdFormat = '<td>%s</td>';
        return sprintf($tdFormat, ($value));
    }

    private function getDistinctCityId(){
        $sql = 'select distinct city_id as city_id from t_driver_balance order by city_id asc ';
        return Yii::app()->db_finance->createCommand($sql)->queryAll();
    }

    private function getDriverBalance($cityId, $board, $cityName){
        $sqlFormat = 'select city_id, max(balance) as max_balance, min(balance) as min_balance, sum(balance) as sum_balance ,
                              avg(balance) as avg_balance,
                              count(driver_id) as total_count
                          from t_driver_balance
                          where city_id = %s;';
        $sql = sprintf($sqlFormat, $cityId);
        $ret = Yii::app()->db_finance->createCommand($sql)->queryRow();
        $moreSql = 'select count(*) from t_driver_balance where city_id = %s and balance >= %s';
        $lessSql = 'select count(*) from t_driver_balance where city_id = %s and balance < %s';
        $more_count = Yii::app()->db_finance->createCommand(sprintf($moreSql, $cityId, $board))->queryScalar();
        $less_count = Yii::app()->db_finance->createCommand(sprintf($lessSql, $cityId, $board))->queryScalar();
        $ret['city_name'] = $cityName;
        $ret['more_count'] = $more_count;
        $ret['less_count'] = $less_count;
        $ret['screen_money'] = $board;
        return $ret;
    }
	
	private function getDriverBalanceCount($days) {
		$result = array();
		$days = (-1)*$days;
		for($i = -1 ; $i >= $days ; $i-- ) {
			$sdate = date('Y-m-d',strtotime($i.' day'));
			//$edate = date('Y-m-d',strtotime($sdate)+24*3600);
			$sql = "select sum_balance, total_count, more_count, less_count from t_driver_balance_count where count_date like '$sdate%' order by count_date desc limit 1";
			$ret = Yii::app()->dbreport->createCommand($sql)->queryRow();
			if(empty($ret)) continue;
			$ret['date'] = $sdate;
			$result[] = $ret;
		}
		
		return $result;
	}
	
	private function insertTodayDriverBalance($sum_banlance,$total_count,$more_count,$less_count) {
		$sql = "INSERT INTO t_driver_balance_count(sum_balance, total_count, more_count, less_count) "." VALUES(:sum_balance, :total_count, :more_count, :less_count) ";
			
		$params = array (
			":sum_balance"=>$sum_banlance, 
			":total_count"=>$total_count, 
			":more_count"=>$more_count, 
			":less_count"=>$less_count
		);
			
		Yii::app()->dbreport->createCommand($sql)->execute($params);
	}

}