<?php
class ComplainAddMaterialAction extends CAction {
    public function run() {
        $res = array('succ'=>0,'errmsg'=>'');
        $cid = Yii::app()->request->getQuery('cid');

        //上传到本地服务器
        if (!empty($_FILES)) {
            $file_size = $_FILES['complain_material_upload']['size'];
            if ($file_size > 10*1024*1024) {
                $res['errmsg'] = '文件不能大于10Mb';
                echo json_encode($res);
                Yii::app()->end();
            }
            $src_filename = $_FILES['complain_material_upload']['name'];
            $file_name = time().Common::makeRandCode().'.'.Common::get_extension($src_filename);
            $tempFile = $_FILES['complain_material_upload']['tmp_name'];
            //$targetPath = $_SERVER['DOCUMENT_ROOT'] .'/images/complainMaterial/';
            $targetPath = '/home/wwwlogs/complainMaterialTmp/';
            if (!file_exists($targetPath)){
                mkdir ($targetPath);
                if(!is_writable($targetPath)){
                    @chmod($targetPath, 0777);
                }
            }
            $targetFile =  str_replace('//','/',$targetPath) . $src_filename;
            if (move_uploaded_file($tempFile,$targetFile)) {
                //上传到云服务器
                $bucketname =  'driverdoc';
                $upload_model = new UpyunUpload($bucketname);
                $base_path = 'driverComplain';
                $is_upload = $upload_model->uploadFile($targetFile, $base_path, $file_name);
                if ($is_upload) {
                    $info = config_upyun::get_config_params($bucketname);
                    $file_url = $info['up_base_url'].$base_path.'/'.$file_name;
                    //存储记录
                    $ret = CustomerComplainMaterial::model()->saveMaterial($cid, $file_url, $src_filename);
                    if ($ret) {
                        $res['succ'] = 1;
                    } else {
                        $res['errmsg'] = '存储记录失败了';
                    }
                } else {
                    $res['errmsg'] = '上传文件到云失败';
                }
            } else {
                $res['errmsg'] = '上传文件失败';
            }

        } else {
            $res['errmsg'] = '请选择文件';
        }

        echo json_encode($res);
    }
}