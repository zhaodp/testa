<?php

/**
 * 从其他数据源读取数据到redis
 *
 * @author syang
 */
class ReloadRedisCommand extends CConsoleCommand {
    
    
    public function actionApiKey() {
	RApiKey::model()->reloadKeys();
    }

}

?>
