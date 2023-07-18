<?php

/**
 * This is the model class for table "{{driver_interview_time}}".
 *
 * The followings are the available columns in table '{{driver_interview_time}}':
 * @property string $id
 * @property string $interview_date
 * @property integer $interview_num
 * @property integer $city_id
 * @property string $address
 * @property string $remark
 * @property string $operator
 * @property integer $moring
 * @property integer $afternoon
 * @property string $created
 */
class DriverInterviewTime extends CActiveRecord
{

    public static $moring = array(
        10 => '10:00-11:00',
        11 => '11:00-12:00'
    );

    public static $afternoon = array(
        13 => '13:00-14:00',
        14 => '14:00-15:00',
        15 => '15:00-16:00',
    );

    //未发布
    CONST STATUS_UNPUBLISHED = 0;

    //已经发布
    CONST STATUS_PUBLISH = 1;

    //已经删除
    CONST STATUS_DELETE = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverInterviewTime the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{driver_interview_time}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('interview_date, interview_num, city_id, address, remark, operator, created', 'required'),
			array('interview_num, city_id, moring, afternoon', 'numerical', 'integerOnly'=>true),
			array('address, remark', 'length', 'max'=>256),
			array('operator', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, interview_date, interview_num, city_id, address, remark, operator, moring, afternoon, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'interview_date' => 'Interview Date',
			'interview_num' => 'Interview Num',
			'city_id' => 'City',
			'address' => 'Address',
			'remark' => 'Remark',
			'operator' => 'Operator',
			'moring' => 'Moring',
			'afternoon' => 'Afternoon',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('interview_date',$this->interview_date,true);
		$criteria->compare('interview_num',$this->interview_num);
        if ($this->city_id>0) {
            $criteria->compare('city_id',$this->city_id);
        }
		$criteria->compare('address',$this->address,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('moring',$this->moring);
		$criteria->compare('afternoon',$this->afternoon);
		$criteria->compare('created',$this->created,true);
        $criteria->addCondition('status <>'.self::STATUS_DELETE);
        $criteria->order = 'interview_date DESC';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>30,
            ),
		));
	}

    /**
     * 以下方法为自己封装
     */

    /**
     * 保存设置信息
     * @param $data
     */
    public function insertData($data) {
        $model = new DriverInterviewTime();
        if (!isset($data['created']))
            $data['created'] = date('Y-m-d H:i:s', time());
        $model->attributes = $data;
        $result = $model->save();
        return $result;
    }

    public function updateData($id, $data) {
        $model = self::model()->findByPk($id);
        $model->attributes = $data;
        return $model->save();
    }

    /**
     * 获得某城市某天设置的面试信息
     * @param $city_id
     * @param $date
     */
    public function getTimeInfoByDate($city_id, $date) {
        $command = Yii::app()->db_readonly->createCommand();
        $interview_time = $command->select('*')->from('t_driver_interview_time')
            ->where('city_id=:city_id and interview_date=:date', array(':city_id'=>$city_id,':date'=>$date))
            ->queryRow();
        return $interview_time;
    }

    public function unpublished($id, $phone) {
        $model = self::model()->findByPk($id);
        $model->status = self::STATUS_UNPUBLISHED;
        if ($model->save()) {
            $info_model = new DriverInterviewInfo();
            $list = $info_model->getInterviewByDate($model->city_id, $model->interview_date);
            if (is_array($list) && count($list)) {
                $sms_message = '重要短信，十分抱歉，您选择的'.date('Y年m月d日', strtotime($model->interview_date)).'面试时间因故取消，您可到招聘官网中的“预约面试”页面，重新选择面试时间。带来的不便，敬请谅解。联系电话：'.$phone;
                //$sms_message = '重要短信，您选择的'.date('Y年m月d日', strtotime($model->interview_date)).'面试时间因故取消，可到预约面试页面或点击：http://zhaopin.edaijia.cn/queue?act=interview, 重新选择面试时间。带来的不便，敬请谅解。联系电话：'.$phone;
                foreach ($list as $r_id) {
                    $r_model = DriverRecruitment::model()->findByPk($r_id);
                    Sms::SendSMS($r_model->mobile, $sms_message);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获得某日之后可用的面试时间信息
     * @param $current_date 默认为当天
     */
    public function getUsableInterviewTime($city_id, $current_date = null, $limit=6) {
        $date = $current_date ? $current_date : date('Y-m-d', time());
        $command = Yii::app()->db_readonly->createCommand();
        $interview_time_data = array();
        $search_str = '';
        if ($city_id)
            $search_str = ' and city_id='.$city_id;
        $interview_time = $command->select('*')->from('t_driver_interview_time')
            ->where('interview_date>=:date and status=:status'.$search_str, array(':date'=>$date, ':status'=>self::STATUS_PUBLISH))
            ->limit($limit)
            ->queryAll();
        if (is_array($interview_time) && count($interview_time)) {
            $info_model = new DriverInterviewInfo();
            foreach ($interview_time as $v) {
                $interview_time_data[$v['interview_date']] = array(
                    'date' => $v['interview_date'],
                    'week' => DriverInterviewTime::transition($v['interview_date']),
                    'interview_num' => $v['interview_num'],
                    'address' => $v['address'],
                    'remark' => $v['remark'],
                    'city_id' => $v['city_id']
                );
                $time_part = array();
                if ($v['moring'])
                    $time_part = array_merge_recursive($time_part, array_keys(self::$moring));
                if ($v['afternoon'])
                    $time_part = array_merge_recursive($time_part, array_keys(self::$afternoon));
                $interview_time_data[$v['interview_date']]['info'] = $info_model->getNumberOfPeopleByTimeList($v['city_id'], $v['interview_date'], $time_part);
            }
        }
        return $interview_time_data;
    }

    /**
     * 功能：获取指定年月日是星期几
     * 传参：年月日格式：2010-01-01的字符串
     * 返回值：计算出来的星期值
     */
    public static function transition($date, $chinese=true) {
        $datearr = explode("-", $date);     //将传来的时间使用“-”分割成数组
        $year = $datearr[0];       //获取年份
        $month = sprintf('%02d', $datearr[1]);  //获取月份
        $day = sprintf('%02d', $datearr[2]);      //获取日期
        $hour = $minute = $second = 0;   //默认时分秒均为0
        $dayofweek = mktime($hour, $minute, $second, $month, $day, $year);    //将时间转换成时间戳
        $w_n = date("w", $dayofweek);      //获取星期值
        $return = '';
        switch($w_n) {
            case 0 :
                $return = '星期日';
            break;
            case 1:
                $return = '星期一';
            break;
            case 2:
                $return = '星期二';
            break;
            case 3:
                $return = '星期三';
            break;
            case 4:
                $return  = '星期四';
            break;
            case 5:
                $return =  '星期五';
            break;
            case 6:
                $return = '星期六';
            break;
        }
        if ($chinese)
            return $return;
        else
            return $w_n;
    }

    /**
     * 获得客户端真实IP
     * @return bool|string
     */
    public static function getRealIp() {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = false;
        return $ip;
    }

    /**
     * 防CSRF攻击
     * @param $id_card
     * @param $phone
     * @return bool|string
     */
    public function getCSRFCode($id_card, $phone) {
        $key = 'INTERVIEWBOOKING';
        $ip = self::getRealIp();
        if ($ip) {
            $string = $key.ip2long($ip).$id_card.$phone;
            return md5($string);
        } else {
            return false;
        }
    }


    /**
     * 统计可用面试时间
     * @param int $city_id
     * @author duke
     * @return mixed
     */
    public function getCanInterviewTime($city_id = 0){
        $where = 'interview_date>=:date and status=:status';
        $params = array(':date'=>date('Y-m-d', time()),':status'=>DriverInterviewTime::STATUS_PUBLISH);
        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }
        $interview = Yii::app()->db_readonly->createCommand()->select('count(*)')->from('t_driver_interview_time')
            ->where($where, $params )
            ->queryScalar();
        return $interview;
    }
}