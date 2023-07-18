<?php
/**
 * 官网API：验证码接口
 * @author cuiluzhe 2014-09-16
 */
    $key = isset($params['key']) ? $params['key'] : '';
    if( empty($key) ){
        $ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
        echo json_encode($ret);return ;
    }
    $code = "";
    for ($i = 0; $i < 4; $i++) {
        $code .= rand(0, 9);
    }

    RVerifyCode::model()->set($key, $code);
    
    //4位验证码也可以用rand(1000,9999)直接生成 
    //将生成的验证码写入session，备验证时用 
    //创建图片，定义颜色值 
    header("Content-type: image/PNG");
    $im = imagecreate(60, 20);
    $black = imagecolorallocate($im, 0, 0, 0);
    $gray = imagecolorallocate($im, 200, 200, 200);
    $bgcolor = imagecolorallocate($im, 255, 255, 255);
    //填充背景 
    imagefill($im, 0, 0, $gray);

    //画边框 
    imagerectangle($im, 0, 0, 60-1, 20-1, $black);

    //随机绘制两条虚线，起干扰作用 
    $style = array ($black,$black,$black,$black,$black,
        $gray,$gray,$gray,$gray,$gray
    );
    imagesetstyle($im, $style);
    $y1 = rand(0, 20);
    $y2 = rand(0, 20); 
    $y3 = rand(0, 20); 
    $y4 = rand(0, 20); 
    imageline($im, 0, $y1, 60, $y3, IMG_COLOR_STYLED);
    imageline($im, 0, $y2, 60, $y4, IMG_COLOR_STYLED);
           
    //在画布上随机生成大量黑点，起干扰作用; 
    for ($i = 0; $i < 80; $i++) { 
        imagesetpixel($im, rand(0, 60), rand(0, 20), $black);
    } 
    //将数字随机显示在画布上,字符的水平间距和位置都按一定波动范围随机生成 
    $strx = rand(3, 8);
    for ($i = 0; $i < 4; $i++) {
        $strpos = rand(1, 6);
        imagestring($im, 5, $strx, $strpos, substr($code, $i, 1), $black);
        $strx += rand(8, 12); 
    }
    imagepng($im);//输出图片  
    imagedestroy($im);//释放图片所占内存 
    return;
?>
~                                             
