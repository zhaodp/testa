<?php
class rankingCommand extends CConsoleCommand
{

	const MAX_MEMORY_LIMIT = 10485760; //10M

	const RANGE_SIZE = 1048576;

	//const REMOTE_URL = 'http://dbstat01.edaijia.cn/dbstat_fc_detect_%s.txt';
	const REMOTE_URL = 'http://dbstat01.edaijia-inc.cn/dbstat_fc_detect_%s.txt';

	private static $rangeSupport = false;

	protected function validateActiveUrl ($url)
	{
		$headers = get_headers($url);

		return false === strpos($headers[0], '404');
	}

	public function actionSync ($date)
	{
		if (! is_numeric($date))
			exit(
			'Please execute this command with date parameter. eg: ranking sync --date=201403 or --date=2014031');
		echo "~~~~~~start~~~~~\n";
		ini_set('memory_limit', '64M');
		$urls = array();
		if (6 == strlen($date)) {
			$i = 1;
			while (true) {
				$url = sprintf(self::REMOTE_URL, $date . $i);
				if (false == $this->validateActiveUrl($url))
					break;
				$urls[] = $url;
				$i ++;
			}
		} else {
			$url = sprintf(self::REMOTE_URL, $date);
			if (false != $this->validateActiveUrl($url)) {
				$urls[] = $url;
			}
		}
		if (empty($urls))
			exit(sprintf('%s is not found', $url));
		foreach ($urls as $url) {
			$this->syncRecords($url);
		}
		echo "~~~~~~end~~~~~~\n";
	}

	/**
	 *
	 * 同步数据
	 * @param string $url
	 * @return void
	 */
	protected function syncRecords ($url)
	{
		$records = array();
		$start = 0;
		$syncSql = "UPDATE `t_daily_order_driver`
					SET alert_level = :alert_level
					WHERE order_id = :order_id";
		$syncRankingDate = array();

		if (self::$rangeSupport) {
			while (self::$rangeSupport) {
				$ret = self::fetchUrl($url, $start . ',' . self::RANGE_SIZE);
				$start += self::RANGE_SIZE;
				if (! empty($ret)) {
					if (self::$rangeSupport) {
						$pos = strrpos($url, "\n");
						$start -= strlen($ret) - $pos;
						$ret = substr($ret, 0, $pos);
					}
					$rows = explode("\n", $ret);
					$command = Yii::app()->dbreport->createCommand($syncSql);
					foreach ($rows as $row) {
						$data = explode("\t", trim($row));
						//print_r($data);
						$syncRankingDate[str_replace('-', '', $data[2])] = true;
						$command->bindValue(':alert_level', $data[3]);
						$command->bindValue(':order_id', $data[0]);
						$command->execute();
						echo sprintf("update complete order_id %d\n", $data[0]);
					}
				} else
					return true;
			}
		} else {
			$ret = self::fetchUrl($url, false);
			$rows = explode("\n", $ret);
			$command = Yii::app()->dbreport->createCommand($syncSql);
			foreach ($rows as $row) {
				$data = explode("\t", trim($row));

				if (4 == count($data)) {
					$syncRankingDate[str_replace('-', '', $data[2])] = true;
					$command->bindValue(':alert_level', $data[3]);
					$command->bindValue(':order_id', $data[0]);
					$res = $command->execute();
					//print_r($res);
				}
				echo sprintf('update complete order_id %d alert_num:%d'."\r\n", $data[0],$data[3]);
			}
		}
		$this->actionSyncRanking(array_keys($syncRankingDate));
	}

	/**
	 *
	 * 同步 driver_ranking_report 表
	 * @param string $date
	 * @return void
	 */
	public function actionSyncRanking ($date)
	{
		if (is_array($date)) {
			foreach ($date as $d) {
				$this->actionSyncRanking($d);
			}
		} else {
			if (! is_numeric($date) || strlen($date) != 8)
			{
				exit(
				'Please execute this command with date parameter. eg: ranking sync --date=20140321');
			}

			$selectSql = "SELECT COUNT(*) AS total, driver_id FROM `t_daily_order_driver`
				WHERE order_date = :order_date AND alert_level > 0
				GROUP BY driver_id";
			$syncSql = "UPDATE `t_driver_ranking_report`
					SET alert_num = :alert_num
					WHERE driver_id  = :driver_id AND date = :dates limit 1";
			$command = Yii::app()->dbreport->createCommand($selectSql);
			$command->bindValue(":order_date", $date);

			$data = $command->queryAll();
			foreach ((array) $data as $key => $value) {
				$commands = Yii::app()->dbreport->createCommand($syncSql);
				$commands->bindValue(":alert_num", $value['total']);
				$commands->bindValue(":driver_id", $value['driver_id']);
				$commands->bindValue(":dates", $date);
				$commands->execute();
			}
			echo "update ".$date." complete \n";
		}
	}

	/**
	 *
	 * 获取远程文件
	 * @param string $url
	 * @param string $range eg:0-100
	 * @param string $method
	 * @param int $timeout
	 * @return string $response
	 */
	public static function fetchUrl ($url, $range = false, $method = 'GET', $timeout = 20)
	{
		$ch = curl_init($url);
		if (false !== $range)
			curl_setopt($ch, CURLOPT_RANGE, $range);
		if (strtoupper($method) == 'POST')
			curl_setopt($ch, CURLOPT_POST, 1);
		else
			curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (curl_errno($ch)) {
			$error = curl_errno($ch);
		}
		curl_close($ch);
		if (416 == $http_code) { //server don't support range
			self::$rangeSupport = false;
			return self::fetchUrl($url, false, $method, $timeout);
		} elseif (200 == $http_code) { //range disabled
			self::$rangeSupport = false;
			return $response;
		} elseif (206 == $http_code) { //range data ok
			return $response;
		} elseif (false === $response) {
			return false;
		}
	}
}
