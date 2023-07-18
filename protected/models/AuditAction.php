<?php

/**
 * This is the model class for table "{{audit_action}}".
 *
 * The followings are the available columns in table '{{audit_action}}':
 * @property integer $id
 * @property integer $action_id
 * @property integer $status
 * @property string $total_amount
 * @property string $params
 * @property string $operator
 * @property string $create_time
 * @property string $update_time
 */
class AuditAction extends CActiveRecord
{
	public static $redisKeyPre = 'FINANCE_AUDIT_';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{audit_action}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('action_id, status', 'numerical', 'integerOnly'=>true),
			array('total_amount', 'length', 'max'=>50),
			array('params', 'length', 'max'=>500),
			array('operator', 'length', 'max'=>32),
			array('create_time, update_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, action_id, status, total_amount, params, operator, create_time, update_time', 'safe', 'on'=>'search'),
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
			'id' => 'id',
			'action_id' => '业务ID,t_admin_action 主键',
			'status' => '资源状态，1：正常，0：禁用',
			'total_amount' => '审核总金额',
			'params' => '资源对应的参数，json格式',
			'operator' => '添加人姓名',
			'create_time' => '创建时间',
			'update_time' => '更新时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('action_id',$this->action_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('total_amount',$this->total_amount,true);
		$criteria->compare('params',$this->params,true);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->dbadmin;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AuditAction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * 获取审核来源
	 */
	public static function getAuditType(){
		$auditType = array();
		$sql = "select distinct(action_id) from t_audit_action,t_audit_auditor where t_audit_action.id=t_audit_auditor.audit_id and t_audit_auditor.auditor=:auditor";
		$command = Yii::app()->dbadmin->createCommand($sql);
		$name = Yii::app()->user->name;
		$command->bindParam(":auditor",$name);
		$list = $command->queryAll();
		foreach( $list as $audit ){
			$id = $audit['action_id'];
			$action = AdminActions::model()->findByPk($id);
			$auditType[$id] = $action->name;
		}
		return $auditType;
	}

	/**
	 * 获取配置参数
	 */
	public static function getAuditParams($id){
		$audit = AuditAction::model()->findByPk($id);
		if( isset($audit->params) ){
			return $audit->params;
		}
		return '';
	}
}