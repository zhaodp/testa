<?php
/**
 * User: cuiluzhe
 * Date: 14-07-17
 * Time: 下午13:29
 */
class ExportAction extends CAction{
    public function run(){
	$param = array();
        if(isset($_GET['support_ticket_id'])){
                $param['support_ticket_id'] = $_GET['support_ticket_id'];
        }
        if(isset($_GET['driver_id'])){
                $param['driver_id'] = $_GET['driver_id'];
        }   
        if(isset($_GET['city_id'])){
                $param['city_id'] = $_GET['city_id'];
        }   
        if(isset($_GET['status'])){
                $param['status'] = $_GET['status'];
        }     
        if(isset($_GET['create_user'])){
                $param['create_user'] = $_GET['create_user'];
        } 
        if(isset($_GET['deal_user'])){  
                $param['deal_user'] = $_GET['deal_user'];
        } 
        if(isset($_GET['create_time_begin'])){
                $param['create_time_begin'] = $_GET['create_time_begin'];
        } 
        if(isset($_GET['create_time_end'])){
                $param['create_time_end'] = $_GET['create_time_end'];
        } 
        if(isset($_GET['feec_time_begin'])){
                $param['feec_time_begin'] = $_GET['feec_time_begin'];
        }
        if(isset($_GET['feec_time_end'])){
                $param['feec_time_end'] = $_GET['feec_time_end'];
        }
        if(isset($_GET['feed_time_begin'])){
                $param['feed_time_begin'] = $_GET['feed_time_begin'];
        }
        if(isset($_GET['feed_time_end'])){
                $param['feed_time_end'] = $_GET['feed_time_end'];
        }
        $fees = SupportTicketFee::model()->getSupportTicketFeeForExport($param);
        $filename = 'fee' . time() . '.csv';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        Header('Accept-Ranges: bytes');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        $headerArr = array();
        $headerArr['support_ticket_id'] = mb_convert_encoding('工单ID', 'gb2312', 'UTF-8');
        $headerArr['city_id'] = mb_convert_encoding('城市', 'gb2312', 'UTF-8');
	$headerArr['type'] = mb_convert_encoding('类型', 'gb2312', 'UTF-8');
        $headerArr['class'] = mb_convert_encoding('分类', 'gb2312', 'UTF-8');
	$headerArr['content'] = mb_convert_encoding('内容', 'gb2312', 'UTF-8');
        $headerArr['driver_id'] = mb_convert_encoding('司机工号', 'gb2312', 'UTF-8');
	$headerArr['total'] = mb_convert_encoding('补偿金额', 'gb2312', 'UTF-8');
        $headerArr['status'] = mb_convert_encoding('状态', 'gb2312', 'UTF-8');
	$headerArr['create_user'] = mb_convert_encoding('创建人', 'gb2312', 'UTF-8');
        $headerArr['create_time'] = mb_convert_encoding('创建时间', 'gb2312', 'UTF-8');
	$headerArr['deal_user'] = mb_convert_encoding('处理人', 'gb2312', 'UTF-8');
        $headerArr['deal_time'] = mb_convert_encoding('处理时间', 'gb2312', 'UTF-8');
        $header =  implode(',', $headerArr) . "\n";
        echo $header;
	foreach($fees as $fee){
		   $data =  $fee['id'].',';
		   $data .= mb_convert_encoding(Dict::item("city",$fee['city_id']),'gb2312', 'UTF-8').',';
		   $data .= mb_convert_encoding(Dict::item("ticket_category",$fee['type']),'gb2312', 'UTF-8').',';
		   $class = $fee['class']=='0'?'':SupportTicketClass::model()->findByPk($fee["class"])->name;
	   	   $data .= mb_convert_encoding($class, 'gb2312', 'UTF-8').','; 
		   $data .= mb_convert_encoding($fee['content'], 'gb2312', 'UTF-8').',';
		   $data .= $fee['driver_id'].','; 
		   $data .= $fee['total'].',';
		   $status = $fee['status']=='1'?"未处理":"已处理";
		   $data .= mb_convert_encoding($status, 'gb2312', 'UTF-8').',';
		   $data .= mb_convert_encoding($fee['create_user'], 'gb2312', 'UTF-8').',';
		   $data .= mb_convert_encoding($fee['create_time'], 'gb2312', 'UTF-8').',';
		   $data .= mb_convert_encoding($fee['deal_user'], 'gb2312', 'UTF-8').',';
		   $data .= $fee['deal_time'].',';
		   $data .= "\n";
		   echo $data;
	}
    }
}
