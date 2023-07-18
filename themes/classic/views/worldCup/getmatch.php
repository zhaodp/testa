<?php
$source=$_GET['source'];//发起人手机号
$source=strrev($source);
$source=$source-01234567899;

//$db=new mysqli("db.edaijia.cn","write","write","db_car");
//$db=new mysqli("db.edaijia.cn","edaijia","XpMfGYWFbAvaYQyb","db_car");
$db=new mysqli("masterdb.edaijia.cn","sp_car_master","uMTNwWqnqjt5CKPa","db_car");
if (mysqli_connect_errno()){
	 $mess = array('code'=> -1 ,'message' => '网络异常，请稍后再试');
	 $json_str=json_encode($mess);
         echo $json_str;
	 exit;
}else{

        $sql ='select * from t_worldcup_setting where begin_time>=now() order by begin_time asc  limit 1';
	$result = $db->query($sql);
	$num_results = $result->num_rows;
	if($num_results==0){
		$mess = array('code'=> -2 ,'message' => '今日比赛竞猜已结束，敬请期待下个比赛日竞猜。');
         	$json_str=json_encode($mess);
		$result->free();
                $db->close();
         	echo $json_str;
         	exit;
	}
	$sql1 = 'select count(*) as num from t_worldcup_quiz where source='.$source.' and created>(select begin_time from t_worldcup_setting where begin_time<now() order by begin_time desc limit 1)';

        $result1 = $db->query($sql1);
        $row1 = $result1->fetch_assoc();
        $num=$row1['num'];
        if($num>=5){
                $result1->free();
                $db->close();
                $mess = array('code'=> 3 ,'message' => '对不起，参赛人数已达到限制');
                $json_str=json_encode($mess);
                echo $json_str;
                exit;
        }

	for($i=0;$i<1;$i++){
		$row = $result->fetch_assoc();
		$begin_time=$row['begin_time'];
		$begin_time=substr($begin_time,6,10);
		$begin_time=str_replace('-','月',$begin_time);
		$begin_time=str_replace(' ','日 ',$begin_time);
		$mess = array('code'=> 0 ,'message' => '获取成功','setting_id'=>$row['id'],
			      'country_1'=>$row['country_1'],'country_2'=>$row['country_2'],'begin_time'=>$begin_time);
                $json_str=json_encode($mess);
		$result->free();
                $db->close();
                echo $json_str;
		exit;
	}
}?>
