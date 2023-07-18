<?php

/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-4-18
 * Time: 下午4:33
 * auther mengtianxue
 */
class cityCommand extends CConsoleCommand
{

    /**
     *
     * @auther mengtianxue
     * php yiic.php city Test
     */
    public function actionTest()
    {
        $citys = Dict::items('city');
        foreach ($citys as $city_id => $city_name) {
            if (!empty($city_id)) {
                $data = array();
                $data['city_id'] = $city_id;
                $data['city_name'] = $city_name;
                $city_prefix = Dict::item('city_prefix', $city_id);
                $data['city_prifix'] = $city_prefix;
                $bonus_city = Dict::items('bonus_city');
                $bonus_city_r = array_flip($bonus_city);
                $bonus_prefix = isset($bonus_city_r[$city_prefix]) ? $bonus_city_r[$city_prefix] : '';

                $data['bonus_prifix'] = $bonus_prefix;

                //是否开通
                $data['status'] = $this->checkCityOpen($city_id);


                $data['pay_money'] = $this->getCityPay($city_id);

                //底线金额
                $data['screen_money'] = $data['pay_money'] == 500 ? 200 : 100;
                $pinyinclass = new GetPingYing();
                $pinyin = $pinyinclass->getAllPY($city_name);
                $shouzimu = $pinyinclass->getFirstPY($city_name);
                $data['first_letter'] = strtoupper($shouzimu);
                $data['pinyin'] = $pinyin;


                $data['bonus_back_money'] = 39;
                $back19 = Common::getCityFeeEq19();
                if (in_array($city_id, $back19)) {
                    $data['bonus_back_money'] = 19;
                }

                //是否是省会

                $data['captital'] = $this->getIsCaptial($city_name);
                //城市等级
                //$data['city_level'] = 'C3';
                if($data['status']){
                    $open_time_and_level = $this->getCityLevelAndOpenTime($city_id);
                    $data['city_level'] = $open_time_and_level['level'];
                    $data['online_time'] = $open_time_and_level['open_time'];
                }
                else{
                    $data['city_level'] = 'C3';
                    $data['online_time'] = '0000-00-00 00:00:00';
                }


                $data['fee_id'] = $this->calculator($city_id);
                $data['cast_id'] = $this->cast($city_prefix);

                $data['create_time'] = date('Y-m-d H:i:s');
                $data['update_time'] = date('Y-m-d H:i:s');
                //$data['online_time'] = date('Y-m-d H:i:s');
                echo $city_name;
                $model = new CityConfig();
                $model->attributes = $data;
                $city_one = $model->find('city_id = :city_id', array(':city_id' => $city_id));
                echo $city_name.'---'.$city_id;
                if (empty($city_one)) {
                    $model->insert();
                    echo "新建".$city_id."\n";
                }
                else{
                    $city_one->attributes = $data;
                    $city_one->save();
                    echo '保存'.$city_id."\n";
                }
                echo "\n";

            }
        }

    }


    public function getCityLevelAndOpenTime($city_id){
        $city_list = array(
            '2014年8月开通C3类城市订单趋势' => array(
                'city_ids'=>array(17,38,87,120,121,124,130,171),
                'level'=>'C3',
                'open_time'=>'2014-08-01 00:00:00'
            ),
            '2014年7月开通C3类城市订单趋势' => array(
                'city_ids'=>array(13,83,128,132,133,159,167),
                'level'=>'C3',
                'open_time'=>'2014-07-01 00:00:00'
            ),

            '2014年6月开通C3类城市订单趋势' => array(
                'city_ids'=>array(95,96,98,101,104,107,113,136,138,160),
                'level'=>'C3',
                'open_time'=>'2014-06-01 00:00:00'
            ),
            '2014年5月开通C3类城市订单趋势' => array(
                'city_ids'=>array(28,103,106,79,40,68,84,85,90,114,134,151,152),
                'level'=>'C3',
                'open_time'=>'2014-05-01 00:00:00'
            ),
            '2014年4月开通C3类城市订单趋势' => array(
                'city_ids'=>array(47,65,73,74,75,76),
                'level'=>'C3',
                'open_time'=>'2014-04-01 00:00:00'
            ),
            '2014年3月开通C3类城市订单趋势' => array(
                'city_ids'=>array(30,31,37,44,46,48,49,50,51,53,56,61,71,72),
                'level'=>'C3',
                'open_time'=>'2014-03-01 00:00:00'
            ),
            'C2类城市订单趋势' => array(
                'city_ids'=>array('16'),
                'level'=>'C2',
                'open_time'=>'2013-07-01 00:00:00'
            ),
            'C1类城市订单趋势' => array(
                'city_ids'=>array(),
                'level'=>'C1',
                'open_time'=>'2013-07-01 00:00:00'
            ),
            'B3类城市订单趋势' => array(
                'city_ids'=>array(23, 29, 25, 43, 41,33,24,26,77,35,36),
                'level'=>'B3',
                'open_time'=>'2013-07-01 00:00:00'
            ),
            'B2类城市订单趋势' => array(
                'city_ids'=>array(22,18, 15, 8, 14, 20,12,19,27,21,9),
                'level'=>'B2',
                'open_time'=>'2013-07-01 00:00:00'
            ),
            'B1类城市订单趋势' => array(
                'city_ids'=>array(2,4,10),
                'level'=>'B1',
                'open_time'=>'2013-07-01 00:00:00'
            ),
            'A2类城市订单趋势' => array(
                'city_ids'=>array(3, 6, 5,7,11),
                'level'=>'A2',
                'open_time'=>'2013-07-01 00:00:00'
            ),
            'A1类城市订单趋势' => array(
                'city_ids'=>array(1),
                'level'=>'A1',
                'open_time'=>'2013-07-01 00:00:00'
            ),
            'S类城市订单趋势' => array(
                'city_ids'=>array( ),
                'level'=>'S',
                'open_time'=>'2013-07-01 00:00:00'
            ),
        );

        $res = array('level'=> '','open_time'=>'');
        foreach($city_list as $v){
            if(in_array($city_id,$v['city_ids'])){
                $res = array('level'=> $v['level'],'open_time'=>$v['open_time']);
            }
        }

        return $res;
    }

    public function getIsCaptial($city_name){
        $citys = array('石家庄','太原','沈阳','长春','哈尔滨','南京','杭州','合肥','福州','南昌','济南','郑州','广州','长沙','武汉','海口','成都','贵阳','昆明','西安','兰州','西宁','台北','呼和浩特','南宁','拉萨','银川','乌鲁木齐');

        if(in_array($city_name,$citys)){
            return 1;
        }
        return 0;

    }

    public function checkCityOpen($city_id){
        $city = array(
            '1'=>'北京',
            '2'=>'成都',
            '3'=>'上海',
            '4'=>'杭州',
            '5'=>'广州',
            '6'=>'深圳',
            '7'=>'重庆',
            '8'=>'南京',
            '9'=>'长沙',
            '10'=>'武汉',
            '11'=>'西安',
            '12'=>'宁波',
            //'13'=>'温州',
            '14'=>'天津',
            '15'=>'济南',
            '16'=>'苏州',
            //'17'=>'昆明',
            '18'=>'郑州',
            '19'=>'沈阳',
            '20'=>'青岛',
            '21'=>'大连',
            '22'=>'厦门',
            '23'=>'合肥',
            '24'=>'哈尔滨',
            '25'=>'石家庄',
            //'26'=>'南昌',
            '27'=>'福州',
            //'28'=>'佛山',
            '29'=>'太原',
            '30'=>'无锡',
            '31'=>'常州',

            //2014-03-28  城市开通  mengtianxue
            '51'=>'嘉兴',
            '50'=>'绍兴',
            '49'=>'金华',
            '37'=>'南通',
            '46'=>'镇江',
            '44'=>'扬州',
            '53'=>'湖州',
            '48'=>'徐州',
            '71'=>'大同',
            '61'=>'洛阳',
            '56'=>'珠海',
            '41'=>'海口',
            '43'=>'银川',
            //'32'=>'东莞',
            '33'=>'贵阳',
            //'34'=>'兰州',
            //'35'=>'南宁',
            //'36'=>'长春',
            //'37'=>'南通',
            //'38'=>'呼和浩特',
            //'39'=>'包头',
//	    	'44'=>'扬州', //开通扬州 BY AndyCong 2014-03-25
            '72'=>'咸阳',
            //2014-4-30开通城市
            '47'=>'泰州',
            '65'=>'唐山',
            '73'=>'连云港',
            '74'=>'丽水',
            '75'=>'盐城',
            '76'=>'湛江',

            //2014-5-23 开通13个城市  by dxm
            '28'=>'佛山',
            '103'=>'汕头',
            '106'=>'阳江',
            '79'=>'漳州',
            '40'=>'威海',
            '68'=>'宜昌',
            '84'=>'潍坊',
            '85'=>'济宁',
            '90'=>'德州',
            '114'=>'宿迁',
            '134'=>'襄阳',
            '151'=>'绵阳',
            '152'=>'德阳',
            //2014-05-29 开通2个城市
            '26'=>'南昌',
            '77'=>'西宁',
            //2014-06-24 开通12个城市
            '35'=>'南宁',
            '36'=>'长春',
            '95'=>'日照',
            '96'=>'莱芜',
            '98'=>'舟山',
            '101'=>'江门',
            '104'=>'揭阳',
            '107'=>'韶关',
            '113'=>'淮安',
            '136'=>'黄石',
            '138'=>'荆州',
            '160'=>'萍乡',
            //2014-07-17 开通7个城市
            '13'=>'温州',
            '83'=>'三明',
            '128'=>'运城',
            '132'=>'渭南',
            '133'=>'汉中',
            '159'=>'新余',
            '167'=>'锦州',
            //2014-08-22 开通2个城市
            '17'=>'昆明',
            '38'=>'呼和浩特',
            //2014-08-26 开通6个城市
            '87'=>'临沂',
            '120'=>'焦作',
            '121'=>'许昌',
            '124'=>'长治',
            '130'=>'宝鸡',
            '171'=>'商丘',
        );
        $city_id_arr = array_keys($city);
        if(in_array($city_id,$city_id_arr)) return 1;
        return 0;
    }

    public function getCityPay($city_id){
        $city_500 = array(1,3,4,5,6,7,2,8,10,11,14,15,18,20);
        if(in_array($city_id,$city_500)) return 500;
        return 200;

    }


    /**
     * 费用计算方法
     * @param $city_id
     * @return string
     * @auther mengtianxue
     */
    public function calculator($city_id)
    {
        switch ($city_id) {
            case 1:
            case 3:
            case 5:
            case 6:
                return 'conventional';
                break;
            case 7:
                return 'cq_single';
                break;
            case 30:
            case 16:
            case 31:
            case 51:
            case 50:
            case 49:
            case 37:
            case 46:
            case 44:
            case 53:
            case 48:
            case 71:
            case 61:
            case 47:
            case 65:
            case 73:
            case 74:
            case 75:
            case 40:
            case 68:
            case 84:
            case 85:
            case 90:
            case 114:
            case 134:
            case 151:
            case 152:
                //2014-06-24
            case 95:
            case 96:
            case 98:
            case 113:
            case 136:
            case 138:
            case 160:
                //2014-07-17
            case 13:
            case 128:
            case 132:
            case 133:
            case 159:
            case 167:
                //2014-08-26
            case 87:
            case 120:
            case 121:
            case 124:
            case 130:
            case 171:
                return 'wx_single';
                break;
            default:
                return 'hz_single';
                break;
        }
    }


    /**
     * 信息费计算方法
     * @param $city_prefix
     * @return string
     * @auther mengtianxue
     */
    public function cast($city_prefix)
    {
        switch ($city_prefix) {
            //20%
            case 'BJ':
            case 'NJ':
            case 'XA':
            case 'ZZ':
            case 'WH':
            case 'TJ':
            case 'JN':
            case 'CD':
            case 'CS':
            case 'NC':
            case 'XN':
                return '_castBJ';
                break;
            //5、10、15、20
            case 'SH':
            case 'GZ':
            case 'SZ':
                return '_castLevelFour';
                break;
            case 'CQ': //39元以下2元 大于39元5块钱
                return '_castCQ';
                break;
            case  'HZ': //5块10块
                return '_castLevel';
                break;
            case  'WX': //5块10块
            case  'SU':
            case  'CZ':
            case  'JX':
            case  'SX':
            case  'JH':
            case  'NT':
            case  'ZJ':
            case  'YZ':
            case  'HU':
            case  'XZ':
            case  'DT':
            case  'LY':
            case  'TZ':
            case  'TS':
            case  'LG':
            case  'LS':
            case  'YH':
            case  'WI':
            case  'YI':
            case  'WF':
            case  'JG':
            case  'DZ':
            case  'SQ':
            case  'XG':
            case  'MY':
            case  'DA':
            case  'RZ':
            case  'LW':
            case  'ZN':
            case  'HI':
            case  'HS':
            case  'JO':
            case  'PX':
            case  'WZ':
            case  'YE':
            case  'WN':
            case  'HH':
            case  'XU':
            case  'JW':
            case 'LI':
            case 'JZ':
            case 'XC':
            case 'CH':
            case 'BI':
            case 'SA':
                return '_castWX';
                break;
            default:
                return '_castBJ';
                break;
        }
    }

}

class GetPingYing {
    private $pylist = array(
        'a'=>-20319,
        'ai'=>-20317,
        'an'=>-20304,
        'ang'=>-20295,
        'ao'=>-20292,
        'ba'=>-20283,
        'bai'=>-20265,
        'ban'=>-20257,
        'bang'=>-20242,
        'bao'=>-20230,
        'bei'=>-20051,
        'ben'=>-20036,
        'beng'=>-20032,
        'bi'=>-20026,
        'bian'=>-20002,
        'biao'=>-19990,
        'bie'=>-19986,
        'bin'=>-19982,
        'bing'=>-19976,
        'bo'=>-19805,
        'bu'=>-19784,
        'ca'=>-19775,
        'cai'=>-19774,
        'can'=>-19763,
        'cang'=>-19756,
        'cao'=>-19751,
        'ce'=>-19746,
        'ceng'=>-19741,
        'cha'=>-19739,
        'chai'=>-19728,
        'chan'=>-19725,
        'chang'=>-19715,
        'chao'=>-19540,
        'che'=>-19531,
        'chen'=>-19525,
        'cheng'=>-19515,
        'chi'=>-19500,
        'chong'=>-19484,
        'chou'=>-19479,
        'chu'=>-19467,
        'chuai'=>-19289,
        'chuan'=>-19288,
        'chuang'=>-19281,
        'chui'=>-19275,
        'chun'=>-19270,
        'chuo'=>-19263,
        'ci'=>-19261,
        'cong'=>-19249,
        'cou'=>-19243,
        'cu'=>-19242,
        'cuan'=>-19238,
        'cui'=>-19235,
        'cun'=>-19227,
        'cuo'=>-19224,
        'da'=>-19218,
        'dai'=>-19212,
        'dan'=>-19038,
        'dang'=>-19023,
        'dao'=>-19018,
        'de'=>-19006,
        'deng'=>-19003,
        'di'=>-18996,
        'dian'=>-18977,
        'diao'=>-18961,
        'die'=>-18952,
        'ding'=>-18783,
        'diu'=>-18774,
        'dong'=>-18773,
        'dou'=>-18763,
        'du'=>-18756,
        'duan'=>-18741,
        'dui'=>-18735,
        'dun'=>-18731,
        'duo'=>-18722,
        'e'=>-18710,
        'en'=>-18697,
        'er'=>-18696,
        'fa'=>-18526,
        'fan'=>-18518,
        'fang'=>-18501,
        'fei'=>-18490,
        'fen'=>-18478,
        'feng'=>-18463,
        'fo'=>-18448,
        'fou'=>-18447,
        'fu'=>-18446,
        'ga'=>-18239,
        'gai'=>-18237,
        'gan'=>-18231,
        'gang'=>-18220,
        'gao'=>-18211,
        'ge'=>-18201,
        'gei'=>-18184,
        'gen'=>-18183,
        'geng'=>-18181,
        'gong'=>-18012,
        'gou'=>-17997,
        'gu'=>-17988,
        'gua'=>-17970,
        'guai'=>-17964,
        'guan'=>-17961,
        'guang'=>-17950,
        'gui'=>-17947,
        'gun'=>-17931,
        'guo'=>-17928,
        'ha'=>-17922,
        'hai'=>-17759,
        'han'=>-17752,
        'hang'=>-17733,
        'hao'=>-17730,
        'he'=>-17721,
        'hei'=>-17703,
        'hen'=>-17701,
        'heng'=>-17697,
        'hong'=>-17692,
        'hou'=>-17683,
        'hu'=>-17676,
        'hua'=>-17496,
        'huai'=>-17487,
        'huan'=>-17482,
        'huang'=>-17468,
        'hui'=>-17454,
        'hun'=>-17433,
        'huo'=>-17427,
        'ji'=>-17417,
        'jia'=>-17202,
        'jian'=>-17185,
        'jiang'=>-16983,
        'jiao'=>-16970,
        'jie'=>-16942,
        'jin'=>-16915,
        'jing'=>-16733,
        'jiong'=>-16708,
        'jiu'=>-16706,
        'ju'=>-16689,
        'juan'=>-16664,
        'jue'=>-16657,
        'jun'=>-16647,
        'ka'=>-16474,
        'kai'=>-16470,
        'kan'=>-16465,
        'kang'=>-16459,
        'kao'=>-16452,
        'ke'=>-16448,
        'ken'=>-16433,
        'keng'=>-16429,
        'kong'=>-16427,
        'kou'=>-16423,
        'ku'=>-16419,
        'kua'=>-16412,
        'kuai'=>-16407,
        'kuan'=>-16403,
        'kuang'=>-16401,
        'kui'=>-16393,
        'kun'=>-16220,
        'kuo'=>-16216,
        'la'=>-16212,
        'lai'=>-16205,
        'lan'=>-16202,
        'lang'=>-16187,
        'lao'=>-16180,
        'le'=>-16171,
        'lei'=>-16169,
        'leng'=>-16158,
        'li'=>-16155,
        'lia'=>-15959,
        'lian'=>-15958,
        'liang'=>-15944,
        'liao'=>-15933,
        'lie'=>-15920,
        'lin'=>-15915,
        'ling'=>-15903,
        'liu'=>-15889,
        'long'=>-15878,
        'lou'=>-15707,
        'lu'=>-15701,
        'lv'=>-15681,
        'luan'=>-15667,
        'lue'=>-15661,
        'lun'=>-15659,
        'luo'=>-15652,
        'ma'=>-15640,
        'mai'=>-15631,
        'man'=>-15625,
        'mang'=>-15454,
        'mao'=>-15448,
        'me'=>-15436,
        'mei'=>-15435,
        'men'=>-15419,
        'meng'=>-15416,
        'mi'=>-15408,
        'mian'=>-15394,
        'miao'=>-15385,
        'mie'=>-15377,
        'min'=>-15375,
        'ming'=>-15369,
        'miu'=>-15363,
        'mo'=>-15362,
        'mou'=>-15183,
        'mu'=>-15180,
        'na'=>-15165,
        'nai'=>-15158,
        'nan'=>-15153,
        'nang'=>-15150,
        'nao'=>-15149,
        'ne'=>-15144,
        'nei'=>-15143,
        'nen'=>-15141,
        'neng'=>-15140,
        'ni'=>-15139,
        'nian'=>-15128,
        'niang'=>-15121,
        'niao'=>-15119,
        'nie'=>-15117,
        'nin'=>-15110,
        'ning'=>-15109,
        'niu'=>-14941,
        'nong'=>-14937,
        'nu'=>-14933,
        'nv'=>-14930,
        'nuan'=>-14929,
        'nue'=>-14928,
        'nuo'=>-14926,
        'o'=>-14922,
        'ou'=>-14921,
        'pa'=>-14914,
        'pai'=>-14908,
        'pan'=>-14902,
        'pang'=>-14894,
        'pao'=>-14889,
        'pei'=>-14882,
        'pen'=>-14873,
        'peng'=>-14871,
        'pi'=>-14857,
        'pian'=>-14678,
        'piao'=>-14674,
        'pie'=>-14670,
        'pin'=>-14668,
        'ping'=>-14663,
        'po'=>-14654,
        'pu'=>-14645,
        'qi'=>-14630,
        'qia'=>-14594,
        'qian'=>-14429,
        'qiang'=>-14407,
        'qiao'=>-14399,
        'qie'=>-14384,
        'qin'=>-14379,
        'qing'=>-14368,
        'qiong'=>-14355,
        'qiu'=>-14353,
        'qu'=>-14345,
        'quan'=>-14170,
        'que'=>-14159,
        'qun'=>-14151,
        'ran'=>-14149,
        'rang'=>-14145,
        'rao'=>-14140,
        're'=>-14137,
        'ren'=>-14135,
        'reng'=>-14125,
        'ri'=>-14123,
        'rong'=>-14122,
        'rou'=>-14112,
        'ru'=>-14109,
        'ruan'=>-14099,
        'rui'=>-14097,
        'run'=>-14094,
        'ruo'=>-14092,
        'sa'=>-14090,
        'sai'=>-14087,
        'san'=>-14083,
        'sang'=>-13917,
        'sao'=>-13914,
        'se'=>-13910,
        'sen'=>-13907,
        'seng'=>-13906,
        'sha'=>-13905,
        'shai'=>-13896,
        'shan'=>-13894,
        'shang'=>-13878,
        'shao'=>-13870,
        'she'=>-13859,
        'shen'=>-13847,
        'sheng'=>-13831,
        'shi'=>-13658,
        'shou'=>-13611,
        'shu'=>-13601,
        'shua'=>-13406,
        'shuai'=>-13404,
        'shuan'=>-13400,
        'shuang'=>-13398,
        'shui'=>-13395,
        'shun'=>-13391,
        'shuo'=>-13387,
        'si'=>-13383,
        'song'=>-13367,
        'sou'=>-13359,
        'su'=>-13356,
        'suan'=>-13343,
        'sui'=>-13340,
        'sun'=>-13329,
        'suo'=>-13326,
        'ta'=>-13318,
        'tai'=>-13147,
        'tan'=>-13138,
        'tang'=>-13120,
        'tao'=>-13107,
        'te'=>-13096,
        'teng'=>-13095,
        'ti'=>-13091,
        'tian'=>-13076,
        'tiao'=>-13068,
        'tie'=>-13063,
        'ting'=>-13060,
        'tong'=>-12888,
        'tou'=>-12875,
        'tu'=>-12871,
        'tuan'=>-12860,
        'tui'=>-12858,
        'tun'=>-12852,
        'tuo'=>-12849,
        'wa'=>-12838,
        'wai'=>-12831,
        'wan'=>-12829,
        'wang'=>-12812,
        'wei'=>-12802,
        'wen'=>-12607,
        'weng'=>-12597,
        'wo'=>-12594,
        'wu'=>-12585,
        'xi'=>-12556,
        'xia'=>-12359,
        'xian'=>-12346,
        'xiang'=>-12320,
        'xiao'=>-12300,
        'xie'=>-12120,
        'xin'=>-12099,
        'xing'=>-12089,
        'xiong'=>-12074,
        'xiu'=>-12067,
        'xu'=>-12058,
        'xuan'=>-12039,
        'xue'=>-11867,
        'xun'=>-11861,
        'ya'=>-11847,
        'yan'=>-11831,
        'yang'=>-11798,
        'yao'=>-11781,
        'ye'=>-11604,
        'yi'=>-11589,
        'yin'=>-11536,
        'ying'=>-11358,
        'yo'=>-11340,
        'yong'=>-11339,
        'you'=>-11324,
        'yu'=>-11303,
        'yuan'=>-11097,
        'yue'=>-11077,
        'yun'=>-11067,
        'za'=>-11055,
        'zai'=>-11052,
        'zan'=>-11045,
        'zang'=>-11041,
        'zao'=>-11038,
        'ze'=>-11024,
        'zei'=>-11020,
        'zen'=>-11019,
        'zeng'=>-11018,
        'zha'=>-11014,
        'zhai'=>-10838,
        'zhan'=>-10832,
        'zhang'=>-10815,
        'zhao'=>-10800,
        'zhe'=>-10790,
        'zhen'=>-10780,
        'zheng'=>-10764,
        'zhi'=>-10587,
        'zhong'=>-10544,
        'zhou'=>-10533,
        'zhu'=>-10519,
        'zhua'=>-10331,
        'zhuai'=>-10329,
        'zhuan'=>-10328,
        'zhuang'=>-10322,
        'zhui'=>-10315,
        'zhun'=>-10309,
        'zhuo'=>-10307,
        'zi'=>-10296,
        'zong'=>-10281,
        'zou'=>-10274,
        'zu'=>-10270,
        'zuan'=>-10262,
        'zui'=>-10260,
        'zun'=>-10256,
        'zuo'=>-10254
    );
    //全部拼音
    public function getAllPY($chinese, $delimiter = '', $length = 0) {
        $chinese =iconv("UTF-8","gb2312", $chinese);
        $py = $this->zh_to_pys($chinese, $delimiter);
        if($length) {
            $py = substr($py, 0, $length);
        }
        return $py;
    }
    //拼音首个字母
    public function getFirstPY($chinese){
        $chinese =iconv("UTF-8","gb2312", $chinese);
        $result = '' ;
        for ($i=0; $i<strlen($chinese); $i++) {
            $p = ord(substr($chinese,$i,1));
            if ($p>160) {
                $q = ord(substr($chinese,++$i,1));
                $p = $p*256 + $q - 65536;
            }
            $result .= substr($this->zh_to_py($p),0,1);
        }
        return $result ;
    }


    //-------------------中文转拼音--------------------------------//
    private function zh_to_py($num, $blank = '') {
        if($num>0 && $num<160 ) {
            return chr($num);
        } elseif ($num<-20319||$num>-10247) {
            return $blank;
        } else {
            foreach ($this->pylist as $py => $code) {
                if($code > $num) break;
                $result = $py;
            }
            return $result;
        }
    }


    private function zh_to_pys($chinese, $delimiter = ' ', $first=0){
        $result = array();
        for($i=0; $i<strlen($chinese); $i++) {
            $p = ord(substr($chinese,$i,1));
            if($p>160) {
                $q = ord(substr($chinese,++$i,1));
                $p = $p*256 + $q - 65536;
            }
            $result[] = $this->zh_to_py($p);
            if ($first) {
                return $result[0];
            }
        }
        return implode($delimiter, $result);
    }
}
?>