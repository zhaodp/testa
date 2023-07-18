<?php
/**
 * Application Behavior
 *
 * add section, in main.php 
 * <pre>
 * 'behaviors' = array(
 * 		'ApplicationBehavior' => 'application.behaviors.ApplicationBehavior'
 * )
 * </pre>
 * 
 * 
 * @author syang
 */
class ApplicationBehavior extends CBehavior {
    
    public function events()
    {
		return array(
		    'onBeginRequest'=>'beginRequest',
		    'onEndRequest' => 'endRequest',
		);
    }

    /**
     * if used the function, please used public
     * @param CEvent $event
     */
    protected function beginRequest($event) {

    }
	
    /**
     * if used the function, please used public
     * @param CEvent $event
     */
    protected function endRequest($event) {
    }

} 

?>    