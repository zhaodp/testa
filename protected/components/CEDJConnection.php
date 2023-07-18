<?php

class CEDJConnection extends CDbConnection
{
    private $last_active = 0;

    public $autoReconnect = 3;
    
    public function setActive($value)
    {
        if($value && $this->autoReconnect) {
            $lifetime = time() - $this->last_active;
            if($lifetime > intval($this->autoReconnect)) {
                try {
                    if ($this->getActive()) {
                        $stm = @$this->getPdoInstance()->query('SELECT 1');
                        if (empty($stm) || $stm->rowCount() !== 1) {  
                            throw new LogicException("MySQL server has gone away");
                        }
                    }
                } catch (Exception $e) {
                    EdjLog::error($e->getMessage()." Last connection's lifetime: {$lifetime}, trying to close and reconnect... ", 'components.DbConnection.setActive');
                    parent::setActive(false);
                }
            }
        }

        parent::setActive($value);
        $this->last_active = time();
    }
}
