<?php
/**
 * 订单状态机
 * 
 * @author sunhongjing 2013-10-16
 * @modified qiujianping@edaijia-inc.cn 2014-04-03
 * 	This file is changed for new order process.
 *	New states are added to the state machine, and the old
 *	ones are still kept
 *	Newly added states include:
 *		
 * 
 * This is the model class for table "{{order_process}}".
 *
 * The followings are the available columns in table '{{order_process}}':
 * @property string $id
 * @property integer $queue_id
 * @property integer $order_id
 * @property string $state
 * @property string $created
 */
class OrderProcess extends ReportActiveRecord
{

	const ORDER_PROCESS_INIT            = '000'; //初始化
	const ORDER_PROCESS_NEW 			= '101'; //101新订单
	const ORDER_PROCESS_DISPATCH 		= '201'; //201正在派
	const ORDER_PROCESS_ACCEPT 			= '301'; //301已接单
	const ORDER_PROCESS_READY 			= '302'; //302已就位
	const ORDER_PROCESS_DRIVING 		= '303'; //303已开车
	const ORDER_PROCESS_SYS_CANCEL 		= '401'; //401系统取消	
	const ORDER_PROCESS_USER_CANCEL 	= '402'; //402客户取消	
	const ORDER_PROCESS_USER_DESTORY 	= '403'; //403客户销单	
	const ORDER_PROCESS_DRIVER_DESTORY 	= '404'; //404司机销单	
	const ORDER_PROCESS_DRIVER_CANCEL 	= '405'; //405司机拒绝取消	
	const ORDER_PROCESS_FINISH 			= '501'; //501已报单


	// Add qiujianping@edaijia-inc.cn
	// This is the new version of state machine
	// Becare do not change the value in it
	const PROCESS_NEW = '000'; //101新订单
	const PROCESS_DRIVER_CREATE = '001'; //司机补单

	// 派单
	// 选司机派单 START_DISPATCH -> ACCEPT | FAIL   
	const PROCESS_WAIT_DISPATCH = '101'; // 等待派单（400订单）
	const PROCESS_START_DISPATCH = '102'; // 开始系统派单
	const PROCESS_SYS_DISPATCH = '180'; //系统派单(一键下单 & 400订单)
	const PROCESS_ADMIN_DISPATCH = '181'; // 手工派单(400订单)
	const PROCESS_ADMIN_DISPATCHED = '202'; // 手工已派单(400订单)

	// Order progressing
	const PROCESS_ACCEPT = '301'; //301已接单
	const PROCESS_READY = '302'; //302已就位
	const PROCESS_DRIVING = '303'; //303已开车
	const PROCESS_DEST = '304'; //304到达目的地

	// Dispatch result
	// We should provided reason for these types 
	// 由于司机原因派单失败: 30s 没有响应，司机主动拒绝，
	// 司机服务中，司机电话中，司机token失效
	const PROCESS_DISPATCH_FAIL_DRIVER_RELATED = '405';
	// 由于系统原因派单失败: 系统收回，网络异常，推送超时
	const PROCESS_DISPATCH_FAIL_SYS_RELATED = '406';

	// Order finish stats
	const PROCESS_DRIVER_SUBMIT = '500'; //司机手动报单
	const PROCESS_AUTO_SUBMIT = '501'; //501自动报单

	// Failed results
	const PROCESS_USER_CANCEL = '502'; //客户取消	
	const PROCESS_USER_DESTROY = '503'; //客户销单	
	const PROCESS_DRIVER_CANCEL = '504'; //司机取消	
	const PROCESS_DRIVER_DESTROY = '505'; //司机销单	
	const PROCESS_SYS_CANCEL = '506'; //系统取消	
	const PROCESS_ADMIN_CANCEL = '507'; //运营取消	
	// The error code for dispatch failed cases
	// With Reject log
	// All Description please refer to OrderRejectLog
	const DISPATCH_FAIL_SYS_REVOKE = 1;
	const DISPATCH_FAIL_DRIVER_NO_RESPONSE = 2;
	const DISPATCH_FAIL_DRIVER_REJECT = 3;
	const DISPATCH_FAIL_DRIVER_SERVICE = 4;
	const DISPATCH_FAIL_DRIVER_PHONE = 5;
	const DISPATCH_FAIL_SYS_NET_ABNORMAL = 6;
	const DISPATCH_FAIL_DRIVER_INVALID_TOKEN = 7;
	const DISPATCH_FAIL_SYS_OUT_OF_TIME = 8;

	// flag
	const FLAG_NONE = 0;
	const FLAG_PROCESS_DRIVER_SUBMIT = 1;

	// The descriptions for different status
	private static $status_desc_array = array(
	    '000' => '新订单',
	    '001' => '司机补单',
	    '101' => '等待派单',
	    '102' => '开始派单',
	    '180' => '系统派单',
	    '181' => '手工派单',
	    '202' => '手工派单完成',
	    '301' => '司机已接单',
	    '302' => '司机已就位',
	    '303' => '司机已开车',
	    '304' => '到达目的地',
	    '405' => '派单失败-司机原因',
	    '406' => '派单失败-系统原因',
	    '500' => '司机手动报单',
	    '501' => '自动报单',
	    '502' => '客户取消',
	    '503' => '客户销单',
	    '504' => '司机取消',
	    '505' => '司机销单',
	    '506' => '系统取消',
	    '507' => '运营取消',
	    );

	// The descriptions for different faile types
	private static $fail_type_desc_array = array(
	    '1' => '系统收回',
	    '2' => '司机30s未接单成功',
	    '3' => '司机主动拒绝',
	    '4' => '司机服务中',
	    '5' => '司机电话中',
	    '6' => '网络异常',
	    '7' => '司机Token失效',
	    '8' => '订单推送超时弹回',
	    );

	// The dispatch status array
	private static $dispatch_status_array = array(
	    '000' =>'新订单',
	    '101' => '等待派单',
	    '102' => '开始系统派单',
	    '180' => '系统派单',
	    '181' => '手工派单',
	    );

	// The finished status array
	private static $finished_status_array = array(
	    '500' => '手动报单',
	    '501' => '自动报单',
	    '502' => '客户取消',
	    '503' => '客户销单',
	    '504' => '司机取消',
	    '505' => '司机销单',
	    '506' => '系统取消',
	    '507' => '运营取消',
	    );
	

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderProcess the static model class
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
		return '{{order_process}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('state, created', 'required'),
			array('queue_id, order_id', 'numerical', 'integerOnly'=>true),
			array('state', 'length', 'max'=>3),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, queue_id, order_id, state, created', 'safe', 'on'=>'search'),
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
			'queue_id' => 'Queue',
			'order_id' => 'Order',
			'driver_id' => 'Driver',
			'state' => 'State',
			'fail_type' => 'ErrorCode',
			'description' => 'Description',
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
		$criteria->compare('queue_id',$this->queue_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  /**
   * @author qiujianping@edaijia-staff.cn 2014-03-25
   *
   * Get the description of order status
   * @param status The status of the order
   * @return The description of the status
   */
  public function getStatusDescription($status='000') {
    $ret = "未知状态";
        
    if(array_key_exists($status,self::$status_desc_array)) {
      $ret = self::$status_desc_array[$status];
    }
    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-03-25
   *
   * Get the description of fail type
   * @param status The failed type
   * @return The description of the failed type provided
   */
  public function getFailTypeDescription($fail_type='1') {
    $ret = "未知错误码";
    if(array_key_exists($fail_type, self::$fail_type_desc_array)) {
      $ret = self::$fail_type_desc_array[$fail_type];
    }
    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-03-25
   * 
   * Insert a new status to redis queue: orderstate.
   * We don't check the attributes here. All the checking will be
   * done later
   *
   * @param params The attributes to be inserted to the table 
   * @return If the insert is success or failed
   */
  public function genNewOrderProcess($params = array()) {
    $task = array(
	'method' => 'set_order_process_state',
	'params' => $params,
	);
    if(Queue::model()->putin($task , 'orderprocess')) {
      return true;
    }
    // TODO: do log here
    return false;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-03-25
   * 
   * Insert a new status to order process, The actual insertion.
   * Called by the queue process command
   *
   * @param params The attributes to be inserted to the table 
   * @return If the insert is success or failed
   */
  public function insertNewOrderStatus($params = array()) {
    // Check if we have the datas required
    if(empty($params)) {
      return false;
    }

    // Check if the queue_id order_id state created driver_id are
    // in the array
    if(!isset($params['queue_id']) || !isset($params['order_id']) ||
	  !isset($params['state']) || !isset($params['driver_id']) ||
	  !isset($params['created'])) {
      return false;
    }

    // Check if the fail type is set 
    if(!isset($params['fail_type'])) {
      $params['fail_type'] = 0;
      // Set the status description
      $params['description'] = $this->getStatusDescription($params['state']);
    } else {
      // Set the failed type description
      $params['description'] = $this->getFailTypeDescription($params['fail_type']);
    }

    $month = date('Ym', time());
    $insert_table_name = "t_order_process_".$month; 

    return Yii::app()->dbreport->createCommand()->insert($insert_table_name ,$params);
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-03-25
   * 
   * Check if the process is dispatching
   *
   * @param status the status to be checked 
   * @return If the status means dispatching
   */
  public static function isDispatchStatus($status = '000') {
    if(array_key_exists($status,self::$dispatch_status_array)) {
      return true;
    }
    return false;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-03-25
   * 
   * Check if the process has log a order as finished
   *
   * @param status the status to be checked 
   * @return If the status means finished
   */
  public static function isFinishStatus($status = '501') {
    if(array_key_exists($status,self::$finished_status_array)) {
      return true;
    }
    return false;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-04-03
   * 
   * Transform old state to be new ones
   *
   * @params The old states
   * @return The new states
   */
  public static function transFromOldToNew($status = '501',$flag = OrderProcess::FLAG_NONE) {
    $ret = $status;
    if($flag != 0) {
      switch($flag) {
	case OrderProcess::FLAG_PROCESS_DRIVER_SUBMIT:
	  $ret = OrderProcess::PROCESS_DRIVER_SUBMIT;
	  break;
      }
      return $ret;
    }
    switch($status) {
      case OrderProcess::ORDER_PROCESS_INIT:
	$ret = OrderProcess::PROCESS_NEW;
	break;
      case OrderProcess::ORDER_PROCESS_NEW:
	$ret = OrderProcess::PROCESS_NEW;
	break;
      case OrderProcess::ORDER_PROCESS_DISPATCH:
	$ret = OrderProcess::PROCESS_SYS_DISPATCH;
	break;
      case OrderProcess::ORDER_PROCESS_ACCEPT:
	$ret = OrderProcess::PROCESS_ACCEPT;
	break;
      case OrderProcess::ORDER_PROCESS_READY:
	$ret = OrderProcess::PROCESS_READY;
	break;
      case OrderProcess::ORDER_PROCESS_DRIVING:
	$ret = OrderProcess::PROCESS_DRIVING;
	break;
      case OrderProcess::ORDER_PROCESS_SYS_CANCEL:
	$ret = OrderProcess::PROCESS_SYS_CANCEL;
	break;
      case OrderProcess::ORDER_PROCESS_USER_CANCEL:
	$ret = OrderProcess::PROCESS_USER_CANCEL;
	break;
      case OrderProcess::ORDER_PROCESS_USER_DESTORY:
	$ret = OrderProcess::PROCESS_USER_DESTROY;
	break;
      case OrderProcess::ORDER_PROCESS_DRIVER_CANCEL:
	$ret = OrderProcess::PROCESS_DRIVER_CANCEL;
	break;
      case OrderProcess::ORDER_PROCESS_DRIVER_DESTORY:
	$ret = OrderProcess::PROCESS_DRIVER_DESTROY;
	break;
      case OrderProcess::ORDER_PROCESS_FINISH:
	$ret = OrderProcess::PROCESS_AUTO_SUBMIT;
	break;
      default:
	$ret = $status;
    }
    return $ret;
  }

  /**
   * @author qiujianping@edaijia-staff.cn 2014-04-04
   * 
   * Return the process of a specified order
   *
   * @param the order_id to be get 
   * @return All the processes of the order
   */
  public function getOrderProcessesById($order_id = '1') {
      $ret = array();
      $order = Order::model()->getOrderById($order_id);
      if(empty($order)) {
	  return $ret;
      }
      $order_date = date('Ymd H:i:s', $order['created']);
      $month = substr($order_date, 0, 6);
      if(strcmp($month, '201407') < 0) {
	  $insert_table_name = "t_order_process"; 
      } else {
	  $insert_table_name = "t_order_process_".$month; 
      }

      $command = Yii::app()->dbreport->createCommand();
      $command->select('order_id, created, driver_id, state, description')
	  ->from($insert_table_name)
	  ->where('order_id=:order_id')
	  ->order('created ASC');
      $ret = $command->queryAll(true, array(':order_id' => $order_id));
      return $ret;
  }
	
	/**
	 * 验证订单执行过程的状态机,
	 * 000啥没有，101新订单，201正在派，301已接单,302已就位,303已开车,
	 * 401系统取消，402客户取消，403客户销单，404司机销单，405司机取消，501已报单
	 * 000->101,
	 * 101->201,401,402
	 * 201->301,401,402
	 * 301->302,401,402
	 * 302->303,403,404
	 * 303->501
	 * @author sunhongjing 2013-10-15
	 *
	 * In case of miss of some states, the follow status should still be continue 
	 * 101-> 301,302,303,403,404,501
	 * 201-> 301,302,303,403,404,501
	 * 301-> 303,501
	 * 302-> 303,501
	 * 
	 * @param unknown_type $from
	 * @param unknown_type $to
	 * @param unknown_type $order_id
	 * @param unknown_type $queue_id
	 * @return bool
	 */
	public function validOrderState($from=SELF::ORDER_PROCESS_INIT,$to='',$order_id='',$queue_id='')
	{
		$ret = false;
		if( empty($from) || empty($to) ){
			return $ret;
		}
		$order_state_machine = array(
			'000'=>array('101',),
			'101'=>array('201','301','302','303','304','401','402','405','501'),
			'201'=>array('301','302','303','304','401','402','403','404','405','501'),
			'301'=>array('302','303','304','401','402','403','404','501'),//
			'302'=>array('303','304','403','404','501'),
			'303'=>array('304','501',),
			'304'=>array('501',),
			'401'=>array('402','403',),
			);
							
		if( isset($order_state_machine[$from]) ){
			if( in_array($to,$order_state_machine[$from]) ){
				$ret = true;
			}
		}
							
		return $ret;
	}
	
}
