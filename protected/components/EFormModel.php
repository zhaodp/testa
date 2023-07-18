<?php
/**
 * EFormModel class file.
 *
 * @author duke
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */


class EFormModel extends CFormModel
{
    private static $_names=array();

    /**
     * Constructor.
     * @param string $scenario name of the scenario that this model is used in.
     * See {@link CModel::scenario} on how scenario is used by models.
     * @see getScenario
     */
    public function __construct($scenario='')
    {
        parent::__construct($scenario);
    }


    public function returnMsg($code = 1,$msg='',$data='') {
        return array('code'=>$code,'message'=>$msg,'data'=>$data);
    }
}