<?php

class DropDownCity3 extends CWidget
{

    public $name = null;

    public $value = null;

    public $cityList = array();

    public $htmlOptions = array();

    public $defaultText = '全国';

    public $type = 'box';

    public $callback = null;

    private $_baseUrl = null;

    public function getScriptUrl()
    {
        if (empty($this->_baseUrl)) {
            $this->_baseUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/source');
        }
        return $this->_baseUrl;
    }

    public function init()
    {
        if (isset($this->htmlOptions['id'])) {
            $this->id = $this->htmlOptions['id'];
        } else {
            $this->htmlOptions['id'] = $this->id;
        }

        if (is_null($this->name)) {
            $this->name = $this->id;
        }

        $options = array(
            'cityList' => $this->cityList,
            'name' => $this->name,
            'type' => $this->type,
        );
        $json_options = json_encode($options);

        if (!is_null($this->callback)) {
            $js = "jQuery('#{$this->id}').citypicker3($json_options, $this->callback);\n";
        } else {
            $js = "jQuery('#{$this->id}').citypicker3($json_options);\n";
        }
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');

        $baseUrl = $this->getScriptUrl();
        $cs->registerScriptFile(SP_URL_JS . 'bootstrap-modal.js', CClientScript::POS_END);
        $cs->registerScriptFile($baseUrl . '/citypicker3.js', CClientScript::POS_HEAD);
        $cs->registerScript(__CLASS__ . '#' . $this->id, $js);
    }

    public function run()
    {
        $city_name = $this->getCityName($this->value);
        echo '<div class="input-append">';
        echo CHtml::textField('city_list', $city_name, $this->htmlOptions);
        echo CHtml::hiddenField($this->name, $this->value);
        echo '<span class="add-on" id="dropdown">';
        echo '<i class="icon-chevron-down"></i>';
        echo '</span>';
        echo '</div>';
    }

    public function getCityName($city_id)
    {
        if ($city_id) {
            $city_list = Dict::items('city');
            return isset($city_list[$city_id]) ? $city_list[$city_id] : $this->defaultText;
        } else {
            return $this->defaultText;
        }
    }
}