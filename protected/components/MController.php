<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class MController extends Controller {

	public function init() {
		parent::init();
        Yii::app()->theme = 'trust';
    }
	

}