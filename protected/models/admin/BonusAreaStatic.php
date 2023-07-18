<?php

/**
 * This is the model class for table "{{bonus_area_static}}".
 *
 * The followings are the available columns in table '{{bonus_area_static}}':
 * @property integer $id
 * @property integer $bonus_type_id
 * @property integer $bonus_sn
 * @property string $operator
 * @property integer $created
 */
class BonusAreaStatic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BonusAreaStatic the static model class
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
		return '{{bonus_area_static}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bonus_type_id, bonus_sn, operator, created', 'required'),
			array('bonus_type_id, bonus_sn, created', 'numerical', 'integerOnly'=>true),
			array('operator', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, bonus_type_id, bonus_sn, operator, created', 'safe', 'on'=>'search'),
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
			'bonus_type_id' => '优惠码类型',
			'bonus_sn' => '优惠码号码',
			'operator' => '操作人',
			'created' => '添加时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($pageSize=10)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('bonus_type_id',$this->bonus_type_id);
		$criteria->compare('bonus_sn',$this->bonus_sn);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
				'pagination' => array (
						'pageSize' => $pageSize
				)
		));
	}
	
	/**
	 * 将数据生成excel
	 */
	public function down_xls($data, $keynames, $name='dataxls')
	{
		$xls[] = "<html><meta http-equiv=content-type content=\"text/html; charset=UTF-8\"><body><table border='1'>";
		$xls[] = "<tr><td>ID</td><td>" . implode("</td><td>", array_values($keynames)) . '</td></tr>';
		$index = 0;
		foreach($data As $o) {
			$line = array(++$index);
			foreach($keynames AS $k=>$v) {
				$line[] = $o[$k];
			}
			$xls[] = '<tr><td>'. implode("</td><td>", $line) . '</td></tr>';
		}
		$xls[] = '</table></body></html>';
		$xls = join("\r\n", $xls);
		header('Content-Disposition: attachment; filename="'.$name.'.xls"');
		die(mb_convert_encoding($xls,'UTF-8','UTF-8'));
	}
	
}