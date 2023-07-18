<?php
return array(
    'zones' => array(
        'cache05_P0' => array(
            //'host'     => 'vqueueredisslave.edaijia-inc.cn',
            'host'     => 'vqueueredis.edaijia-inc.cn',
            'port'     => 6379,
            'password' => 'k74FkBwb7252FsbNk2M7',
            'brpop'    => 5,
            'set'      => array(
                //add P0 queue type here
                'apporder',
                'dispatch',
                'newpush_message',
                'getui_message',
                'current',
                'current_status',
            ),
        ),

        'cache05_P1' => array(
            //'host'     => 'vqueueredisslave.edaijia-inc.cn',
            'host'     => 'vqueueredis.edaijia-inc.cn',
            'port'     => 6379,
            'password' => 'k74FkBwb7252FsbNk2M7',
            'brpop'    => 5,
            'set'      => array(
                //add P1 queue type here
                'dalorder',
		'pushmsg',
		'order',
		'settlement',
		'orderstate',
            ),
        ),

        'cache05_P2' => array(
            //'host'     => 'vqueueredisslave.edaijia-inc.cn',
            'host'     => 'vqueueredis.edaijia-inc.cn',
            'port'     => 6379,
            'password' => 'k74FkBwb7252FsbNk2M7',
            'brpop'    => 5,
            'set'      => array(
                //add P2 queue type here
		'tmporder',
                'orderprocess',
                'position',
		'clientpush',
		'task',
		'register',
            ),
        ),

        'cache05_P3' => array(
            //'host'     => 'vqueueredisslave.edaijia-inc.cn',
            'host'     => 'vqueueredis.edaijia-inc.cn',
            'port'     => 6379,
            'password' => 'k74FkBwb7252FsbNk2M7',
            'brpop'    => 5,
            'set'      => array(
                //add P3 queue type here
		'status',
		'default',
		'dalmessage',
                'dumplog',
		'synchronize_elasticsearch',
		'newpush_notice_message',
		'getui_notice_message',
		'test',
		'orderlog',
		'activity',
                'support',
		'backtogether',
		'dispatchlog',
                'calllog',
		'dumpsmslog',
		'appcalllog',
                'position_miss',
		'urgecall',
		'coupon',
		'v2loginsms',
                'dache',
		'message',
            ),
        ),

	//默认配置
        'default' => array(
            //'host'     => 'vqueueredisslave.edaijia-inc.cn',
            'host'     => 'vqueueredis.edaijia-inc.cn',
            'port'     => 6379,
            'brpop'    => 5,
            'password' => 'k74FkBwb7252FsbNk2M7',
        ),
    ),
);
