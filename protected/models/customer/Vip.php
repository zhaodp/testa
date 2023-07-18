<?php

/**
 * This is the model class for table "{{vip}}".
 *
 * The followings are the available columns in table '{{vip}}':
 * @property string $id
 * @property string $name
 * @property string $phone
 * @property string $company
 * @property string $send_phone
 * @property string $email
 * @property integer $send_type
 * @property string $type
 * @property integer $credit
 * @property integer $city_id
 * @property double $totelamount
 * @property double $balance
 * @property string $status
 * @property string $operator
 * @property string $commercial_invoice
 * @property string $remarks
 * @property integer $created
 */
class Vip extends FinanceActiveRecord {
    //搜索选项（消费统计） --start
    public $aveCost = null;         //平均消费
    public $changeRate = null;      //变化率
    public $changeCost = null;     //变化量
    public $aveCostType = null;     //平均消费判断类型
    public $changeRateType = null;  //变化率判断类型
    public $changeCostType = null; //变化量判断类型
    public $recordStatus = null;    //跟进状态
    //搜索选项（消费统计） --end

    /**
	 * 储值卡
	 */
	const TYPE_CREDIT = 0;
	/**
	 * 定额卡
	 */
	const TYPE_FIXED = 1;
	/**
	 * 补偿卡
	 */
	const TYPE_COMPENSATE = 2;

	/**
	 * 正常
	 */
	const STATUS_NORMAL = 1;
	/**
	 * 禁用
	 */
	const STATUS_DISABLE = 2;
	/**
	 * 欠费
	 */
	const STATUS_ARREARS = 3;

	/**
	 * wap接受短信
	 */
	const SEND_TYPE_WAP = 0;

	/**
	 * 短信接受
	 */
	const SEND_TYPE_SMS = 1;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Vip the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{vip}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, phone, id','required'),
            array('credit, city_id, created', 'numerical', 'integerOnly'=>true),
            array('totelamount, balance,invoice_type', 'numerical'),
            array('id, phone, send_phone', 'length', 'max'=>15),
            array('name, company, email', 'length', 'max'=>50),
            array('email,contact,address,telephone', 'length', 'max'=>255),
            array('commercial_invoice, remarks', 'length', 'max'=>150),
            array('type, status ,send_type', 'length', 'max'=>1),
            array('operator', 'length', 'max'=>20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, company, phone, send_phone, email, send_type, type, credit, city_id, totelamount, balance, status, operator, created,'
                . 'aveCost, changeRate, changeCost, aveCostType, changeRateType, changeCostType, recordStatus',
                'safe', 'on'=>'search'),
        );
    }

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array (
                    'vipCostExt'=>array(
                        self::HAS_ONE,
                        'VipCostExt',
                        'vip_id',
                    )
                );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
            'id' => 'VIP卡号',
            'name' => '姓名',
            'company' => '公司名称',
            'phone' => '手机号',
            'send_phone' => '接受短信备用号（如果不填默认为主卡手机号）',
            'email' => '邮件地址',
            'send_type' => '发送类型',
            'type' => 'VIP卡类型',
            'credit' => '信誉度',
            'city_id' => 'VIP办理城市',
            'totelamount' => '总消费金额',
            'balance' => '余额',
            'status' => '状态',
            'operator' => '经手人',
			'commercial_invoice' => '发票抬头',
			'remarks' => '备注',
            'created' => '开卡时间',
            'invoiced' => '发票申请',
		
	   'contact' => '收件人',
	   'telephone' => '收件人电话',
	   'address' => '地址',
	   'invoice_type' => '发票类型',
        );
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($extCriteria=NULL) {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

        $criteria->compare('t.id',$this->id,true);
        $criteria->compare('name',$this->name,true);
//        if($this->name){
//        	$vipPhone = VipPhone::model()->find('name = :name',array(':name' => $this->name));
//        	if($vipPhone){
//        		$criteria->compare('id',$vipPhone->vipid,true);
//        	}
//        }
        if($this->phone){
        	$vipPhone = VipPhone::model()->find('phone = :phone and status = :status',array(':phone'=>$this->phone,':status'=>VipPhone::STATS_NORMAL));

        	if($vipPhone){
        		$criteria->compare('id',$vipPhone->vipid,false);
        	}else{
        		$criteria->compare('phone',$this->phone,false);
        	}
        }
        if($this->type >=0){
        	$criteria->compare('type',$this->type,true);
        }
        $criteria->compare('credit',$this->credit);
        if($this->city_id != 0){
        	$criteria->compare('city_id',$this->city_id);
        }
        $criteria->compare('totelamount',$this->totelamount);
        if($this->balance){
        	$criteria->addCondition('balance < '.$this->balance);
        }
        if($this->status != 0){
        	$criteria->compare('status',$this->status,true);
        }
        $criteria->compare('commercial_invoice',$this->commercial_invoice,true);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('company',$this->company,true);
        $criteria->compare('created',$this->created);
        $criteria->order = 'created desc';

        if($extCriteria !== null){
            $criteria->mergeWith($extCriteria);
        }

        return new CActiveDataProvider($this, array (
			'pagination'=>array (
				'pageSize'=>30
			),
            'criteria'=>$criteria,
        ));
	}

//	public function beforeSave(){
//		if ($this->getIsNewRecord()) {
//			$vip = self::model()->find('id = :id',array(':id' => $this->id));
//			$data = $vip->attributes;
//			$vip_log = json_encode($data);
//
//		}
//	}
//

	public function insertVip($data){
		$return = FALSE;
		$model = new Vip();
		if(empty($data['send_phone'])){
			$data['send_phone'] = $data['phone'];
		}
		$data['operator'] = Yii::app()->user->getId();
		$data['created'] = time();
		$data['totelamount'] = $data['balance'] = $data['totelamount'];
		$model->attributes = $data;
		if(isset($data['invoiced'])){//充值卡激活的时候没有发票申请
			$model->invoiced=$data['invoiced'];
			$model->contact=$data['contact'];
			$model->address=$data['address'];
			$model->telephone=$data['telephone'];
		}
		if($model->save()){
            //添加日志
            $data['description'] = '新创建vip';
			//把操作日志给放到异步,不是关键步骤
			$task = array(
				'method'    => 'dump_insert_vip_log',
				'params'    => $data,
			);
			Queue::model()->putin($task, 'settlement');
            //在vipPhone中添加主卡
            $dataVipPhone = array();
            $dataVipPhone['vipid'] = $data['id'];
            $dataVipPhone['name'] = $data['name'];
            $dataVipPhone['type'] = VipPhone::TYPE_MAIN;
            $dataVipPhone['phone'] = $data['phone'];
            $dataVipPhone['status'] = 1;
            $dataVipPhone['created'] = time();
            VipPhone::model()->createVipPhone($dataVipPhone, false);

            //添加充值记录
            $model->vipTrade($data['id'], $data['totelamount'], $data['totelamount']);

            $return = TRUE;
		}else{
			EdjLog::error('insert vip error, user phone '.$data['phone'].' error info is '.json_encode($model->getErrors()));
		}
		return $return;
	}


    public function getCreateTimeByCard($cardNo){
        $userVip = Vip::model()->getByCard($cardNo);
        if ($userVip){
            return date('Y-m-d H:i:s', $userVip->created);
        }
        return '';
    }

    public function getPhoneByCard($vipcard){
	$userVip=Vip::model()->getByCard($vipcard);
	if($userVip){
	    return $userVip->phone;
	}
	return '';
    }

    public function getCardByPhone($phone){
	$userVip=Vip::model()->getPrimaryPhone($phone);
	if($userVip){
	    return $userVip->id;
	}
	return '';
    } 
    /**
     *
     * 按卡号查询vip信息
     * @param int $vipcard
     */
    public function getByCard($vipcard) {
        $criteria = new CDbCriteria();
        $criteria->condition = 'id =:vipcard';
        $criteria->params = array (
            ':vipcard'=>$vipcard
        );
        $vip = Vip::model()->find($criteria);
//        if(!$vip){
//            $vip = VipTrade::model()->find('comment like "%'.$vipcard.'%"');
//        }
        return $vip;
    }


    /**
	 * 充值
	 */
	public function vipIncome($model, $amount, $order_id = '', $remarks = '',$type=0,$source=0){
		$return = FALSE;
		$vip = $model->attributes;
		$vip['totelamount'] += $amount;
		$vip['balance'] += $amount;
		if($vip['balance'] > 0){
			$vip['status'] = self::STATUS_NORMAL;
		}
		$model->attributes = $vip;
		if($model->save()){
			EdjLog::info('vipIncome success orderId:'.$order_id.'|amount:'.$amount);
			$return = $this->vipTrade($vip['id'], $amount, $vip['balance'], $order_id,$remarks,$type,$source);

		}else{
			EdjLog::info('vipIncome fail orderId:'.$order_id.'|amount:'.$amount);
			$return=false;
		}
		//add vip log
		$vip['description'] = '充值vip,充值结果'.serialize($return);
		$vip['created'] = time();
        $operator = Yii::app()->user->getId();
		$vip['operator'] = empty($operator) ? '系统' : $operator;
		//把操作日志给放到异步,不是关键步骤
		$task = array(
			'method'    => 'dump_insert_vip_log',
			'params'    => $vip,
		);
		Queue::model()->putin($task, 'settlement');
		return $return;
	}

	public function vipTrade($vipid, $amount, $balance, $order_id = null,$remarks='',$type=0,$source=0){
		$modelTrade = new VipTrade();
		$vipTrade = $modelTrade->attributes;
		$vipTrade['vipcard'] = $vipid;
		$vipTrade['order_id'] = $order_id;
		$vipTrade['type'] =  $type;
		$vipTrade['source'] =  $source;
		$vipTrade['amount'] = $amount;
        $vipTrade['balance'] = $balance;
        if(!empty($remarks)){
        	$comment=$remarks;
        }else{
            try{
                $comment = 'VIP充值,操作人:' . Yii::app()->user->getId();
            }catch (Exception $e){
                $comment = '重结账，返还扣除费用 单号:' . $order_id;
            }
        }
		$vipTrade['comment'] = $comment;
		$vipTrade['created'] = time();
		$modelTrade->attributes = $vipTrade;
		return $modelTrade->insert();
	}

	/**
	 *  充值卡充值
	 */
	public function vipCardIncome($vipid,$amount,$card_id){
		$return = FALSE;
		$model = $this->getPrimary($vipid);
		$vip = $model->attributes;
		$vip['totelamount'] += $amount;
		$vip['balance'] += $amount;
		$model->attributes = $vip;
		if($model->save()){
			$modelTrade = new VipTrade();
			$vipTrade = $modelTrade->attributes;
			$vipTrade['vipcard'] = $vip['id'];
			$vipTrade['order_id'] = 0;
			$vipTrade['type'] =  VipTrade::TYPE_CARD_INCOME;
			$vipTrade['amount'] = $amount;
            $vipTrade['balance'] = $vip['balance'];
			$vipTrade['comment'] = '充值卡号：'.$card_id.',操作人:' . Yii::app()->user->getId();
			$vipTrade['created'] = time();
			$modelTrade->attributes = $vipTrade;
			$modelTrade->insert();
			$return = TRUE;
		}
		//add vip log
		$vip['description'] = '充值卡充值vip,充值结果'.serialize($return);
		$vip['created'] = time();
		$vip['operator'] = Yii::app()->user->getId();
		//把操作日志给放到异步,不是关键步骤
		$task = array(
			'method'    => 'dump_insert_vip_log',
			'params'    => $vip,
		);
		Queue::model()->putin($task, 'settlement');
		return $return;
	}

	/**
	 * 按卡号查询vip信息 禁用了就查不到信息
	 * @param int $vipcard
	 */
	public function getPrimary($id){
		$criteria = new CDbCriteria();
		$criteria->compare('id', $id);
		$criteria->addCondition('status != :status');
		$criteria->params[':status'] = self::STATUS_DISABLE;
        return self::model()->find($criteria);
	}

	//到写库去拿数据，避免读写不一致导致bug
	public function forceGetPrimary($id){
		return $this->getPrimary($id);
	}

	/**
	 * 按卡号查询vip信息
	 * @param int $vipcard
	 */
	public function getVipInfo($id){
		return self::model()->find("id = :id",
						array(':id' =>$id));
	}

	/**
	 *
	 * 按手机号查询vip信息
	 * @param int $vipcard
	 */
	public function getPrimaryPhone($phone,$status=''){
		$criteria = new CDbCriteria();
		$criteria->compare('phone', $phone);
		if($status !== ''){
			$criteria->addCondition('status != :status');
			$criteria->params[':status'] = $status;
		}
		return self::model()->find($criteria);
	}

	/**
	 *
	 * 修改VIP账户余额,参数为变化金额
	 * @param int $vipcard
	 * @param int $amount
	 */
	public function setBalance($id, $balance) {
		$model = $this->find('id = :id', array(':id'=>$id));
		if($model){
			return $model->updateByPk($id, array (
				'balance'=>$balance
			));
		}
	}

	public function getBalance($id){
		$model = $this->find('id = :id', array(':id'=>$id));
		return empty($model) ? 0 : $model->balance;
	}
	
	public function updateBalance($id, $delta){
		return self::model()->updateCounters(array('balance'=>$delta),'id= :id',array(':id'=>(string)$id));
	}

	/**
	 * 司机台账汇总  vip 总余额
	 */
	public function getVipBalanceTotal($city_id = 0)
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'SUM(balance) AS balance';
		if ($city_id != 0) {
			$criteria->addCondition('city_id = :city_id');
			$criteria->params = array(':city_id' => $city_id);
		}
		$result = Vip::model()->find($criteria);
		return $result;
	}

	public function checkData() {
		if ($this->type==0) {
			$this->addError('type', '请选择VIP卡类型');
			return false;
		}

		if ($this->city_id==0) {
			$this->addError('city_id', '请选择所在城市');
			return false;
		}

		if ($this->balance < 0) {
			$this->addError('totelamount', '充值金额不能小于0.00');
			return false;
		}

		if ($this->status==0) {
			$this->addError('status', '请选择VIP卡状态');
			return false;
		}

		return true;

	}

	/**
	 * 记录viplog日志
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public function vipLog($data){
		$data['created'] = time();
        $phone = trim($data['phone']);
        $send_phone = trim($data['send_phone']);
		$connection = Yii::app()->dbstat;
		$sql = 'INSERT INTO t_vip_log
					(vipcard, name, company, phone, send_phone, email, send_type, type, credit, city_id, totelamount, balance, status, commercial_invoice, remarks, description, operator, created)
				VALUES
					(:vipcard, :name, :company, :phone, :send_phone, :email, :send_type, :type, :credit, :city_id, :totelamount, :balance, :status, :commercial_invoice, :remarks, :description, :operator, :created)';
		$command = $connection->createCommand($sql);
		$command->bindParam(":vipcard", $data['id']);
		$command->bindParam(":name", $data['name']);
		$company = empty($data['company']) ? ' ' : $data['company']; //数据库要求不能为 null
		$command->bindParam(":company", $data['company']);
		$command->bindParam(":phone", $phone);
		$command->bindParam(":send_phone", $send_phone);
		$command->bindParam(":email", $data['email']);
		$command->bindParam(":send_type", $data['send_type']);
		$command->bindParam(":type", $data['type']);
		$command->bindParam(":credit", $data['credit']);
		$command->bindParam(":city_id", $data['city_id']);
		$command->bindParam(":totelamount", $data['totelamount']);
		$command->bindParam(":balance", $data['balance']);
		$command->bindParam(":status", $data['status']);
		$command->bindParam(":commercial_invoice", $data['commercial_invoice']);
		$command->bindParam(":remarks", $data['remarks']);
		$command->bindParam(":description", $data['description']);
		$operator = empty($data['operator']) ? '系统' : $data['operator'];
		$command->bindParam(":operator", $operator);
		$command->bindParam(":created", $data['created']);
		$command->execute();
		$command->reset();
	}

    protected  function makeVipTradeCriteria($params=array()){
        $criteria = new CDbCriteria;
        if (!empty($params)) {
            $items = VipTrade::model()->attributes;
            foreach ($params as $k => $v) {
                if (array_key_exists($k, $items) && !empty($v)) {
                    $criteria->compare($k, $v);
                }
            }
        }
        return $criteria;
    }

    /**
     * 获取vip交易流水数量
     * @param array $params
     * @return int
     */
    public function getVipTradeCount($params=array()){
        $criteria =$this->makeVipTradeCriteria($params);
        $ret=VipTrade::model()->count($criteria);

        return $ret;
    }

    /**
     * 获取主页面
     * @author mengtianxue  修改 libaiyang 方法  2013-05-31
     * @param $vipPhone
     * @param $vip
     * @param $title_month
     * @return string
     */
    public function getMailBody($vipPhone, $vip, $title_month)
    {
        $mailBody = '';
        $mailBody .= '
					<!DOCTYPE html>
					<html lang="en">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>e代驾 - Vip账单列表</title>
					</head>
					<body>
                    <div style="width:1004px; margin:0;padding:0;">
                    <table style="min-height:69px;border:1px rgb(0, 136, 204) solid;border-top-width:6px;font-family:\'微软雅黑\',sans-serif;font-size:14px;"
                           cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                        <tr>
                            <td>
                                <center>
                                    <div style="text-align:left; width:990px;min-height:69px;">
                                        <div style="float:left;height:69px;">
                                            <a href="http://www.edaijia.cn/" target="_blank"
                                               style="display:block;height:42px;width:131px;margin:13px 20px 0;">
                                                <img style="border:none;"
                                                     src="http://www.edaijia.cn/v2/sto/classic/www/images/logo.png" title="e代驾">
                                            </a>
                                        </div>
                                        <table style="float:right;font-size:14px;margin-right:15px;" border="0" cellpadding="0"
                                               cellspacing="0" height="69px">
                                            <tbody>
                                            <tr>
                                                <td width="100px"><strong>VIP服务电话：</strong></td>
                                                <td width="130px">
                                                    010-58690340
                                                </td>

                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </center>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <table style="border:1px rgb(0, 136, 204) solid;border-width:0 1px 0 1px;font-family:\'微软雅黑\',sans-serif;font-size:13px;"
                           cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                    <tr>
                    <td>
                    <center>
                    <div style="width:990px;text-align:left;border-bottom:1px #D9D9D9 dashed; padding-bottom:20px;font-size:13px;">
                    <table cellpadding="5" cellspacing="0" width="990">
                    <tbody>
                    <tr>
                        <td>
                            <div style="border-bottom:1px rgb(0, 136, 204) solid;font-size:18px;line-height:40px;color:rgb(0, 136, 204);">
                                e代驾{title}VIP明细单
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {vip_consume_html}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {vip_recharge_html}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" width="990">
                                <tbody>
                                <tr>
                                    <td style="height:40px;line-height:40px;">
                                        <div style="color:rgb(0, 136, 204);font-size:15px;">
                                            您的账户信息
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table style="text-align:left;border:1px #929292 solid;font-size:13px;"
                                               cellpadding="9" cellspacing="0" width="100%">
                                            <thead>
                                            <tr style="background:#929292;color:#FFF;">
                                                <td width="70%">账户信息项目</td>
                                                <td align="center" width="30%">信息详情</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>VIP号码：</td>
                                                <td align="center">{vip_card}</td>
                                            </tr>
                                            <tr bgcolor="#f6f6f6">
                                                <td>客户姓名：</td>
                                                <td align="center">{customer_name}</td>
                                            </tr>
                                            <tr>
                                                <td>账户余额：</td>
                                                <td align="center">{balance}</td>
                                            </tr>
                                            <tr bgcolor="#f6f6f6">
                                                <td>客户绑定电话：</td>
                                                <td align="center">{customer_tel}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    </tbody>
                    </table>
                    </div>
                    </center>
                    </td>
                    </tr>
                    </tbody>
                    </table>
                    <table style="border:1px rgb(0, 136, 204) solid;border-top:none;font-family:\'微软雅黑\',sans-serif;font-size:12px;line-height:35px;padding-bottom:30px;"
                           cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                        <tr>
                            <td>
                                <center>
                                    <div style="width:990px;text-align:center;">
                                        <table style="color:#989898;margin:0 auto;margin-top:10px;text-align:center;font-size:12px;"
                                               cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                   <p>Copyright@2011-2013 edaijia.cn All Right Reserved <br/>24小时热线：4006-91-3939</p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </center>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>

					</body>
					</html>';

//        $phone = $vip['phone'] . "<br/>";
//        $tphone = '';
//        foreach ( $vipPhone as $list ) {
//            $tphone .= $list ['phone'] . "<br />";
//        }
        $num_phone = count($vipPhone) + 1;
        $phone = $num_phone . "个";
        $customer_name = $vip['name'];
        $vip_card = $vip['id'];
        $balance = $vip['balance'];
        $mailBody = str_replace('{balance}', $balance, $mailBody);
        $mailBody = str_replace('{vip_card}', $vip_card, $mailBody);
        $mailBody = str_replace('{customer_name}', $customer_name, $mailBody);
        $mailBody = str_replace('{customer_tel}', $phone, $mailBody);
        $mailBody = str_replace('{title}', $title_month, $mailBody);
        return $mailBody;
    }


    /**
     * 消费记录转成html
     * @author mengtianxue 2013-05-31
     * @param $data
     * @return string
     */
    public function vipConsumeHtml($data)
    {
        $html = '';

        $html .= '<table cellpadding="0" cellspacing="0" width="990">
                    <tbody>
                    <tr>
                        <td style="height:40px;line-height:40px;">
                            <div style="color:rgb(0, 136, 204);font-size:15px;float:left;">
                                消费明细
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="text-align:center;border:1px #929292 solid;font-size:13px;"
                                   cellpadding="9" cellspacing="0" width="100%">
                                <thead>
                                <tr style="background:#929292;color:#FFF;">
                                    <td style="word-break: keep-all; width: 60px;">订单编号</td>
                                    <td style="word-break: keep-all; width: 80px;">客户信息</td>
                                    <td style="word-break: keep-all; width: 170px;">出发/到达时间</td>
                                    <td style="word-break: break-all; width: 210px;">出发/到达地点</td>
                                    <td style="word-break: keep-all; width: 50px;">总里程</td>
                                    <td style="word-break: keep-all; width: 230px;">收费明细</td>
                                    <td style="word-break: keep-all; width: 60px;">消费金额</td>
                                    <td style="word-break: keep-all; width: 60px;">帐户余额</td>
                                    <td style="word-break: keep-all; width: 80px;">备注</td>
                                </tr>
                                </thead>
                                <tbody>';
        if (!empty($data)) {
            foreach ($data as $item) {

                $phone = $this->getDriverId($item->order_id, "phone");
                $contact_phone = $this->getDriverId($item->order_id, "contact_phone");
                if($phone == $contact_phone){
                    $contact_phone = '';
                }

                $vip_phone = VipPhone::model()->getPrimary($phone);
                $name = $vip_phone['name'];

                $tmp_line = '';
                $tmp_line .= '<tr>
                                    <td style="word-break: keep-all; width: 60px;">' . $item->order_id . '</td>
                                    <td style="word-break: keep-all; width: 80px;">' . $name . '<br />' . $phone . '</td>
                                    <td style="word-break: keep-all; width: 170px;">预约：' . $this->getDriverId($item->order_id, "booking_time") . '<br>出发：' . $this->getDriverId($item->order_id, "start_time") . '<br>到达：' . $this->getDriverId($item->order_id, "end_time") . '</td>
                                    <td style="word-break: break-all; width: 210px;">出发地：' . $this->getDriverId($item->order_id, "location_start") . '<br>目的地：' . $this->getDriverId($item->order_id, "location_end") . '</td>
                                    <td style="word-break: keep-all; width: 50px;">' . $this->getDriverId($item->order_id, "distance") . '公里</td>
                                    <td style="word-break: keep-all; width: 230px;">' . Order::model()->vipListPriceInfo($item->order_id) . '</td>
                                    <td style="text-align:right; word-break: keep-all; width: 60px;">' . -$item->amount . '元</td>
                                    <td style="text-align:right; word-break: keep-all; width: 60px;">' . $item->balance . '元</td>
                                    <td style="word-break: keep-all; width: 80px;">' . $contact_phone . '</td>';
                $tmp_line .= '</tr>';
                $html .= $tmp_line;
            }
        }
        $html .= '</tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>';

        return $html;
    }

    /**
     * 充值记录转成html格式
     * @author mengtianxue 2013-05-31
     * @param $data
     * @return string
     */
    public function vipRechargeHtml($data)
    {
        $html = '';
        if (!empty($data)) {
            $html .= '<table cellpadding="0" cellspacing="0" width="990">
                                <tbody>
                                <tr>
                                    <td style="height:40px;line-height:40px;">
                                        <div style="color:rgb(0, 136, 204);font-size:15px;">
                                            充值明细
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table style="text-align:left;border:1px #929292 solid;font-size:13px;"
                                               cellpadding="9" cellspacing="0" width="100%">
                                            <thead>
                                            <tr style="background:#929292;color:#FFF;">
                                                <td width="25%">日期</td>
                                                <td align="right" width="25%">充值金额</td>
                                                <td align="right" width="25%">充值类型</td>
                                                <td align="right" width="25%">余额</td>
                                            </tr>
                                            </thead>
                                            <tbody>';

            foreach ($data as $item) {

		$tmp_line='';
                $type_name = $item->type == 0 ? "充值" : "充值卡充值";
                $tmp_line .= '<tr>';
                $tmp_line .= '<td style="font-size:12px" >' . date("Y-m-d", $item->created) . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $item->amount . '元</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $type_name . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $item->balance . '元</td>';
                $tmp_line .= '</tr>';
                $html .= $tmp_line;
            }
            $html .= '

                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>';;
        }
        return $html;
    }

    // 获取司机信息
    public function getDriverId($order_id, $field)
    {
        if (!empty ($order_id)) {
            $model = Order::model()->getOrderInfo($order_id);
            if ($model)
                return $model->$field;
        }
        return '';
    }

	/**
	 * 给vip用户返回钱
	 *
	 * @param $cast
	 * @param $vipCard
	 * @param $orderId employeeAccount里面的orderId
	 */
	public function refundOrderCost($cast, $vipCard, $orderId){
		if(empty($vipCard)){
			return;
		}
		$amount = abs($cast);
		$vip 	= Vip::model()->findByPk($vipCard);
		$balance= $vip->balance;
		$ret = $this->updateBalance($vipCard, $amount);
		if($ret == 1){//成功更新,添加账务流水
			$tradeInfo = array(
				'vipcard'		=> $vipCard,
				'order_id'		=> $orderId,
				'amount'		=> $amount,
				'balance'		=> $balance + $amount,
				'comment'		=> '重结账，返还扣除费用 单号:' . $orderId,
				'type'			=> VipTrade::TYPE_INCOME,
				'source'		=> VipTrade::TRANS_SOURCE_D,
			);
			$vipTrade = new VipTrade();
			$amount = $vipTrade->addTrade($tradeInfo);
			if($amount===null || $amount === false){
				EdjLog::info("add trade log fail ".serialize($tradeInfo));
			}
		}
		$format = 'vip 客户 充值返回值|%s|应回退金额|%s|vipCard|%s|orderId|%s|time|%s';
		EdjLog::info(sprintf($format, $ret, $cast, $vipCard, $orderId, date('Y-m-d H:i:s')));
	}

	/**
	 * 根据vip type返回vipid
	 *
	 * @param $vipType
	 * @return array
	 */
	public function getVipIdListByType($vipType){
		$criteria = new CDbCriteria();
		$criteria->compare('type', $vipType);
		$vipList = self::model()->findAll($criteria);
		$idList = array();
		if($vipList){
			foreach($vipList as $vip){
				$idList[] = $vip->id;
 			}
		}
		return $idList;
	}

    /**
     * 更新数据
     */
    public function updatePhoneNum($id, $phonenew){
        $model = $this->find('id = :id', array(':id'=>$id));
        if($model){
            return $model->updateByPk($id, array (
                'phone'=>$phonenew,'status'=>Vip::STATUS_DISABLE
            ));
        }
    }

}
