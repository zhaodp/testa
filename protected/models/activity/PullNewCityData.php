<?php
/**
 * 司机拉新活动 城市每日报表数据
 */
class PullNewCityData extends CActiveRecord
{
    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db_activity;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RedPacketLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_pull_new_city_data}}';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id,recruitment_drivers_num,sign_drivers_num,total_amount,create_time', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
    }

    /**
     * 获取$date日的数据 格式:2015-02-18
     */
    public function getCityDataReport($date = '')
    {
        if (empty($date)) {
            EdjLog::info("请输入日期");
            return;
        }
        $criteria = new CDbCriteria;
        $criteria->select = '*';
        $criteria->condition = 'create_time=:create_time';
        $criteria->params = array(':create_time' => $date);
        $criteria->order = ' sign_drivers_num desc';
        $data = self::model()->findAll($criteria);
        return $data;
    }

    public function dataHtml($city_datas,$driver_datas,$date)
    {
        $citys = RCityList::model()->getOpenCityList();
        $html = '<table cellpadding="0" cellspacing="0" width="500">
                            <tbody>
                            <tr>
                                <td style="height:40px;line-height:40px;text-align:center">
                                    <div style="color:rgb(0, 136, 204);font-size:15px;">
                                       分城市司机拉新司机数据
                                    </div>
                                </td>
                            </tr>
                                <tr>
                                    <td>
                                        <table style="text-align:left;border:1px #929292 solid;font-size:13px;"
                                               cellpadding="9" cellspacing="0" width="100%">
                                            <thead>
                                            <tr style="background:#5577AA;color:#FFF;">
						                        <td width="18%">日期</td>
                                                <td align="right" width="17%">城市</td>
                                                <td align="right" width="20%">报名人数</td>
                                                <td align="right" width="20%">签约人数</td>
                                                <td align="right" width="25%">奖励司机总金额</td>
                                            </tr>
                                            </thead>
                                            <tbody>';
        if ($city_datas) {
            foreach ($city_datas as $data) {
                $tmp_line = '';
                $tmp_line .= '<tr>';
                $tmp_line .= '<td style="font-size:12px" >' . $date . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $citys[$data->city_id] . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->recruitment_drivers_num . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->sign_drivers_num . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->total_amount . '</td>';
                $tmp_line .= '</tr>';
                $html .= $tmp_line;
            }

        }
        $html .= '

                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>';
        $html_driver = '<table cellpadding="0" cellspacing="0" width="500">
                            <tbody>
                            <tr>
                                <td style="height:40px;line-height:40px;text-align:center">
                                    <div style="color:rgb(0, 136, 204);font-size:15px;">
                                       分工号司机拉新司机数据
                                    </div>
                                </td>
                            </tr>
                                <tr>
                                    <td>
                                        <table style="text-align:left;border:1px #929292 solid;font-size:13px;"
                                               cellpadding="9" cellspacing="0" width="100%">
                                            <thead>
                                            <tr style="background:#5577AA;color:#FFF;">
						                        <td width="18%">日期</td>
                                                <td align="right" width="17%">工号</td>
                                                <td align="right" width="20%">报名人数</td>
                                                <td align="right" width="20%">签约人数</td>
                                                <td align="right" width="25%">奖励司机总金额</td>
                                            </tr>
                                            </thead>
                                            <tbody>';
        if ($driver_datas) {
            foreach ($driver_datas as $data) {
                $tmp_line = '';
                $tmp_line .= '<tr>';
                $tmp_line .= '<td style="font-size:12px" >' . $date . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->driver . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->recruitment_drivers_num . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->sign_drivers_num . '</td>';
                $tmp_line .= '<td align="right" style="font-size:12px" >' . $data->total_amount . '</td>';
                $tmp_line .= '</tr>';
                $html_driver .= $tmp_line;
            }

        }
        $html_driver .= '

                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>';
        return $html.$html_driver;
    }


}
