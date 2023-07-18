<?php
/**
 * 银联支付
 * User: Bidong
 * Date: 13-12-24
 * Time: 下午3:38
 *
 */
require_once("lib/upmp_service.php");
class UpmpPay {

//public static
    const TRANS_TYPE_PUSH="01";     // 交易类型 订单推送
    const TRANS_TYPE_VOID="31";     // 交易类型 消费撤销
    const TRANS_TYPE_REFUND="04";   // 交易类型 退货交易
    const TRANS_TYPE_BINDING='72';  // 建立绑定关系(绑定信用卡)
    const TRANS_TYPE_UNBINDING='74';  // 解除绑定关系(取消绑定)
    const TRANS_TYPE_FIND_BINDING='75';  // 查询绑定关系


    protected $_version='';
    protected $_charset='';
    protected $_merId='';
    protected $_backEndUrl='';
    protected $_frontEndUrl='';

    public function __construct(){

        $this->_version=upmp_config::$version; // 版本号
        $this->_charset=upmp_config::$charset; // 字符编码
        $this->_merId=upmp_config::$mer_id; // 商户代码
        $this->_backEndUrl=Yii::app()->params['payment']['unionPayConfig']['mer_back_end_url'];//upmp_config::$mer_back_end_url; // 通知URL
        $this->_frontEndUrl=upmp_config::$mer_front_end_url; // 前台通知URL(可选)

    }


    /**
     * 订单推送
     * @param  $fee 单位为分
     * @param  $orderTime  date("YmdHis")
     * @param  $trans_order_id  BUpmpPayTrade::model()->makeOrderNo()
     * @param  $orderDes 订单描述
     * @param  $reserved 请求方保留域
     * @param  $trans_type 交易类型
     * @return array()
     * @author bidong
     */
    public  function purchase($fee,$orderTime,$trans_order_id,$order_des,$reserved,$trans_type=self::TRANS_TYPE_PUSH){
        return $this->push($fee,$orderTime,$trans_order_id,$trans_type,$reserved,$order_des);
    }

    /**
     * 消费撤销
     * @param $fee  取消金额
     * @param $orderTime 新的订单时间
     * @param $trans_order_id 新订单号
     * @param $qn   查询流水号（原订单支付成功后获取的流水号
     * @return array
     */
    public function void($fee,$orderTime,$trans_order_id,$qn){
        $trans_type=self::TRANS_TYPE_VOID;
        $order_des='';
        return $this->push($fee,$orderTime,$trans_order_id,$trans_type,array(),$order_des,$qn);
    }

    /**
     * 退货交易
     * @param $fee  金额
     * @param $orderTime 新的订单时间
     * @param $trans_order_id 新订单号
     * @param $qn 查询流水号（原订单支付成功后获取的流水号
     * @return array
     */
    public function refund($fee,$orderTime,$trans_order_id,$qn){
        $trans_type=self::TRANS_TYPE_REFUND;
        $order_des='';
        return $this->push($fee,$orderTime,$trans_order_id,$trans_type,array(),$order_des,$qn);
    }

    private  function push($fee,$orderTime,$trans_order_id,$trans_type,$reserved=array(),$order_des='',$qn=''){

        //需要填入的部分
        $req['version']     		= $this->_version; // 版本号
        $req['charset']     		= $this->_charset; // 字符编码
        $req['transType']   		= $trans_type; // 交易类型
        $req['merId']       		= $this->_merId; // 商户代码
        $req['backEndUrl']      	= $this->_backEndUrl;
        $req['frontEndUrl']     	= $this->_frontEndUrl; // 前台通知URL(可选)
        if($order_des)
            $req['orderDescription'] = $order_des;// 订单描述(可选)
        $req['orderTime']   		= $orderTime; // 交易开始日期时间yyyyMMddHHmmss
        $req['orderTimeout']   		= ""; // 订单超时时间yyyyMMddHHmmss(可选)
        $req['orderNumber'] 		= $trans_order_id; //订单号(商户根据自己需要生成订单号)
        $req['orderAmount'] 		= $fee; // 订单金额 单位分
        $req['orderCurrency'] 		= "156"; // 交易币种(可选)
        if($qn)
            $req['qn'] 				= $qn; // 查询流水号（原订单支付成功后获取的流水号）
        if($reserved)
            $req['reqReserved'] 		= $reserved; // 请求方保留域(可选，用于透传商户信息)

        // 保留域填充方法
//        $merReserved['test']   	= "test";
//        $req['merReserved']   	= UpmpService::buildReserved($merReserved); // 商户保留域(可选)

        $resp = array ();
        $validResp = UpmpService::trade($req,upmp_config::$upmp_trade_url, $resp);

        return array('succ'=>$validResp,'data'=>$resp);
    }

    /**
     * 交易状态查询
     * @param $trans_type  交易类型
     * @param $order_time  商户订单时间
     * @param $order_no    商户订单编号
     * @author bidong
     */
    public function query($trans_type,$order_time,$order_no){

        //需要填入的部分
        $req['version']     	= $this->_version; // 版本号
        $req['charset']     	= $this->_charset; // 字符编码
        $req['transType']   	= $trans_type; // 交易类型
        $req['merId']       	= $this->_merId; // 商户代码
        $req['orderTime']   	= $order_time; // 交易开始日期时间yyyyMMddHHmmss或yyyyMMdd
        $req['orderNumber'] 	= $order_no; // 订单号

        $resp = array ();
        $validResp = UpmpService::query($req, $resp);

        $ret=array();
        // 商户的业务逻辑
        if ($validResp){
            // 服务器应答签名验证成功
            $ret=array('succ'=>1,'data'=>$resp);
        }else {
            // 服务器应答签名验证失败
            $ret=array('succ'=>0,'data'=>$resp);
        }
        return $ret;
    }

    /**
     * 检查交易是否成功
     * @param $trans_type  交易类型
     * @param $order_time  商户订单时间
     * @param $order_no    商户订单编号
     * @author dengxiaoming
     */
    public function query0($trans_type,$order_time,$order_no) {
	$result=$this->query($trans_type,$order_time,$order_no);
	if($result['succ']) {
		$respCode=$result['data']['respCode'];
                $transStatus=isset($result['data']['transStatus'])?$result['data']['transStatus']:'';
		if($respCode=='00' && $transStatus=='00') {
			return true;
		}
	}
	
	return false;
    }



    /**
     * 交易结果通知
     * @param $post_data
     * @return array(succ=>'',data=>'',msg=>'')
     * @author bidong
     */
    public function notify($post_data){

        $ret=array('succ'=>0,'data'=>$post_data,'msg'=>'');
        if (UpmpService::verifySignature($post_data)){// 服务器签名验证成功
            //请在这里加上商户的业务逻辑程序代码
            //获取通知返回参数，可参考接口文档中通知参数列表(以下仅供参考)
            //版本号	 version 1.0.0
            //签名方法	signMethod
            //签名信息	signature
            //交易类型	transType
            //商户代码	merId
            //交易状态	transStatus    00:交易成功结束
            //响应码	     respCode
            //查询流水号	 qn
            //商户订单号	 orderNumber
            $transStatus = $post_data['transStatus'];// 交易状态
            $transType=$post_data['transType'];// 交易类型
            if ($transStatus!='' && "00"==$transStatus){
                // 交易处理成功
                $ret=array('succ'=>1,'data'=>$post_data,'transType'=>$transType,'msg'=>'交易成功');
            }else {
                $ret=array('succ'=>0,'data'=>$post_data,'msg'=>'交易失败');
            }
        }else {// 服务器签名验证失败
            $ret=array('succ'=>0,'data'=>$post_data,'msg'=>'银联服务器签名验证失败');
        }

        return $ret;
    }

}
