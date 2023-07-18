<?php
return array(
    'zones' => array(
        'order' => array(
            'host'     => 'redis02n.edaijia.cn',
            'port'     => 6379,
            'password' => '',
            'set'      => array(
                //add queue type here
            ),
        ),

        'brpop_order' => array(
            'host'     => 'redis02n.edaijia.cn',
            'port'     => 6379,
            'password' => '',
            'brpop'    => 5,
            'set'      => array(
                //add queue type here
                'orderprocess',
            ),
        ),

        'default' => array(
            'host'     => 'redis02n.edaijia.cn',
            'port'     => 6379,
            'password' => '',
        ),
    ),
);
