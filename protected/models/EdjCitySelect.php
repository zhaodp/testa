<?php
/**
 * Desc：
 * Created by Huangjiangang
 * Created on 2021/6/3 14:39
 */

class EdjCitySelect {
    //三个控件的公共属性
    public $ele_id; //设置
    public $sel_id;
    public $city_list;
    public $html_operation;
    public $label_name;
    public $is_multi; //是多选
    public $checkboxName; //复选框name值
    public $show_city_ids; //需要展示的城市 id 集合
    public $checked_city_ids;
    public $model_id;
    public $disabled_city_ids;
    public $all_city_value = 0;

    //页面多选组件
    public $mode = 'page';

    public $component_type = ''; //组件类型

    public function __construct($component_type = 'A', $is_multi = false, $checkboxName='', $label_name='', $ele_id='',
                                $id = '', $data = [], $html_operation = [], $show_city_ids = [], $checked_city_ids = [],
                                $model_id = 'modalCity', $disabledCityList = [])
    {
        $this->component_type = $component_type;
        $this->label_name = $label_name;
        $this->ele_id = $ele_id;
        $this->sel_id = $id;
        $this->city_list = $data;
        $this->html_operation = $html_operation;
        $this->is_multi = $is_multi;
        $this->checkboxName = $checkboxName;
        $this->show_city_ids = $show_city_ids;
        $this->checked_city_ids = $checked_city_ids;
        $this->model_id = $model_id;
        $this->disabled_city_ids = $disabledCityList;
    }

    public function initComponentScript()
    {
        $ele_id = $this->ele_id;
        $checked_city_id = json_encode($this->checked_city_ids);
        $show_city_ids = json_encode($this->show_city_ids);
        //yii html_operation 的逻辑这里也处理下
        if (isset($this->html_operation['id'])) {
            $ele_id = $this->html_operation['id'];
        }
        if ($this->component_type == 'A') { //弹窗单选控件
            if (empty($this->sel_id)) {
                $this->sel_id = -1;
            }
            echo "
                var choose_city_id = $this->sel_id;
                var $this->model_id = new ModalCity({
                modalId: '$this->model_id',
                showCityIds: $show_city_ids,
                isMultiSelect: false,onSingleSelectCity: function (data) {
                    $('#$ele_id').val(data.id);
                }
            });";
        } else if ($this->component_type == 'B') {
            echo "var modalCity = new ModalCity({
                showCityIds: $show_city_ids,
                isMultiSelect: $this->is_multi,
                checkboxName: '$this->checkboxName',
                isMultiSelect: false,onSingleSelectCity: function (data) {
                    console.log(data);
                }
            });";
        } else if ($this->component_type == 'C') {
            echo "var edj_city_list = '" . json_encode($this->city_list) . "';" .
                "edj_city_list = JSON.parse(edj_city_list);";
            echo "var $this->model_id = new ComponentCity({
                    container: $('#$ele_id'),
                    modalId: '$this->model_id',
                    checkboxName: '$this->checkboxName',
                    isMultiSelect: $this->is_multi,
                    checkedCityList: $checked_city_id,
                    showCityIds: $show_city_ids
                });";
            if (!empty($this->disabled_city_ids)) {
                echo "$this->model_id.renderDisabledCityList(" . json_encode(array_values($this->disabled_city_ids)) . ");";
            }
        }
    }

    public function initComponentHtml()
    {
        if ($this->component_type == 'A') { //弹窗单选控件
            if (!empty($this->label_name)) {
                echo CHtml::label($this->label_name, $this->ele_id);
            }
            echo CHtml::dropDownList($this->ele_id, $this->sel_id , $this->city_list, $this->html_operation);
        } else if ($this->component_type == 'B') {
            echo CHtml::label($this->label_name, $this->ele_id);
            echo CHtml::dropDownList($this->ele_id, $this->sel_id , $this->city_list, $this->html_operation);
        } else if ($this->component_type == 'C') {
            echo CHtml::label($this->label_name, $this->ele_id);
            echo "<div class='panel-body box box_border' id=$this->ele_id></div>";
        }
        if ($this->all_city_value === false) {
            echo "<style>#provinceCityWrapper .select-country-wrapper{display: none;}</style>";
        }
    }

    public static function getShowCityListAndCityIds() {
        $user_city_id = Yii::app()->user->city;
        $user_city_list=array();
        $show_city_ids = [];
        if (isset(Yii::app()->user->city_list) && !empty(Yii::app()->user->city_list)) {
            $user_city_list = Yii::app()->user->city_list;
        }
        $citys = Common::getOpenCity();
        $mycity = array();
        if ($user_city_id == 0) {//全国
            $mycity = $citys;
        }
        if (is_array($user_city_list)) {//多个城市
            if (in_array('0', $user_city_list)) {
                $mycity = $citys;
            } else {
                foreach ($citys as $k=>$v) {
                    if (in_array($k,$user_city_list)) {
                        $mycity[$k] = $v;
                    }
                }
                $mycity[$user_city_id]=$citys[$user_city_id];
            }
        } else {//一个城市
            $mycity = array($user_city_id=>$citys[$user_city_id]);
        }

        if ($user_city_id != 0) {
            if(!empty($mycity)){
                $show_city_ids = array_keys($mycity);
                $city_list = CityTools::cityPinYinSort(false,$mycity);
            }else{
                $show_city_ids = [$user_city_id];
                $city_list = array(
                    '城市' => array(
                        $user_city_id => Dict::item('city', $user_city_id)
                    )
                );
            }
        } else {
            $city_list = CityTools::cityPinYinSort();
        }

        return ['city_list' => $city_list, 'show_city_ids' => $show_city_ids];
    }
}