<?php

class ConsoleApplicationBehavior extends CBehavior
{
    const JOB_START     = 1;
    const JOB_ERROR     = 2;
    const JOB_EXCEPTION = 3;
    const JOB_STOP      = 4;
    const JOB_KILLED    = 5;

    public function events()
    {
        return array_merge(
            parent::events(),
            array(
               'onBeginRequest' => 'onBeginRequest',
               'onEndRequest' => 'onEndRequest',
               'onError' => 'onApplicationError',
               'onException' => 'onApplictionException'
            )
        );
    }

    private function logBeginRequest(CEvent $event)
    {
        // 这个值是在yiic.php里从argv里extarct出来的，然后argv里就没有cronId了
        global $cronId;
        if ($cronId === 0)
           return; 

        Yii::app()->dbsys->createCommand()->insert(
            'sys_event_log',
             array(
                'cronid' => $cronId,
                'event_type' => ConsoleApplicationBehavior::JOB_START,
                'event_description' => 'Job start'
             )
        );
    }

    private function logEndRequest(CEvent $event)
    {
        global $cronId;
        if ($cronId === 0)
           return; 

        Yii::app()->dbsys->createCommand()->insert(
            'sys_event_log',
             array(
                'cronid' => $cronId,
                'event_type' => ConsoleApplicationBehavior::JOB_STOP,
                'event_description' => 'Job stop'
             )
        );
    }

    private function prepareErrorMessage($code, $message, $file, $line)
    {
        $error_description = "";
		$error_description .=  "PHP Error[$code]: $message\n";
		$error_description .=  "    in file $file at line $line\n";
		$trace=debug_backtrace();
        // 这里跳过了前面的6个堆栈，堆栈的调用层级直接决定了这里应该跳过多少层
        // 所以日后如果出现了重构，一定要检查需要跳过多少级，跳过正确的层级，否则打印出来的
        // trace log会包含一些不必要的信息
		if(count($trace)>6)
			$trace=array_slice($trace,6);
		foreach($trace as $i=>$t)
		{
			if(!isset($t['file']))
				$t['file']='unknown';
			if(!isset($t['line']))
				$t['line']=0;
			if(!isset($t['function']))
				$t['function']='unknown';
			$error_description .=  "#$i {$t['file']}({$t['line']}): ";
			if(isset($t['object']) && is_object($t['object']))
				$error_description .=  get_class($t['object']).'->';
			$error_description .=  "{$t['function']}()\n";
		}

        return $error_description;
    }

    private function logApplicationError(CErrorEvent $event)
    {
        global $cronId;
        if ($cronId === 0)
           return; 

        Yii::app()->dbsys->createCommand()->insert(
            'sys_event_log',
             array(
                 'cronid' => $cronId,
                 'event_type' => ConsoleApplicationBehavior::JOB_ERROR,
                 'event_description' => $this->prepareErrorMessage($event->code, $event->message, $event->file, $event->line)
             )
        );
    }

    private function prepareExceptionMessage(CExceptionEvent $event)
    {
        return $event->exception->__toString();
    }

    private function logApplictionException(CExceptionEvent $event)
    {
        global $cronId;
        if ($cronId === 0)
           return; 

        Yii::app()->dbsys->createCommand()->insert(
            'sys_event_log',
             array(
                 'cronid' => $cronId,
                 'event_type' => ConsoleApplicationBehavior::JOB_EXCEPTION,
                 'event_description' => $this->prepareExceptionMessage($event)
             )
        );
    }

    // TODO: strategy pattern should be used here.
    public function onBeginRequest(CEvent $event)
    {
        if ($event->sender instanceof CConsoleApplication) {
           $this->logBeginRequest($event); 
        }
    }

    public function onEndRequest(CEvent $event)
    {
        if ($event->sender instanceof CConsoleApplication) {
            $this->logEndRequest($event);
        }
    }

    public function onApplicationError(CErrorEvent $event)
    {
        if ($event->sender instanceof CConsoleApplication) {
            $this->logApplicationError($event); 
        }
    }

    public function onApplictionException(CExceptionEvent $event)
    {
        if ($event->sender instanceof CConsoleApplication) {
            $this->logApplictionException($event); 
        }
    }
}
