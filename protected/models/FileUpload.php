<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 14-7-5
 * Time: 17:12
 */

class FileUpload extends  CModel{
	private $file = '';

	public $source = 1;


	public static function model($className = __CLASS__){
		return parent::model(__CLASS__);
	}

	public function attributeNames(){
		return array(
			'file'		=> $this->file,
			'source'	=> $this->source,
		);
	}

} 