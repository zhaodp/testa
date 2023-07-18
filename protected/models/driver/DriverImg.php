<?php

/**
 * This is the model class for table "{{driver_img}}".
 *
 * The followings are the available columns in table '{{driver_img}}':
 * @property string $driver_id
 * @property string $bin_data
 * @property string $filesize
 * @property string $filetype
 * @property integer $version
 */
class DriverImg extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverImg the static model class
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
		return '{{driver_img}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, bin_data, filesize, filetype, version', 'required'),
			array('version', 'numerical', 'integerOnly'=>true),
			array('driver_id', 'length', 'max'=>20),
			array('filesize, filetype', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('driver_id, bin_data, filesize, filetype, version', 'safe', 'on'=>'search'),
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
			'driver_id' => 'Driver',
			'bin_data' => 'Bin Data',
			'filesize' => 'Filesize',
			'filetype' => 'Filetype',
			'version' => 'Version',
		);
	}
	
	public function uploadImg($city_id, $driver_id, $image){
		$file_dir = $city_id . '/' . $driver_id . '/';
		if (!file_exists(IMAGE_ASSETS . $city_id . '/'))
			mkdir(IMAGE_ASSETS . $city_id);
		if (!file_exists(IMAGE_ASSETS . $file_dir))
			mkdir(IMAGE_ASSETS . $file_dir);
		$file_dir = IMAGE_ASSETS . $file_dir;
		
		$picture_address = SP_URL_DRIVER_IMG . $city_id . '_' . $driver_id . '_middle.jpg';
		
		$driverImgVersion = DriverImg::model()->findBySql('SELECT version FROM t_driver_img WHERE driver_id=:driver_id ORDER BY version DESC', array (
						':driver_id'=>$driver_id));
		
		$driverImg = new DriverImg();
		$bin_data = addslashes(fread(fopen($image->tempName, "r"), filesize($image->tempName)));
		$driverImgAttr = array (
			'driver_id'=>$driver_id, 
			'bin_data'=>$bin_data, 
			'filesize'=>$image->size, 
			'filetype'=>$image->type, 
			'version'=>1);
		
		if (isset($driverImgVersion->version))
			$driverImgAttr['version'] = $driverImgVersion->version + 1;
		
		$driverImg->attributes = $driverImgAttr;
		$driverImg->save();
		
		$image->saveAs($file_dir . $driver_id . '.jpg');
		
		Yii::app()->thumb->load($file_dir . $driver_id . '.jpg')->resize(120, 144)->save($file_dir . "small.jpg", "JPG");
		Yii::app()->thumb->load($file_dir . $driver_id . '.jpg')->resize(160, 192)->save($file_dir . "middle.jpg", "JPG");
		Yii::app()->thumb->load($file_dir . $driver_id . '.jpg')->resize(560, 672)->save($file_dir . "normal.jpg", "JPG");
	    $upload_model = new UpyunUpload('edriver');
        $pic_address = $file_dir . $driver_id . '.jpg';
        $is_upload = $upload_model->driverPicUpload($city_id, $driver_id, $pic_address);
		//return $picture_address;
        if ($is_upload) {
            $new_pic_url = Driver::getPictureUrl($driver_id, $city_id);
            $driver_model = Driver::getProfile($driver_id);
            $driver_model->picture = $new_pic_url;
            $driver_model->save();
            return Driver::getPictureUrl($driver_id, $city_id);
        } else {
            return false;
        }
	}
	
	public function getLastVersion($driverID){
		$criteria=new CDbCriteria;
		
		$criteria->select = 'version';
		$criteria->order = 'version DESC';
		$criteria->condition = 'driver_id=:driver_id';
		$criteria->params = array (':driver_id'=>$driverID);
		
		return DriverImg::model()->find($criteria)->version;
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

		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('bin_data',$this->bin_data,true);
		$criteria->compare('filesize',$this->filesize,true);
		$criteria->compare('filetype',$this->filetype,true);
		$criteria->compare('version',$this->version);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}