<?php

class ShortUrlController extends CController {

    public function actionRe($h) {
        $long_url = ShortUrl::model()->long_url($h);
        if ($long_url) {
            $this->redirect($long_url, true, 302);
        } else {
            header("HTTP/1.0 404 Not Found");

            $html = "<html>".
                "<head><title>404 Not Found</title></head>".
                "<body bgcolor='white'>".
                "<center><h1>404 Not Found</h1></center>".
                "</body>".
                "</html>";
            echo($html);

        }

      //echo "<pre>short url test $h</pre>\n";
      // http://www.waisir.com/t.cn/
     // $this->redirect('http://www.edaijia.cn/v2/index.php?r=site/login', true, 302);
      //throw new CHttpException(404, 'The requested page does not exist.');
    }
}
