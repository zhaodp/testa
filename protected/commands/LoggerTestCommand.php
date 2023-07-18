<?php

class LoggerTestCommand extends LoggerExtCommand {

    public function actionWorker($qname='default') {
        EdjLog::info('Hello LogExtCommand', 'console');
    }
}
