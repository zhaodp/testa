<?php
/**
 * CitySelectController class file.
 * This file is just used to generate "select html" for city.
 * @author liuzhen <liuzhen02@edaijia-inc.cn>
 */

class CitySelectController extends Controller
{
    public function actions() {
        return array();
    }

    /**
     * 生成城市对应的单选下拉列表，带用户权限验证，判断是否能显示全国数据
     * @param string $name              下拉框名称
     * @param string $city_list_str     下拉框数据
     * @param int $city_id              下拉框默认值
     * @param string $options_str       其它属性
     */
    public function actionGenSelect($name = '', $city_list_str = '', $city_id = 0, $options_str = '') {
        $city_list_temp = json_decode($city_list_str, true);
        if (empty($city_list_temp)) {
            echo '';
            return;
        }

        empty($city_id) ? $city_id = 0 : '';

        $html_options = [
            'readonly' => 'readonly',
            'disabled' => true
        ];
        $options_arr = json_decode($options_str, true);
        if (!empty($options_arr)) {
            $html_options = $html_options + $options_arr;
        }

        //判断城市权限是否为全国
        $is_all = false;

        $user_city = Yii::app()->user->city;
        if (0 == $user_city) {
            $is_all = true;
        }

        if (isset(Yii::app()->user->city_list) && !empty(Yii::app()->user->city_list)) {
            $user_city_list = Yii::app()->user->city_list;
            if (in_array('0', $user_city_list)) {
                $is_all = true;
            }
        }

        if ($is_all) {
            $city_list = CityTools::cityPinYinArrSort();
        } else {
            if (0 == $city_id) {
                $city_id = key($city_list_temp);
            }

            //将city_list_temp转换格式
            $city_list_pinyin = CityTools::cityListSortWithPinYin();
            foreach ($city_list_pinyin as $val) {
                foreach ($val as $city_id_key => $city_name_val) {
                    if (isset($city_list_temp[$city_id_key])) {
                        $city_list_temp_pinyin[$city_id_key] = $city_name_val;
                    }
                }
            }

            $city_list['城市'][''] = $city_list_temp_pinyin;
        }

        $city_list['搜索']=array();

        $this->widget('application.widgets.common.DropDownCity3', array(
            'cityList' => $city_list,
            'name' => $name,
            'value' => $city_id,
            'type' => 'modal',
            'htmlOptions' => $html_options
        ));
    }

    /**
     * 生成城市对应的单选下拉列表，不带用户权限验证，显示全国数据
     * @param string $name              下拉框名称
     * @param int $city_id              下拉框默认值
     * @param string $options_str       其它属性
     */
    public function actionGenSelectNoAuth($name = '', $city_id = 0, $options_str = '') {
        $html_options = [
            'readonly' => 'readonly',
            'disabled' => true
        ];
        $options_arr = json_decode($options_str, true);
        if (!empty($options_arr)) {
            $html_options = $html_options + $options_arr;
        }

        empty($city_id) ? $city_id = 0 : '';
        $city_list = CityTools::cityPinYinArrSort();
        $city_list['搜索']=array();

        $this->widget('application.widgets.common.DropDownCity3', array(
            'cityList' => $city_list,
            'name' => $name,
            'value' => $city_id,
            'type' => 'modal',
            'htmlOptions' => $html_options
        ));
    }

    public static function str2pinyin($s){
        $s = preg_replace("/\s/is", "_", $s);
        $s = preg_replace("/(|\~|\`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\-|\+|\=|\{|\}|\[|\]|\||\\|\:|\;|\"|\'|\<|\,|\>|\.|\?|\/)/is", "", $s);
        $py = "";
        // 加入这一句，自动识别utf-8
        if(strlen("拼音") > 4)$s = iconv('utf-8', 'gbk', $s);
        for($i = 0;$i < strlen($s);$i++){
            if(ord($s[$i]) > 128){
                // if($py!="")$py.="_";
                $py .= self::asic2pinyin(ord($s[$i]) + ord($s[$i + 1]) * 256);
                $i++;
            }else{
                $py .= $s[$i];
            }
            if(strlen($py) >= 20)return $py;
        }
        return $py;
    }

}
