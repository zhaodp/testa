<?php

class HojoAction extends CAction
{

    public function run()
    {
        $method = isset($this->controller->_params['method']) ? $this->controller->_params['method'] : '';
        switch ($method) {
            case 'check' :
                $phone = empty($this->controller->_params['phone']) ? '' : trim($this->controller->_params['phone']);
                break;
            default :
                $task = array(
                    'method' => 'callcenter_hojo',
                    'params' => $this->controller->_params
                );
                Queue::model()->putin($task, 'dumplog');
                break;
        }
        echo "200";
    }

    /**
     * 检查电话是否黑名单或者VIP客户
     * @param string $phone
     */
    private function check($phone)
    {

    }
}
