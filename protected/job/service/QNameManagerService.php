<?php
/**
 * queue队列名称维护
 */
class QNameManagerService {
    public $whitelist = array('position','current', 'current_status', 'status', 'heartbeat','orderprocess',
                                    'dumplog','task','heatmap','dispatch','calllog','order','test',
                                    'default','position_miss','apporder','orderstate','apptest','register',
                                    'dache','settlement','tmporder','support','coupon','pushmsg','dalorder',
                                    'dalmessage','dumpsmslog','appcalllog','urgecall','dispatchlog','orderext',
                                    'clientpush','backtogether','newpush_message','orderlog', 'getui_message',
                                    'activity', 'synchronize_elasticsearch', 'newpush_notice_message', 'getui_notice_message',
                                    'message','v2loginsms', 'customer_getui_order_message', 'customer_getui_notice_message',
                                    'apple_order_message', 'apple_notice_message', 'bad_weather_sms_notify',
                                    'order_status_changed_publisher', 'imm_user_push', 'driver_doc_download','bonus_no_sn','driver_quiz_publisher',
                                    'error'
);

    private static $_models;

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function get_base_qname($hash_qname) {
        if(empty($hash_qname)) {
            return '';
        }

        $hash_qname = strtolower($hash_qname);

        if(in_array($hash_qname, $this->whitelist)) {
            return $hash_qname;
        }

        $rev_qname = strrev($hash_qname);
        $split_name = explode("_", $rev_qname, 2);
        if(!isset($split_name) || count($split_name) != 2)
            return '';

        $base_qname = strrev($split_name[1]);
        $hash_code_a = strrev($split_name[0]);
        $hash_code_b = $this->elf_hash(strrev($base_qname));

        if($hash_code_a != $hash_code_b) {
            return '';
        }

        return $base_qname;
    }

    public function gen_hash_qname($base_qname) {
        if(empty($base_qname)) {
            return '';
        }

        $r_base_qname = strrev($base_qname);
        return $base_qname . '_' . $this->elf_hash($r_base_qname);
    }

    private function elf_hash($str) {
        $hash = $x = 0;
        $n = strlen($str);

        for ($i = 0; $i <$n; $i++)
        {
            $hash = ($hash <<4) + ord($str[$i]);
            if(($x = $hash & 0xf0000000) != 0)
            {
                $hash ^= ($x>> 24);
                $hash &= ~$x;
            }
        }
        return $hash % 701819;
    }
}
