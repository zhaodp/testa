<?php

class ImageController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout = '//layouts/column1';

    public function actionImgupload(){
        $url=array();
        $url['base_path'] = $_GET['base_path'];
        $url['flash_dir']=$url['base_path']."/flash";
        $url['img_dir'] = $url['base_path']."/img";
        $url['get_type'] = $_GET['type'];
        $url['CKEditorFuncNum'] = $_GET['CKEditorFuncNum'];
        $url['upload'] = $_FILES['upload'];
        $url['call_back_self'] = isset($_GET['call_back_self']) ? $_GET['call_back_self'] : 0;
        $url['call_back_fun'] = isset($_GET['call_back_fun']) ? $_GET['call_back_fun'] : '';
        $this->imgupload($url);
    }
    /**
     * config 上传图片配置文件
     * Enter description here ...
     * @param unknown_type $url
     */
    public function imgupload($url){
        $config=array();
        $config['type']=array("flash","img"); //上传允许type值
        $config['img']=array("jpg","jpeg","bmp","gif","png");
        $config['flash']=array("flv","swf");
        $config['flash_size']=2000;
        $config['img_size']=2000;
        $config['message']="上传成功";
        $config['name']=mktime();
        $config['flash_dir']=$url['flash_dir'];
        $config['img_dir']=$url['img_dir'];
        $config['site_url']=SP_URL_HOME;
        $config['get_type'] = $url['get_type'];
        $config['base_path'] = $url['base_path'];
        $config['CKEditorFuncNum'] = $url['CKEditorFuncNum'];
        $config['upload'] = $url['upload'];
        $config['call_back_self'] = $url['call_back_self'];
        $config['call_back_fun'] = $url['call_back_fun'];
        $this->uploadfile($config);
    }

    public function uploadfile($config)
    {
        //判断是否是非法调用
        if(empty($config['CKEditorFuncNum']))
            $this->mkhtml(1,"","错误的功能调用请求");
        $fn=$config['CKEditorFuncNum'];
        if(!in_array($config['get_type'],$config['type']))
            $this->mkhtml(1,"","错误的文件调用请求");
        $type = $config['get_type'];
        $base_path = $config['base_path'];
        if(is_uploaded_file($config['upload']['tmp_name']))
        {
            //判断上传文件是否允许
            $filearr=pathinfo($config['upload']['name']);
            $filetype=strtolower($filearr["extension"]);
            if(!in_array($filetype,$config[$type]))
                $this->mkhtml($fn,"","错误的文件类型！",$config['call_back_self'], $config['call_back_fun']);
            //判断文件大小是否符合要求
            if($config['upload']['size']>$config[$type."_size"]*1024)
                $this->mkhtml($fn,"","上传的文件不能超过".$config[$type."_size"]."KB！",$config['call_back_self'], $config['call_back_fun']);
            $filename = IMAGE_ASSETS.$config[$type."_dir"];

//            //判断图片长宽不能大于手机屏幕480*900
//            if($type == 'img'){
//                $str = getimagesize($config['upload']['tmp_name']);
//                $mode="/width=\"(.*)\" height=\"(.*)\"/";
//                preg_match($mode,$str[3],$arr);
//                if($arr[1]>400 || $arr[2]>900)
//                    $this->mkhtml($fn,"","上传的图片不能大于400*900");
//            }
            if(!file_exists($filename))
                mkdir($filename,0777,true);

            $file_abso="images/".$config[$type."_dir"]."/".$config['name'].".".$filetype;
            $file_host=IMAGE_ASSETS.$config[$type."_dir"]."/".$config['name'].".".$filetype;

            if(move_uploaded_file($config['upload']['tmp_name'],$file_host))
            {
                $upload_model = new UpyunUpload('edaijia');

                $is_upload = $upload_model->edaijiaPicUpload($base_path, $config['name'], $file_host);

                //$update_cache = $upload_model->updateDriverPicCache($model->user, $model->city_id);
                $pic_url = $is_upload ? Knowledge::getPictureUrl($base_path, $config['name']) : '';
                $this->mkhtml($fn,$pic_url,$config['message'], $config['call_back_self'], $config['call_back_fun']);
            }
            else
            {
                $this->mkhtml($fn,"","文件上传失败，请检查上传目录设置和目录读写权限", $config['call_back_self'], $config['call_back_fun']);
            }
        }
    }
    public function mkhtml($fn,$fileurl,$message, $call_back_self=0, $call_back_fun=null)
    {
        if ($call_back_self && $call_back_fun) {
            $str='<script type="text/javascript">window.parent.'.$call_back_fun.'( \''.$fileurl.'\', \''.$message.'\');</script>';
        } else {
            $str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>';
        }
        exit($str);
    }

    public function actionUpload() {
        $bucketname = isset($_REQUEST['bucketname']) ? trim($_REQUEST['bucketname']) : 'edaijia';
        if (!empty($_FILES)) {
            $is_upload = false;
            $pic_url = '';
            $base_path = isset($_REQUEST['folder']) ? trim($_REQUEST['folder']) : 'tmp';
            $img_name = isset($_REQUEST['img_name']) ? trim($_REQUEST['img_name']) : time().Common::makeRandCode().'.'.Common::get_extension($_FILES['Filedata']['name']);
            $tempFile = $_FILES['Filedata']['tmp_name'];
            //$targetPath = $_SERVER['DOCUMENT_ROOT'] .'/images/tmp/';
            $targetPath = '/home/wwwlogs/tmp/';
            if (!file_exists($targetPath)){
                mkdir ($targetPath);
                if(!is_writable($targetPath)){
                    @chmod($targetPath, 0777);
                }
            }
            $targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
            if (move_uploaded_file($tempFile,$targetFile)) {
                $upload_model = new UpyunUpload($bucketname);
                $base_path = str_replace('/v2/', '', $base_path);
                $is_upload = $upload_model->uploadFile($targetFile, $base_path, $img_name);
                $is_upload['img_name'] = $img_name;
                if (is_array($is_upload) && count($is_upload)) {
                    $info = config_upyun::get_config_params($bucketname);
                    $pic_url = $info['up_base_url'].$base_path.'/'.$img_name;
                }
            }
            $result['status'] = $pic_url ? true : false;
            $result['data'] = $pic_url;
            echo json_encode($result);
            exit;
        }
    }

}