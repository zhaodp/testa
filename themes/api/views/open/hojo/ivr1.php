<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangjun
 * Date: 15/3/4
 * Time: 下午1:53
 */

$phone = $params['phone'];

IvrConfig::model()->callHandle($phone);


?>