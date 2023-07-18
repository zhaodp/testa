<?php

/**
 * This is the model class for table "{{customer_invoice}}".
 *
 * The followings are the available columns in table '{{customer_invoice}}':
 * @property integer $id
 * @property string $customer_phone
 * @property string $title
 * @property string $content
 * @property string $contact
 * @property string $address
 * @property string $zipcode
 * @property string $telephone
 * @property integer $status
 * @property integer $isdeal
 * @property string $description
 * @property integer $created
 * @property integer $updatetime
 */
class CustomerInvoice extends FinanceActiveRecord
{
	const TYPE_DAIJIA = 1;
	const TYPE_SERVICE = 2;
	const TYPE_UNDETERMIND = 3;
	static $type = array(
		self::TYPE_DAIJIA => '代驾服务费',
		self::TYPE_SERVICE => '服务费',
		self::TYPE_UNDETERMIND => '待定',
	);
	//发票申请来源 app/400/vip申请
	const SRC_APP = 0;
	const SRC_400 = 1;
	const SRC_VIP = 2;
	static $src = array(
		self::SRC_APP => 'APP',
		self::SRC_400 => '400',
		self::SRC_VIP => 'VIP',
	);

	//支付邮费类型
	const PAY_TYPE_E = 1;//e币
	const PAY_TYPE_DELIVERY = 2;//快递到付
	const PAY_TYPE_NOKNOWN = 3;//待
	const PAY_TYPE_NOCHARGE = 4;//满500免邮
	static $pay_type = array(
                self::PAY_TYPE_E => '500E币支付',
                self::PAY_TYPE_DELIVERY => '到付',
            //    self::PAY_TYPE_NOKNOWN => '待定',
		self::PAY_TYPE_NOCHARGE => '满500免邮',
        );
	
	static $delivery = array(
        '1'=>'韵达',
        '2'=>'顺丰',
        '3'=>'EMS',
        '4'=>'宅急送',
        '5'=>'申通',
        '6'=>'中通',
        '7'=>'圆通',
        '8'=>'天天',
        '9'=>'全峰',
        '10'=>'星辰急便',
        '11'=>'百世汇通',
	);

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerInvoice the static model class
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
		return '{{customer_invoice}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('customer_phone, title, contact, address,telephone, created, updatetime,type,pay_type,src', 'required'),
			array('status, created, updatetime, isdeal,src,finance_confirm,deliveryer,finance_confirm_time,export,export_time', 'numerical', 'integerOnly'=>true),
			array('customer_phone', 'length', 'max'=>12),
			array('title, content', 'length', 'max'=>200),
			array('contact', 'length', 'max'=>100),
			array('address', 'length', 'max'=>500),
			array('zipcode', 'length', 'max'=>6),
			array('telephone', 'length', 'max'=>100),
			array('invoice_number,delivery_number,client_amount', 'length', 'max'=>50),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, customer_phone, title, content, contact, address, zipcode, telephone, status, isdeal, description, created, updatetime,type,src', 'safe', 'on'=>'search'),
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
			'customer_phone' => '客户电话',
			'title' => '抬头',
			'content' => 'Content',
			'contact' => '收件人',
			'address' => '收件地址',
			'zipcode' => 'Zipcode',
			'telephone' => '收件人电话',
			'status' => 'Status',
			'isdeal' => '状态',
			'description' => 'Description',
			'created' => 'Created',
			'updatetime' => 'Updatetime',
			'remark' => '备注',
			'type' => '类型',
			'src'  => '来源',
			'pay_type' => '邮费支付方式',
			'invoice_number' =>'发票号',
			'delivery_number' =>'快递单号',
			'deliveryer' =>'快递公司',
			'client_amount' =>'申请金额',
			'export' =>'导出状态',
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
		$criteria->compare('id',$this->id);
		$criteria->compare('customer_phone',$this->customer_phone,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('isdeal',$this->isdeal);
		$criteria->compare('description',$this->description,true);
        $criteria->order = 'isdeal asc,updatetime desc';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 统计发票申请数量 （可按照时间统计）
     * @param string $start_time
     * @param string $end_time
     * @return mixed
     */
    public function getInvoiceCount($start_time = '',$end_time = '')
    {
        $where = '1';
        $invoiceTotalAmount = 0;
        $params = array();
        if ($start_time) {
            $where .= " and created >= :start_time";
            $params[':start_time'] = $start_time;
        }
        if ($end_time) {
            $where .= " and created <= :end_time";
            $params[':end_time'] = $end_time;
        }
        $invoiceCount = Yii::app()->db_finance->createCommand()
            ->select("count(id) as count,sum(case when ( (client_amount=0.00 and isdeal=1) or (client_amount>0 and (finance_confirm=1 or confirm=2))) then 1 else 0 end) as isdeal,sum(case when ( (client_amount=0.00 and isdeal=1) or (client_amount>0 and (finance_confirm=1 or confirm=2))) then total_amount else 0 end) as sum ")
            ->from('{{customer_invoice}}')
            ->where($where, $params)
            ->queryRow();
        $invoiceCount_arr['total'] = isset($invoiceCount['count']) ? $invoiceCount['count'] : 0;
        $invoiceCount_arr['dealed'] = isset($invoiceCount['isdeal']) ? $invoiceCount['isdeal'] : 0;
        $invoiceCount_arr['nodeal'] = $invoiceCount_arr['total'] - $invoiceCount_arr['dealed'];
        $invoiceCount_arr['invoiceTotalAmount'] = isset($invoiceCount['sum']) ? $invoiceCount['sum'] : 0;
        return $invoiceCount_arr;
    }


   public function getCustomerInvoiceList($data){
        $criteria = new CDbCriteria();
        if(isset($data['title'])){
            $criteria->compare('title', $data['title'],true);
        }
        if(isset($data['customer_phone'])){
            $criteria->compare('customer_phone', $data['customer_phone']);
        }
        if(isset($data['contact'])){
            $criteria->compare('contact', $data['contact']);
        }
        if(isset($data['telephone'])){
            $criteria->compare('telephone', $data['telephone']);
        }
        if(isset($data['isdeal'])){
            $criteria->compare('isdeal', $data['isdeal']);
        }
        if(isset($data['confirm'])){
            $criteria->compare('confirm', $data['confirm']);
        }
        if(isset($data['finance_confirm'])){
            if($data['finance_confirm'] == 0){
                $criteria->compare('confirm', 1);
            }
            $criteria->compare('finance_confirm', $data['finance_confirm']);
        }
        if(isset($data['times'])){
            if($data['times'] != -1){
                $criteria->compare('times', $data['times']);
            }
        }
        if(isset($data['export'])){
            if($data['export'] != -1){
                $criteria->compare('export', $data['export']);
            }
        }
        if(isset($data['pay_type'])){
            if($data['pay_type'] != 0){
                if($data['pay_type'] == 1){//已支付
                    $criteria->addCondition(" pay_type= " .CustomerInvoice::PAY_TYPE_E . " or pay_type= " .CustomerInvoice::PAY_TYPE_NOCHARGE);
                }else if($data['pay_type'] == 2){//未支付
                    $criteria->compare('pay_type', CustomerInvoice::PAY_TYPE_DELIVERY);
                }else{//待定
                    $criteria->compare('pay_type', CustomerInvoice::PAY_TYPE_NOKNOWN);
                }
            }
        }
       if(isset($data['src']) && $data['src'] != 0){
           if($data['src'] == 1){//非创建vip是生成的申请
               $criteria->addInCondition('src', array(0,1));
           }else{
               $criteria->compare('src', 2);
           }
       }
        if (isset($data['start_time']) && isset($data['end_time'])) {
            $criteria->addCondition("updatetime >= " . $data['start_time'] . " and updatetime <= " . $data['end_time']);
        }
        $criteria->order = 'client_amount desc,isdeal asc,updatetime desc';
        return new CActiveDataProvider('CustomerInvoice', array(
            'pagination' => array(
                'pageSize' => 50
            ),
            'criteria' => $criteria));
  }
	
      public function dealCustomerInvoice($id){
          $sql = "UPDATE t_customer_invoice SET isdeal=1,updatetime=:updatetime, deal_time=:deal_time where id=:id";
          $command = Yii::app()->db_finance->createCommand($sql);
          $updatetime = time();
          $command->bindParam(":updatetime", $updatetime);
          $command->bindParam(":deal_time", $updatetime);
          $command->bindParam(":id", $id);
          $command->execute();
     }
      public function dealCustomerInvoiceSetAmount($id,$total_amount){
          $sql = "UPDATE t_customer_invoice SET total_amount=:total_amount,updatetime=:updatetime,isdeal=1, deal_time=:deal_time where id=:id";
          $command = Yii::app()->db_finance->createCommand($sql);
          $command->bindParam(":id", $id);
          $updatetime = time();
          $command->bindParam(":updatetime", $updatetime);
          $command->bindParam(":total_amount", $total_amount);
          $command->bindParam(":deal_time", $updatetime);
          $command->execute();    
     }
     public function getInvoiceList($customer_phone, $pageSize, $pageNumber){
            $criteria = new CDbCriteria();
            $criteria -> select = '*';
            $criteria -> condition = 'customer_phone = :customer_phone';

	    $criteria->offset = $pageNumber*$pageSize;//第一页是0
	    $criteria->limit  = $pageSize;

            $criteria -> order = 'id desc';
            $criteria ->params = array (':customer_phone'=>$customer_phone);
            $result = CustomerInvoice::model()->findAll($criteria);
            return $result;
    }

    public function getInvoiceListSize($customer_phone){
            $criteria = new CDbCriteria();
            $criteria -> select = 'id';
            $criteria -> condition = 'customer_phone = :customer_phone';
            $criteria ->params = array (':customer_phone'=>$customer_phone);
            $result = CustomerInvoice::model()->findAll($criteria);
	    if($result){
		return count($result);
	    }
            return 0;
    }


     public function getInvoiceApplyStatics($start_time = '',$end_time = ''){
         $where = '1';
         $invoiceTotalAmount = 0;
         $params = array();
         if ($start_time) {
             $where .= " and created >= :start_time";
             $params[':start_time'] = $start_time;
         }
         if ($end_time) {
             $where .= " and created <= :end_time";
             $params[':end_time'] = $end_time;
         }
         $statics = Yii::app()->db_finance->createCommand()
             ->select("sum(case when src=1 then 1 else 0 end) as web,sum(case when src=0 then 1 else 0 end) as app,sum(case when src=2 then 1 else 0 end) as vip,created")
             ->from('{{customer_invoice}}')
             ->where($where, $params)
             ->queryRow();
         $data = array();
         $data['web'] = isset($statics['web']) ? $statics['web'] : 0;
         $data['app'] = isset($statics['app']) ? $statics['app'] : 0;
         $data['vip'] = isset($statics['vip']) ? $statics['vip'] : 0;
         $data['created'] = isset($statics['created']) ? $statics['created'] : 0;

         $where = ' confirm=1 ';
         $params = array();
         if ($start_time) {
             $where .= " and confirm_time >= :start_time";
             $params[':start_time'] = $start_time;
         }
         if ($end_time) {
             $where .= " and confirm_time <= :end_time";
             $params[':end_time'] = $end_time;
         }
         $statics = Yii::app()->db_finance->createCommand()
             ->select("count(*) as confirm")
             ->from('{{customer_invoice}}')
             ->where($where, $params)
             ->queryRow();
         $data['confirm'] = isset($statics['confirm']) ? $statics['confirm'] : 0;

         $where = ' (finance_confirm=1 or export=1)';
         $params = array();
         if ($start_time) {
             $where .= " and (finance_confirm_time >= :start_time or export_time >= :export_begin_time)";
             $params[':start_time'] = $start_time;
             $params[':export_begin_time'] = $start_time;
         }
         if ($end_time) {
             $where .= " and (finance_confirm_time <= :end_time or export_time <= :export_end_time)";
             $params[':end_time'] = $end_time;
             $params[':export_end_time'] = $end_time;
         }
         $statics = Yii::app()->db_finance->createCommand()
             ->select("count(*) as finance_confirm")
             ->from('{{customer_invoice}}')
             ->where($where, $params)
             ->queryRow();
         $data['finance_confirm'] = isset($statics['finance_confirm']) ? $statics['finance_confirm'] : 0;
         return $data;
     }

     public function getInvoiceNotDealStatics(){
         $statics = Yii::app()->db_finance->createCommand()
             ->select("sum(case when confirm=0 then 1 else 0 end) as not_confirm,sum(case when confirm=1 and finance_confirm=0 and export=0 then 1 else 0 end) as finance_not_confirm,sum(case when confirm=2 then 1 else 0 end) as cancel")
             ->from('{{customer_invoice}}')
             ->queryRow();
         $statics['not_confirm'] = isset($statics['not_confirm']) ? $statics['not_confirm'] : 0;
         $statics['finance_not_confirm'] = isset($statics['finance_not_confirm']) ? $statics['finance_not_confirm'] : 0;
         $statics['cancel'] = isset($statics['cancel']) ? $statics['cancel'] : 0;
         return $statics;
     }

    /**
     * 获取vip累计申请数(只取客服未确认的)
     * @return mixed
     */
    public function getVipInvoiceStatics(){
        $phones = Yii::app()->db_finance->createCommand()
            ->select("customer_phone")
            ->from('{{customer_invoice}}')
            ->where('confirm=:confirm', array(':confirm'=>0))
            ->queryAll();
        //$phones = self::model()->findAll('confirm=:confirm', array(':confirm'=>0));
        if(!$phones){
            return 0;
        }
        $vip_num = 0;
        foreach($phones as $phone){
            if(CustomerMain::model()->isVip($phone)){
                $vip_num++;
            }
        }
        return $vip_num;
    }

     public function invoiceConsumeHtml($datas){
         $html = '<table cellpadding="0" cellspacing="0" width="990">
                            <tbody>
                            <tr>
                                <td style="height:40px;line-height:40px;text-align:center">
                                    <div style="color:rgb(0, 136, 204);font-size:15px;">
                                        E代驾最新30日开票明细单
                                    </div>
                                </td>
                            </tr>
				<tr><td>(注:当前财务待开票数是指客服已确认但财务未处理的发票数目,未处理完毕数是指没有走完整个开票流程的发票数目)</td></tr>
                                <tr>
                                    <td>
                                        <table style="text-align:left;border:1px #929292 solid;font-size:13px;"
                                               cellpadding="9" cellspacing="0" width="100%">
                                            <thead>
                                            <tr style="background:#5577AA;color:#FFF;">
						                    <td width="10%">日期</td>
                                                <td align="right" width="9%">当日400新增发票申请数</td>
                                                <td align="right" width="9%">当日APP新增发票申请数</td>
                                                <td align="right" width="9%">当日vip申请生成发票数</td>
                                                <td align="right" width="9%">当日客服已确认数</td>
                                            <td align="right" width="9%">当日财务已开票数</td>
                                            <td align="right" width="9%">当前客服待确认数</td>
                                                <td align="right" width="9%">当前财务待开票数</td>
                                                <td align="right" width="9%">最近七天未处理完毕数</td>
                                                <td align="right" width="9%">超过7天未处理完毕数</td>
                                                <td align="right" width="9%">总取消数</td>
                                            </tr>
                                            </thead>
                                            <tbody>';
         if ($datas) {
             foreach ($datas as $data) {
                 $total = $data->web + $data->app + $data->vip;
                 $web_rate = $total == 0 ? '0.00' : number_format(($data->web / $total) * 100, 2);
                 $app_rate = $total == 0 ? '0.00' : number_format(($data->app / $total) * 100, 2);
                 $vip_rate = $total == 0 ? '0.00' : number_format(($data->vip / $total) * 100, 2);
                 $tmp_line = '';
                 $tmp_line .= '<tr>';
                 $tmp_line .= '<td style="font-size:12px" >' . date("Y-m-d", $data->created - 86400) . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->web . '(' . $web_rate . '%)' . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->app . '(' . $app_rate . '%)' . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->vip . '(' . $vip_rate . '%)' . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->confirm . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->finance_confirm . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->not_confirm . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->finance_not_confirm . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->not_complate_in_sevenday . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->not_complate_out_sevenday . '</td>';
                 $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->cancel . '</td>';
                 $tmp_line .= '</tr>';
                 $html .= $tmp_line;
             }

         }
         $html .= '

                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>';
         return $html;
    }
    //获取7天内未处理完毕数和7天外未处理完毕数
    public function getSevenDaysStatics($state){
        $count_time = strtotime(date('Y-m-d 07:00', time() - 3600 * 24 * 7));
        if ($state == 'in') {
            $where = ' confirm !=2 and (finance_confirm=0 and export=0) and created>=:count_time';
        } else {
            $where = ' confirm !=2 and (finance_confirm=0 and export=0) and created<=:count_time';
        }
        $params = array();
        $params[':count_time'] = $count_time;

        $statics = Yii::app()->db_finance->createCommand()
            ->select("count(*) as finance_not_confirm")
            ->from('{{customer_invoice}}')
            ->where($where, $params)
            ->queryRow();
        $data['finance_not_confirm'] = isset($statics['finance_not_confirm']) ? $statics['finance_not_confirm'] : 0;
        return $data;
    }

    //获取vip7天内和7天外申请数
    public function getVipSevenDaysStatics($state){
        $count_time = strtotime(date('Y-m-d 07:00', time() - 3600 * 24 * 7));
        if ($state == 'in') {
           // $phones = self::model()->findAll('confirm=:confirm and created>=:count_time', array(':confirm'=>0,':count_time'=>$count_time));
            $phones = Yii::app()->db_finance->createCommand()
                ->select("customer_phone")
                ->from('{{customer_invoice}}')
                ->where('confirm=:confirm and created>=:count_time', array(':confirm'=>0,':count_time'=>$count_time))
                ->queryAll();
        } else {
            //$phones = self::model()->findAll('confirm=:confirm and created<=:count_time',array(':confirm'=>0,':count_time'=>$count_time));
            $phones = Yii::app()->db_finance->createCommand()
                ->select("customer_phone")
                ->from('{{customer_invoice}}')
                ->where('confirm=:confirm and created<=:count_time', array(':confirm'=>0,':count_time'=>$count_time))
                ->queryAll();
        }
        if(!$phones){
            return 0;
        }
        $vip_num = 0;
        foreach($phones as $phone){
            if(CustomerMain::model()->isVip($phone)){
                $vip_num++;
            }
        }
        return $vip_num;
    }
}
