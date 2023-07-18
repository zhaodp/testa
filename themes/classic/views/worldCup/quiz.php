<?php
$source=$_GET['source'];//发起人手机号
$source=strrev($source);
$source=$source-01234567899;

$view=$_GET['view'];
//$db=new mysqli("db.edaijia.cn","write","write","db_car");
//$db=new mysqli("db.edaijia.cn","edaijia","XpMfGYWFbAvaYQyb","db_car");
$db=new mysqli("masterdb.edaijia.cn","sp_car_master","uMTNwWqnqjt5CKPa","db_car");
if(mysqli_connect_errno()){
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
	if($view==0){
		$name=$_GET['name'];
		$telephone=$_GET['telephone'];
		$score_1=$_GET['score_1'];
		$score_2=$_GET['score_2'];

                $sql1 = 'select * from t_worldcup_quiz where telephone='.$telephone.' and  created>(select begin_time from t_worldcup_setting where begin_time<now() order by begin_time desc limit 1)';

        	$result1 = $db->query($sql1);
        	$num_results1 = $result1->num_rows;
        	if($num_results1>0){
			$result1->free();
                        $db->close();
                	$mess = array('code'=> 1,'message' => '您已竞猜过这场比赛');
                	$json_str=json_encode($mess);
                	echo $json_str;
                	exit;
        	}else{
                	$sql1 = 'insert into t_worldcup_quiz(name,telephone,score_1,score_2,source) values(?,?,?,?,?)';
                	$stmt=$db->prepare($sql1);
                	$stmt->bind_param("ssiis",$name,$telephone,$score_1,$score_2,$source);
                	$stmt->execute();
                	$stmt->close();
        	}
	}
        for($i=0;$i<1;$i++){
                $row = $result->fetch_assoc();
                $begin_time=$row['begin_time'];
                $begin_time=substr($begin_time,6,10);
                $begin_time=str_replace('-','月',$begin_time);
                $begin_time=str_replace(' ','日 ',$begin_time);
                $country_1=$row['country_1'];
		$country_2=$row['country_2'];
		$begin_time=$begin_time;
        }
	
		$sql ='select name,score_1,score_2 from t_worldcup_quiz where created>(select begin_time from t_worldcup_setting where begin_time<now() order by begin_time desc limit 1) and source='.$source.' order by id desc';

        $result = $db->query($sql);
        $num_results = $result->num_rows;
	$datas=array();
	
	if($num_results>0){
		for($i=0;$i<$num_results;$i++){
			$row = $result->fetch_assoc();
			$data=array();
			$data['name']=$row['name'];
			$data['score_1']=$row['score_1'];
			$data['score_2']=$row['score_2'];	
			$datas[$i]=$data;
		}
                $result->free();
                $db->close();
        }
	$mess = array('code'=> 0 ,'message' => '成功','country_1'=>$country_1,'country_2'=>$country_2,'begin_time'=>$begin_time,'data' => $datas);
	$json_str=json_encode($mess);
	echo $json_str;
}
?>
