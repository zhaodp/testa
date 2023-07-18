<?php
	  $path = isset($params['path']) ? $params['path'] : '';
    $encrypt = isset($params['encrypt']) ? $params['encrypt'] : 0; //是否需要解密，0不需要，1需要
    $encrypt = 0;
    $tmp_path  = "/tmp/";//接收文件目录  
    $target_path = $tmp_path.($_FILES['file']['name']);
    $target_path = iconv("UTF-8","gb2312", $target_path);

    $target_path_encode = $tmp_path.($_FILES['file']['name']).'.encode';
    $target_path_encode = iconv("UTF-8","gb2312", $target_path_encode);

    if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path_encode)) {  
       if($encrypt){
         //对target_path文件进行解密
         $key = "edaijia15101061387";
         $crypt = new DES($key);
         $handle = @fopen($target_path, "r");
         if ($handle) {
              while (!feof($handle)) {
                  $line = fgets($handle, 4096);
                  //解密
          		    $line = $crypt->decrypt($crypt->encrypt($line));
          		    //写入文件,追加方式
          		    file_put_contents($target_path, $line, FILE_APPEND);
              }
              fclose($handle);
         }
      }else{
        $target_path = $target_path_encode;
      }
	   //上传到又拍云
       $upload_model = new UpyunUpload('etrack');
       $res = $upload_model->uploadFileForDebugLog($target_path, 'debug_log/'.$path, $_FILES['file']['name'],'etrack'); 
       if($res){
	       	$ret = array(
	        'code' => 0,
	        'message' => "The file ".( $_FILES['file']['name'])." has been uploaded,path=".$path,
	    	);
	    	echo json_encode($ret);
       }else{
			$ret = array(
	        'code' => 2,
	        'message' => '上传到又拍云失败',
	    	);
	    	echo json_encode($ret);
       }
    }else{ 
    	$ret = array(
	        'code' => 2,
	        'message' => "There was an error uploading the file, please try again! Error Code: ".$_FILES['file']['error'],
	    	);
	    echo json_encode($ret); 
    }

?>
