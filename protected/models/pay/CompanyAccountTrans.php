<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2015/1/22
 * Time: 12:15
 */

class CompanyAccountTrans extends FinanceActiveRecord{

    const TRANS_TYPE_CAST = 1;//充值
    const TRANS_TYPE_PAY = 2;//支付

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{company_account_trans}}';
    }
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'account_id' => '公司账户ID',
            'user_id' => '用户ID',
            'trans_order_id' => '交易订单号',
            'trans_type' => '交易类型',
            'cast' => '交易金额',
            'balance' => '当前余额',
            'operator' => '操作人',
            'create_time' => '创建时间',
            'remark' => '备注',
        );
    }
    public function rules()
    {
        return array(
            array('account_id', 'required'),
            array('account_id, user_id, trans_order_id, trans_type,cast,balance,operator,create_time,remark','safe'),
        );
    }
    /**
     * 插入对应公司账户流水信息
     * @param array $params
     * @return array
     * @auther mengtianxue
     */
    public function addCompanyAccountTrade($params = array())
    {

        $back = array('code' => 1, 'message' => '参数不能为空');
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $params[$k] = trim($v);
            }
            $params['create_time'] = date('Y-m-d H:i:s');
            $model = new CompanyAccountTrans();
            $model->attributes = $params;

            $ret = $model->insert();
            if ($ret) {
                $back['code'] = 0;
                $back['message'] = '添加成功';
                $back['date'] = $params;
            } else {
                $back['message'] = '添加失败';
            }
        }
        return $back;
    }

} 