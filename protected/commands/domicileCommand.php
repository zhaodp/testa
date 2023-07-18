<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-7-19
 * Time: 下午2:44
 * To change this template use File | Settings | File Templates.
 */

class domicileCommand extends CConsoleCommand {

    public $province = array(
        "'北京'",
        "'上海'",
        "'广东'",
        "'浙江'",
        "'河北'",
        "'山东'",
        "'辽宁'",
        "'四川'",
        "'重庆'",
        "'黑龙江'",
        "'吉林'",
        "'甘肃'",
        "'青海'",
        "'河南'",
        "'江苏'",
        "'湖北'",
        "'湖南'",
        "'江西'",
        "'云南'",
        "'福建'",
        "'海南'",
        "'山西'",
        "'陕西'",
        "'贵州'",
        "'安徽'",
        "'广西'",
        "'内蒙古'",
        "'西藏'",
        "'新疆'",
        "'宁夏'",
        "'澳门'",
        "'香港'",
        "'台湾'",
    );

    public $province_arr = array(
        "北京",
        "上海",
        "广东",
        "浙江",
        "河北",
        "山东",
        "辽宁",
        "四川",
        "重庆",
        "黑龙江",
        "吉林",
        "甘肃",
        "青海",
        "河南",
        "江苏",
        "湖北",
        "湖南",
        "江西",
        "云南",
        "福建",
        "海南",
        "山西",
        "陕西",
        "贵州",
        "安徽",
        "广西",
        "内蒙古",
        "西藏",
        "新疆",
        "宁夏",
        "澳门",
        "香港",
        "台湾",
    );

    public $match = array(
        2 => '四川',
        4 => '浙江',
        5 => '广东',
        6 => '广东',
        8 => '江苏',
        9 => '湖南',
        10 => '湖北',
        11 => '陕西',
        12 => '浙江',
        13 => '浙江',
        15 => '山东',
        16 => '浙江',
        17 => '云南',
        18 => '河南',
        19 => '辽宁',
        20 => '山东',
        21 => '辽宁',
        22 => '福建',
    );

    /**
     * 修复原错误数据 例如：河北省 、 河北唐山
     */
    public function actionReset() {
        $province_str = implode(',', $this->province);
        $sql = "SELECT id,user,domicile FROM t_driver WHERE domicile not in ({$province_str})";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $data = $command->queryAll();
        if (is_array($data) && count($data)) {
            foreach($data as $v) {
                foreach ($this->province_arr as $p) {
                    if (!empty($v['domicile'])) {
                        if (strpos($v['domicile'], $p) !== false || strpos($p, $v['domicile']) !== false) {
                            $_model = Driver::model()->getProfile($v['user']);
                            $_model->domicile = $p;
                            if ($_model->save(false)) {
                                echo iconv('utf-8','GBK//IGNORE',$v['user']).",".iconv('utf-8','GBK//IGNORE',$v['domicile']).",".iconv('utf-8','GBK//IGNORE',$p)."\n";
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 显示无法修复的错误数据 如：应该填黑龙江却写了哈尔滨
     */
    public function actionGetError() {
        $province_str = implode(',', $this->province);
        $sql = "SELECT id,city_id,user,domicile FROM t_driver WHERE domicile not in ({$province_str}) order by city_id";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $data = $command->queryAll($sql);
        if (is_array($data) && count($data)) {
            foreach($data as $v) {
                echo iconv('utf-8','GBK//IGNORE',$v['id']) . "," . iconv('utf-8','GBK//IGNORE',$v['city_id']) . "," . iconv('utf-8','GBK//IGNORE',$v['user']). "," .iconv('utf-8','GBK//IGNORE',$v['domicile']). "\n";
            }
        }
    }

    /**
     * 列出本省司机 例如籍贯是河南南阳的司机在郑州分公司工作就应该显示城市而不是河南
     */
    public function actionShowErrordata() {
        foreach($this->match as $city_id=>$city_name) {
            $sql = "SELECT * FROM t_driver WHERE city_id={$city_id} AND domicile like '%{$city_name}%'";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $data = $command->queryAll();
            if (is_array($data) && count($data)) {
                foreach($data as $v) {
                    echo iconv('utf-8','GBK//IGNORE',$v['id']) . "," . iconv('utf-8','GBK//IGNORE',$v['city_id']) . "," . iconv('utf-8','GBK//IGNORE',$v['user']). "," .iconv('utf-8','GBK//IGNORE',$v['domicile']). "\n";
                }
            }
        }
    }
}
