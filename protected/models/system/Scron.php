<?php
/**
 * This is the model class for table "{{crontab}}".
 *
 * The followings are the available columns in table '{{crontab}}':
 * @property integer $cronId
 * @property string $task
 * @property integer $active
 * @property string $mhdmd
 * @property string $command
 * @property string $params
 * @property string $process
 * @property integer $runAt
 * @property string $host
 */
class Scron extends CActiveRecord
{

        public $min = "*/1";//1-59
        public $hour = "*";//1-23
        public $day = "*";//1-91
        public $month = "*";//1-12
        public $week = "*";//0-6

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return Crontab the static model class
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
                return '{{crontab}}';
        }
		
        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
                // NOTE: you should only define rules for those attributes that
                // will receive user inputs.
                return array(
                        array('task,min,hour,day,month,week, mhdmd,command,host,user,owner', 'required'),
                        array('active,timeout,isQueue' ,'numerical', 'integerOnly'=>true),
                        array('task,mhdmd,callback,command, params, process,logFile', 'length', 'max'=>255),
                        array('owner','length','max'=>64),
                        // The following rule is used by search().
                        // Please remove those attributes that should not be searched.
                        array('cronId, task, active, mhdmd, command, params, process, runAt,host,root,logFile,owner', 'safe', 'on'=>'search'),
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
                        'cronId' => '编号',
                        'task' => '任务名称',
                        'active' => '状态',
                        'host'=>'主机IP',
                        'mhdmd' => '任务执行规律',
                        'command' => '命令',
                        'params' => '参数',
                        'process' => '单机最大进程数',
                        'runAt' => '执行时间',
                        'min'=>'分',//1-59
                        'hour'=>'小时',//1-23
                        'day'=>'日',//1-91
                        'month'=>'月',//1-12
                        'week'=>'星期',
                        'isQueue'=>'是否是队列',
            			'timeout'=>'超时时间(单位：分钟)',
            			'callback'=>'超时回调脚本',
						'logFile'=>'日志文件名',
                        'user'=>'使用者',
                        'owner'=>'责任人',
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

                $criteria->compare('cronId',$this->cronId);
                $criteria->compare('task',$this->task,true);
                $criteria->compare('host',$this->host);
                $criteria->compare('active',$this->active);
                $criteria->compare('mhdmd',$this->mhdmd,true);
                $criteria->compare('command',$this->command,true);
                $criteria->compare('params',$this->params,true);
                $criteria->compare('process',$this->process,true);
                $criteria->compare('runAt',$this->runAt);
                $criteria->compare('user',$this->user,true);
                $criteria->compare('owner',$this->owner,true);
                return new CActiveDataProvider($this, array(
                        'criteria'=>$criteria,
                ));
        }
		
		public function getDbConnection()
		{
			self::$db=Yii::app()->dbsys;
			return self::$db;
		}

        public function restDbConnection()
        {
            self::$db=Yii::app()->db;
        }

        public function getHostName($host){
            $ret = ScronHost::model()->getStartHost();
            return isset($ret[$host]) ? $ret[$host] : "";
        }

        public function getLogDomain($host) {
            return "joblog." . $this->getHostName($host) .'.edaijia.cn';
        }

        public function getCron(){
            return $this->mhdmd ." " . $this->command . " " . $this->params;
        }

        public function getActiveName(){
            $font = '<span style="color:%s;">%s</span>';
            $color = $this->active==1?"green":"red";
            $name = $this->active==1?"已激活":"未激活";
            return sprintf($font,$color,$name) ;
        }

}
