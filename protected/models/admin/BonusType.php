<?php

/**
 * This is the model class for table "{{bonus_type}}".
 *
 * The followings are the available columns in table '{{bonus_type}}':
 * @property string $id
 * @property string $name
 * @property integer $money
 * @property string $channel
 * @property string $type
 * @property string $sn_start
 * @property string $issued
 * @property string $sn_end
 * @property integer $end_date
 * @property integer $is_limited
 * @property string $create_by
 * @property integer $created
 * @property string $update_by
 * @property integer $updated
 * @property string $remark
 */
class BonusType extends CActiveRecord {
	
	const BONUS_TYPE_ERROR_MSG = '此优惠券无效';
	const BONUS_TYPE_FRESH_MSG = '此优惠券仅限新用户使用';
	const BONUS_TYPE_APP_FRESH_MSG = '此优惠券仅限APP新用户使用';
	const BONUS_TYPE_APP_MSG = '此优惠券仅限APP用户使用';
	const BONUS_OWN_USED_MSG = '您已绑定过此优惠券';
	const BONUS_TYPE_STALE_MSG = '此优惠券仅限老用户使用';
	const BONUS_USED_MSG = '此优惠券已绑定';
	const BONUS_PHONE_USED_MSG = '此手机号已绑定过优惠券';
	const BONUS_MAX_USED_MSG = '此优惠券已经达到绑定最大次数';
	/**
	 * 单次使用
	 */
	const BONUS_LIMITED = 1;
	
	/**
	 * 多次使用
	 */
	const BONUS_UNLIMITED = 0;
	
	/**
	 * 充值码不限用户使用次数限制
	 */
	const BONUS_TYPE_UNLIMIT = 0;
	
	/**
	 * 充值码限新用户可重复使用
	 */
	const BONUS_TYPE_UNLIMIT_FRESH = 1;
	
	/**
	 * 充值码限新用户不可重复使用
	 */
	const BONUS_TYPE_LIMIT_FRESH = 2;
	
	/**
	 * 充值码限老用户可重复使用
	 */
	const BONUS_TYPE_UNLIMIT_STALE = 3;
	
	/**
	 * 充值码限老用户不可重复使用
	 */
	const BONUS_TYPE_LIMIT_STALE = 4;
	
	/**
	 * 充值码不限用户单次使用
	 */
	const BONUS_TYPE_UNLIMIT_ONCE = 5;
	
	/**
	 * 充值码限App新用户可重复使用
	 */
	const BONUS_TYPE_LIMIT_APP_FRESH = 6;
	
	/**
	 * 充值码限App用户可重复使用
	 */
	const BONUS_TYPE_LIMIT_APP = 7;
	
	/**
	 * 邀请码
	 */
	const BONUS_CHANNEL_INVITE = 1;
	/**
	 * 充值码
	 */
	const BONUS_CHANNEL_CHARGE = 0;
	/**
	 * 优惠码类型:区域码
	 */
	const BONUS_TYPE_AREA = 0;
	/**
	 * 优惠码类型:固定码
	 */
	const BONUS_TYPE_FIXED = 1;
	/**
	 * 优惠码类型:固定区域码
	 */
	const BONUS_TYPE_FIXED_AREA = 2;
	/**
	 * Returns the static model of the specified AR class.
	 * 
	 * @param string $className
	 *        	active record class name.
	 * @return BonusType the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model ( $className );
	}
	
	/**
	 *
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{bonus_type}}';
	}
	
	/**
	 *
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
				array (
						'name, channel, type, sn_type, sn_start, sn_end, money, end_date, is_limited',
						'required' 
				),
				array (
						'money, end_date, is_limited, created, updated, issued',
						'numerical',
						'integerOnly' => true 
				),
				array (
						'name',
						'length',
						'max' => 60 
				),
				array (
						'channel, type, sn_type',
						'length',
						'max' => 11 
				),
				array (
						'sn_start, sn_end, create_by, update_by',
						'length',
						'max' => 32 
				),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('remark',	'safe'),
				array (
						'id, name, money, channel, type, sn_start, sn_end, end_date, is_limited, create_by, created, update_by, updated',
						'safe',
						'on' => 'search' 
				) 
		);
	}
	
	/**
	 *
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 *
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
				'id' => 'ID',
				'name' => '券名称',
				'money' => '金额',
				'channel' => '渠道',
				'type' => '类型',
				'sn_type' => '号码类型',
				'sn_start' => 'Sn 起始',
				'sn_end' => 'Sn 截止',
				'issued' => '发行数量',
				'end_date' => '停用日期',
				'is_limited' => '使用限制',
				'create_by' => '创建人',
				'created' => '创建日期',
				'update_by' => '更新人',
				'updated' => '更新日期',
				'remark' => '备注' 
		);
	}
	
	public function beforeSave() {
		if (parent::beforeSave ()) {
			if ($this->isNewRecord) {
				$this->create_by = Yii::app ()->user->getId ();
				$this->created = time ();
			}
			
			$this->updated = time ();
			$this->update_by = Yii::app ()->user->getId ();
			
			return true;
		}
		return parent::beforeSave ();
	}
	/*
	 * 自动生成指定数量优惠码
	 */
	public function afterSave() {
		if ($this->getIsNewRecord ()) {
			// 如果当前为固定区域，则生成指定区域数量
			if ($this->sn_type == self::BONUS_TYPE_FIXED_AREA) {
				$start = $this->sn_start;
				$end = $this->sn_end;
				$num = $this->issued;
				$this->createAreaBonus ( $start, $end, $num );
			}
		}
		parent::afterSave ();
	}
	
	/*
	 * 生成指定优惠码
	 */
	public function createAreaBonus($start, $end, $num) {
		$sn_start = $start;
		$sn_end = $end;
		$count_code_num = $num;
		$curr_count = 0;
		$code_arr = array ();
		$bonus_type_id = self::BONUS_TYPE_FIXED_AREA;
		$operator = Yii::app ()->user->getId ();
		$bonus_type_id = Yii::app ()->db->getLastInsertID ();
		
		while ( $curr_count < $count_code_num ) {
			$tmp_code = rand ( $sn_start, $sn_end );
			$end_char_code = Helper::CheckCode ( $tmp_code );
			if (strlen ( $end_char_code ) == 0 || $end_char_code == '') {
				continue;
			}
			$end_code = $tmp_code . $end_char_code;
			if (! in_array ( $end_code, $code_arr )) {
				$code_arr [] = $end_code;
				$insert_arr = array ();
				$insert_arr ['bonus_type_id'] = $bonus_type_id;
				$insert_arr ['bonus_sn'] = $end_code;
				$insert_arr ['operator'] = $operator;
				$insert_arr ['created'] = time ();
				Yii::app ()->db->createCommand ()->insert ( 't_bonus_area_static', $insert_arr );
				
				unset ( $insert_arr, $end_code );
				$curr_count ++;
			}
			unset ( $tmp_code, $end_code );
		}
	}
	
	/*
	 * 检查优惠码是否存在
	 */
	public function checkBonusExist($bonusCode) {
		$select = Yii::app ()->db_readonly->createCommand ()
		->select ( 'id' )
		->from ( 't_bonus_area_static' )
		->where ( 'bonus_sn=:bonus_sn', array (':bonus_sn' => $bonusCode ) )
		->queryScalar ();
		
		if ($select) {
			return true;
		}
		return false;
	}
	
	/**
	 *
	 *
	 * 验证客户提交的优惠券是否能被使用
	 * 
	 * @param string $bonus        	
	 * @param string $phone        	
	 *
	 */
	public static function validateBonus($bonus, $phone, $type = 0) {
		
		$valiateRet = self::validCode ( $bonus );
		// print_r($valiateRet);
		if ($valiateRet ['code'] == - 1) {
			return $valiateRet;
		} else {
			if (CustomerBonus::existsCustomerPhone ( $phone )) {
				return array (
						'code' => - 1,
						'message' => self::BONUS_PHONE_USED_MSG 
				);
			}
			
			if ($valiateRet ['typeId'] == CustomerInvite::BONUS_TYPE_ID) {
				$countInvite = CustomerBonus::model ()->getCountBonusUsed ( $valiateRet ['bonusString'], $valiateRet ['parityBit'] );
				if ($countInvite >= CustomerInvite::MAX_INVITE_COUNT) {
					return array (
							'code' => - 1,
							'message' => self::BONUS_MAX_USED_MSG 
					);
				}
			}
			
			switch ($valiateRet ['limit']) {
				case self::BONUS_TYPE_UNLIMIT_FRESH :
					if (Order::getOrderCountByCustomerPhone ( $phone ) > 0) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_TYPE_FRESH_MSG 
						);
					}
					if (CustomerBonus::existsCustomerBonusByType ( $valiateRet ['typeId'], $phone )) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_OWN_USED_MSG 
						);
					}
					break;
				case self::BONUS_TYPE_LIMIT_FRESH :
					if (Order::getOrderCountByCustomerPhone ( $phone ) > 0) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_TYPE_FRESH_MSG 
						);
					}
					if (CustomerBonus::existsCustomerBonus ( $valiateRet ['bonusString'] )) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_USED_MSG 
						);
					}
					break;
				case self::BONUS_TYPE_UNLIMIT_STALE :
					if (Order::getOrderCountByCustomerPhone ( $phone ) < 1) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_TYPE_STALE_MSG 
						);
					}
					if (CustomerBonus::existsCustomerBonusByType ( $valiateRet ['typeId'], $phone )) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_OWN_USED_MSG 
						);
					}
					break;
				case self::BONUS_TYPE_LIMIT_STALE :
					if (Order::getOrderCountByCustomerPhone ( $phone ) < 1) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_TYPE_STALE_MSG 
						);
					}
					if (CustomerBonus::existsCustomerBonus ( $valiateRet ['bonusString'] )) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_USED_MSG 
						);
					}
					break;
				case self::BONUS_TYPE_UNLIMIT_ONCE :
					if (CustomerBonus::existsCustomerBonus ( $valiateRet ['bonusString'] )) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_USED_MSG 
						);
					}
					break;
				case self::BONUS_TYPE_UNLIMIT :
					if (CustomerBonus::existsCustomerBonusWithPhone ( $valiateRet ['bonusString'], $phone )) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_OWN_USED_MSG 
						);
					}
					break;
				case self::BONUS_TYPE_LIMIT_APP_FRESH :
					if (Order::getAPPOrderCountByCustomerPhone ( $phone ) > 0) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_TYPE_APP_FRESH_MSG 
						);
					}
					if (CustomerBonus::existsCustomerBonusByType ( $valiateRet ['typeId'], $phone )) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_OWN_USED_MSG 
						);
					}
					break;
				case self::BONUS_TYPE_LIMIT_APP :
					if (CustomerBonus::existsCustomerBonusByType ( $valiateRet ['typeId'], $phone )) {
						return array (
								'code' => - 1,
								'message' => self::BONUS_OWN_USED_MSG 
						);
					}
					break;
			}
			
			return $valiateRet;
		}
	}
	
	/**
	 * 校验优惠码是否有效
	 *
	 * @param string $bonus        	
	 */
	public static function validCode($bonus) {
		$json = array ();
		$json ['code'] = - 1;
		$json ['message'] = self::BONUS_TYPE_ERROR_MSG;
		
		$valid = false;
		
		// 判断是不是固定码
		$criteria = new CDbCriteria ();
		$criteria->addCondition ( ':bonus BETWEEN sn_start AND sn_end and sn_type=:sn_type' );
		$criteria->params = array (
				':bonus' => $bonus,
				':sn_type' => self::BONUS_TYPE_FIXED
		);
		$bonusType = self::model ()->find ( $criteria );
		if ($bonusType && $bonusType->end_date > time()) {
			// 唯一固定码
			if ($bonusType->sn_start == $bonusType->sn_end) {
				$valid = true;
			} else {
				// 司机对应的固定码
				// 判断是不是存在这个司机的工号
				if (strlen ( $bonus ) == 6) {
					$city_id = substr ( $bonus, 0, 2 );
					$driver_id = substr ( $bonus, 2, 4 );
					
					$cityPrefix = Dict::items ( "bonus_city" );
					$driver_id = $cityPrefix [$city_id] . $driver_id;
					
					$criteria = new CDbCriteria ();
					$criteria->addCondition ( 'user=:user' );
					$criteria->params = array (
							':user' => $driver_id 
					);
					$user = Driver::model ()->find ( $criteria );
					if ($user) {
						$valid = true;
					}
				
				}
			}
			if ($valid) {
				$json ['code'] = 1;
				$json ['typeId'] = $bonusType->id;
				$json ['type'] = $bonusType->type;
				$json ['sn_type'] = $bonusType->sn_type;
				$json ['name'] = $bonusType->name;
				$json ['money'] = $bonusType->money;
				$json ['bonusString'] = $bonus;
				$json ['parityBit'] = 0;
				$json ['limit'] = $bonusType->is_limited;
			}
		} else {
			$parityBit = substr ( $bonus, - 1 );
			$bonusString = substr ( $bonus, 0, strlen ( $bonus ) - 1 );
			
			if ($parityBit == Helper::CheckCode ( $bonusString )) {
				// 判断是否是区域码
				$criteria = new CDbCriteria ();
				$criteria->addCondition ( ':bonus BETWEEN sn_start AND sn_end and sn_type=:sn_type' );
				$criteria->params = array (
						':bonus' => $bonusString,
						':sn_type' => self::BONUS_TYPE_FIXED_AREA 
				);
				$bonusType = self::model ()->find ( $criteria );

				if ($bonusType && $bonusType->end_date > time()) {
					// 确定是区域码,调用检查code
					if (self::model()->checkBonusExist ( $bonus )) {
						$json ['code'] = 1;
						$json ['typeId'] = $bonusType->id;
						$json ['type'] = $bonusType->type;
						$json ['sn_type'] = $bonusType->sn_type;
						$json ['name'] = $bonusType->name;
						$json ['money'] = $bonusType->money;
						$json ['bonusString'] = $bonusString;
						$json ['parityBit'] = $parityBit;
						$json ['limit'] = $bonusType->is_limited;
					}
				} else {
					$criteria = new CDbCriteria ();
					$criteria->addCondition ( ':bonus BETWEEN sn_start AND sn_end' );
					$criteria->params = array (
							':bonus' => $bonusString 
					);
					$bonusType = self::model ()->find ( $criteria );
					
					if ($bonusType && $bonusType->end_date > time()) {
						$json ['code'] = 1;
						$json ['typeId'] = $bonusType->id;
						$json ['type'] = $bonusType->type;
						$json ['sn_type'] = $bonusType->sn_type;
						$json ['name'] = $bonusType->name;
						$json ['money'] = $bonusType->money;
						$json ['bonusString'] = $bonusString;
						$json ['parityBit'] = $parityBit;
						$json ['limit'] = $bonusType->is_limited;
					}
				}
			} 
		}
		return $json;
	}
	
	public function getBonusTypes() {
		$bonusTypes = BonusType::model ()->findAll ( 'end_date > :end_date', array (
				':end_date' => time () 
		) );
		
		$bonusTypesArray = array ();
		
		foreach ( $bonusTypes as $bonusType ) {
			$bonusTypesArray [$bonusType ['id']] = $bonusType ['name'];
		}
		
		return $bonusTypesArray;
	}
	
	public static function getBonusType($type_id) {
		$bonusType = BonusType::model ()->find ( 'id = :type_id', array (
				':type_id' => $type_id 
		) );
		return $bonusType;
	}
	
	public function getBonusName($type_id) {
		$bonusType = BonusType::model ()->find ( 'id = :type_id', array (
				':type_id' => $type_id 
		) );
		if ($bonusType)
			return $bonusType->name;
		return '';
	}
	
	public function getBonusNameByOrder($order_id) {
		$customerBonus = CustomerBonus::model ()->find ( 'order_id=:order_id', array (
				'order_id' => $order_id 
		) );
		if ($customerBonus) {
			$bonus_type_id = $customerBonus->bonus_type_id;
			return BonusType::model ()->getBonusName ( $bonus_type_id );
		} else {
			return '未知优惠券';
		}
	}
	
	public function getBonusCountByHand($bonus_sn, $driver_id, $source = 2) {
                $sql = "SELECT order_id FROM t_customer_bonus WHERE bonus_type_id = 8 AND bonus_sn=:bonus_sn";
		$tmp = Yii::app()->db_finance->createCommand($sql, array(
                    ':bonus_sn' => $bonus_sn
		))->quertAll();
		if(empty($tmp)) {
		    return 0;
		}

		$order_ids = array();
		foreach($tmp as $item) {
		    $order_ids[] = $item['order_id'];
		}

		$sql = "SELECT count(order_id) FROM t_order WHERE order_id IN ("
                    .join(',', $order_ids)
                    .") source IN (:source) AND driver_id=:driver_id AND status IN (1, 4)";
		$usedNum = Order::getDbReadonlyConnection()->createCommand($sql, array(
                    ':source' => $source,
                    ':driver_id' => $driver_id 
		))->queryScalar();

		return $usedNum;
	}
	
	public function getBonusCountByCallType($bonus_sn, $driver_id, $call_type = 0) {
                $sql = "SELECT order_id FROM t_customer_bonus WHERE bonus_type_id = 8 AND bonus_sn=:bonus_sn";
		$tmp = Yii::app()->db_finance->createCommand($sql, array(
                    ':driver_id' => $driver_id
		))->quertAll();
		if(empty($tmp)) {
		    return 0;
		}

		$order_ids = array();
		foreach($tmp as $item) {
		    $order_ids[] = $item['order_id'];
		}
		$sql = "SELECT imei FROM t_order WHERE order_id IN ("
                    .join(',', $order_ids)
                    .") source=:source AND driver_id=:driver_id AND status IN (1, 4)";
		$tmp = Order::getDbReadonlyConnection()->createCommand($sql, array(
                    ':source' => $source
		))->queryAll();
		if(empty($tmp)) {
		    return 0;
		}

		$order_imeis = array();
		foreach($tmp as $item) {
		    $order_imeis[] = $item['imei'];
		}
		$sql = "SELECT count(imei) FROM t_call_history WHERE imei IN ("
                    .join(',', $imeis)
                    .") AND t_call_history.duration < 10 and t_call_history.type=:type";
		$usedNum = Order::getDbReadonlyConnection()->createCommand($sql, array(
                    ':type' => $call_type
		))->queryScalar();

		return $usedNum;
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * 
	 * @return CActiveDataProvider the data provider that can return the models
	 *         based on the search/filter conditions.
	 */
	public function search($pageSize = 10) {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		
		$criteria = new CDbCriteria ();
		
		$criteria->compare ( 'id', $this->id, true );
		$criteria->compare ( 'name', $this->name, true );
		$criteria->compare ( 'money', $this->money );
		$criteria->compare ( 'channel', $this->channel, true );
		$criteria->compare ( 'type', $this->type, true );
		$criteria->compare ( 'sn_type', $this->sn_type, true );
		$criteria->compare ( 'sn_start', $this->sn_start, true );
		$criteria->compare ( 'sn_end', $this->sn_end, true );
		$criteria->compare ( 'issued', $this->issued, true );
		$criteria->compare ( 'end_date', $this->end_date );
		$criteria->compare ( 'is_limited', $this->is_limited );
		$criteria->compare ( 'create_by', $this->create_by, true );
		$criteria->compare ( 'created', $this->created );
		$criteria->compare ( 'update_by', $this->update_by, true );
		$criteria->compare ( 'updated', $this->updated );
		
		return new CActiveDataProvider ( $this, array (
				'criteria' => $criteria,
				'pagination' => array (
						'pageSize' => $pageSize 
				) 
		) );
	}
	
	public function getStaticBonus() {
		$arrId = array ();
		$bonusType = self::model ()->findAll ( 'sn_type = :sn_type', array (
				':sn_type' => self::BONUS_TYPE_FIXED
		) );
		if ($bonusType) {
			foreach ( $bonusType as $type ) {
				array_push ( $arrId, $type->id );
			}
		}
		return $arrId;
	}
	
	public function getAllBonusByLimit($limit) {
		$arrId = array ();
		$bonusType = self::model ()->findAll ( 'is_limited = :limit', array (
				':limit' => $limit 
		) );
		if ($bonusType) {
			foreach ( $bonusType as $type ) {
				array_push ( $arrId, $type->id );
			}
		}
		return $arrId;
	
	}

}
