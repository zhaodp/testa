<?php
/**
 * 投诉监控列表
 * User: zhaoyingshuang
 * Date: 15-04-09
 * Time: 上午1:07
 * To change this template use File | Settings | File Templates.
 */

class MonitorAction extends CAction {
    public function run()
    {
        $start_time = Yii::app()->request->getQuery('start_time');
        $end_time = Yii::app()->request->getQuery('end_time');
        $category = Yii::app()->request->getQuery('category');
        $top_type = Yii::app()->request->getQuery('complain_maintype');
        $second_type = Yii::app()->request->getQuery('sub_type');
        $group = Yii::app()->request->getQuery('task_group');
        $operator = Yii::app()->request->getQuery('task_operator');
        $type = Yii::app()->request->getQuery('type');

        $start_time = !empty($start_time)?$start_time:date('Y-m-d 00:00',time()-24*3600);
        $end_time = !empty($end_time)?$end_time:date('Y-m-d 23:59',time());
        $category = !empty($category)?$category:-1;
        $top_type = !empty($top_type)?$top_type:-1;
        $second_type = !empty($second_type)?$second_type:-1;
        $type = !empty($type)?$type:'stat_type';


        //查询分类
        $complainType = CustomerComplainType::model()->getComplainTypeByID(0);
        $typeArr = array('-1'=>'全部');
        foreach($complainType as $item){
            $typeArr[$item->id] = $item->name;
        }
        $secondTypeList = array('-1' => '---全部---');
        if($top_type != -1){
            $data = CustomerComplainType::model()->getComplainTypeByID((int)$top_type);
            if(!empty($data)){
                foreach($data as $d){
                    $secondTypeList[$d->id] = $d->name;
                }
            }
        }
        //查询大类
        $cate = CustomerComplainType::model()->getComplainCategory();
        $cateArr = array('-1'=>'全部');
        foreach($cate as $item){
            $cateArr[$item['category']] = $item['category'];
        }
        //查询任务组 和 人
        $groupArr = array('-1'=>'全部');
        $operatorArr = array('-1'=>'全部');

        $db = Yii::app()->db_readonly;
        if ($second_type!=-1) {
            $subType = array(array('id'=>$second_type));
        } else {
            if ($category!=-1 && $top_type!=-1) {
                $subType = CustomerComplainType::model()->getSubTypeByIDAndCategory($top_type, $category);
            } else if ($category!=-1) {
                $subType = CustomerComplainType::model()->getSubTypeByCategory($category);
            } else if ($top_type!=-1) {
                $subType = CustomerComplainType::model()->getComplainTypeByID($top_type);
            } else {
                $subType = CustomerComplainType::model()->getAllSubType();
            }
        }

        $condition = '1=1';
        $param = array();
        if (!empty($start_time)) {
            $condition .= ' and create_time>=:ct';
            $param[':ct'] = $start_time;
        }
        if (!empty($end_time)) {
            $condition .= ' and create_time<=:et';
            $param[':et'] = $end_time;
        }

        if (!empty($operator)) {
            //暂不处理
        } else {

        }

        //统计
        $listArr = array();
        switch($type) {
            case 'stat_type':
                //var_dump($subType);die;
                //按二级分类分组，投诉数量、结案数量、平均响应时间、最快响应时间、最慢响应时间、平均结案时间、最快结案时间、最慢结案时间
                foreach ($subType as $k=>$v) {
                    //查询分类详情
                    $typeInfo = CustomerComplainType::model()->getCtypeByID($v['id']);
                    $parentTypeInfo = CustomerComplainType::model()->getCtypeByID($typeInfo['parent_id']);
                    //查询投诉
                    $con = $condition.' and complain_type='.$v['id'];
                    $sql = 'select id, create_time from '.CustomerComplain::model()->tableName().' where '.$con;
                    //echo $sql;die;
                    $complainList = $db->createCommand($sql)->query($param);
                    $complainNum = count($complainList);
                    if ($complainNum == 0) {
                        continue;
                    }
                    //echo $v['id'].'--';
                    $closingNum = 0;
                    $responseAllTime = 0;
                    $closingAllTime = 0;
                    $rTime = array();
                    $cTime = array();
                    $hasResponse = false;
                    $hasClose = false;
                    foreach ($complainList as $k1=>$v1) {
                        //查询处理日志
                        $sql = 'select id, ptime, node from '.CnodeLog::model()->tableName().' where customer_id='.$v1['id'].' and node!=93';//过滤掉增加分类的节点
                        $log = $db->createCommand($sql)->queryAll();
                        if ($log) {
                            $hasResponse = true;
                            //响应时间
                            $responseTime = strtotime($log[0]['ptime']) - strtotime($v1['create_time']);//响应时间
                            $responseAllTime += $responseTime;//总计响应时间
                            //echo $responseTime.'----';
                            $rTime[] = $responseTime;

                            //是否结案判断
                            for ($i = count($log)-1; $i>=0; $i--) {
                                if ($log[$i]['node'] == 8) {
                                    $hasClose = true;
                                    $closingNum ++;
                                    //结案时间
                                    $closingTime = strtotime($log[$i]['ptime']) - strtotime($v1['create_time']);
                                    $closingAllTime += $closingTime;//总结案时间
                                    $cTime[] = $closingTime;
                                    break;
                                }
                            }
                        } else {
                            $responseTime = time() - strtotime($v1['create_time']);
                            $rTime[] = $responseTime;
                        }

                    }
                    if (empty($rTime)) {
                        $fastestRtime = 0;
                        $lowestRtime = 0;
                    } else {
                        rsort($rTime);
                        $lowestRtime = $rTime[0];
                        if ($hasResponse) {
                            $fastestRtime = array_pop($rTime);
                        } else {
                            $fastestRtime = 0;
                        }

                    }
                    if (empty($cTime)) {
                        $fastestCtime = 0;
                        $lowestCtime = 0;
                    } else {
                        rsort($cTime);
                        $lowestCtime = $cTime[0];
                        if ($hasClose) {
                            $fastestCtime = array_pop($cTime);
                        } else {
                            $fastestCtime = 0;
                        }
                    }
                    //$listArr[$v['id']] = array();
                    $listArr[$v['id']]['category'] = $typeInfo['category'];
                    $listArr[$v['id']]['top_type'] = $parentTypeInfo['name'];
                    $listArr[$v['id']]['second_type'] = $typeInfo['name'];
                    $listArr[$v['id']]['complainNum'] = $complainNum;
                    $listArr[$v['id']]['closingNum'] = $closingNum;
                    $listArr[$v['id']]['avgRtime'] = $complainNum!=0?round(($responseAllTime/3600)/$complainNum,2):0;//平均响应时间（小时）
                    $listArr[$v['id']]['fastestRtime'] = round($fastestRtime/3600,2);
                    $listArr[$v['id']]['lowestRtime'] = round($lowestRtime/3600,2);
                    $listArr[$v['id']]['avgCtime'] = $closingNum!=0?round(($closingAllTime/3600)/$closingNum,2):0;//平均结案时间（小时）
                    $listArr[$v['id']]['fastestCtime'] = round($fastestCtime/3600,2);
                    $listArr[$v['id']]['lowestCtime'] = round($lowestCtime/3600,2);
                }
                CustomerComplainMonitorDownload::model()->saveData('download_stat_type_'.Yii::app()->user->user_id,$listArr);
                //var_dump($listArr);die;
                break;
            case 'stat_operator':
                echo "<meta charset='utf-8'/>";
                echo '<script>alert("还未实现哦1！");window.history.go(-1);window.close();</script>';
                Yii::app ()->end ();
                exit ();
                //按投诉任务人分组，投诉数量、结案数量、平均响应时间、最快响应时间、最慢响应时间、平均结案时间、最快结案时间、最慢结案时间
                break;
            case 'process_type':
                //按二级分类分组，投诉数量、未及时响应数、未及时跟进数、未及时结案数、累计未响应数、累计未跟进数、累计未结案数
                //var_dump($subType);die;
                foreach ($subType as $k=>$v) {
                    //查询分类详情
                    $typeInfo = CustomerComplainType::model()->getCtypeByID($v['id']);
                    $parentTypeInfo = CustomerComplainType::model()->getCtypeByID($typeInfo['parent_id']);
                    //var_dump($typeInfo);die;
                    $should_response_hour = $typeInfo['should_response_hour'];
                    $should_follow_hour = $typeInfo['should_follow_hour'];
                    $should_closing_hour = $typeInfo['should_closing_hour'];

                    //查询投诉
                    $con = $condition.' and complain_type='.$v['id'];
                    $sql = 'select id, complain_type, create_time from '.CustomerComplain::model()->tableName().' where '.$con;
                    //echo $sql.'----';
                    $complainList = $db->createCommand($sql)->query($param);
                    $complainNum = count($complainList);
                    if ($complainNum == 0) {
                        continue;
                    }
                    //echo $v['id'].'--';
                    $delayedRnum = 0;
                    $delayedFnum = 0;
                    $delayedCnum = 0;
                    $unRnum = 0;
                    $unFnum = 0;
                    $unCnum = 0;
                    foreach ($complainList as $k1=>$v1) {
                        //查询处理日志
                        $sql = 'select id, ptime, node from '.CnodeLog::model()->tableName().' where customer_id='.$v1['id'].' and node!=93';//过滤掉增加分类的节点
                        $log = $db->createCommand($sql)->queryAll();
                        if (!$log) {//无日志
                            $responseTime = time() - strtotime($v1['create_time']);
                        } else {//有日志
                            $responseTime = strtotime($log[0]['ptime']) - strtotime($v1['create_time']);
                        }

                        if ($should_response_hour < round($responseTime/3600,2)) {//未及时响应
                            $delayedRnum ++;
                        }

                        //是否结案判断
                        $close = false;
                        $closingTime = 0;
                        for ($i = count($log)-1; $i>=0; $i--) {
                            if ($log[$i]['node'] == 8) {
                                $close = true;
                                //结案时间
                                $closingTime = strtotime($log[$i]['ptime']) - strtotime($v1['create_time']);
                                break;
                            }
                        }

                        $logNum = count($log);//日志数
                        //echo $logNum.'---';
                        if ($logNum <= 0) {//未响应
                            $unRnum ++;
                        }
                        if ($close) {//已结案
                            if ($logNum>1) {
                                $followTime = strtotime($log[$logNum-1]['ptime']) - strtotime($log[$logNum-2]['ptime']);
                            } else {
                                $followTime = strtotime($log[0]['ptime']) - strtotime($v1['create_time']);
                            }
                        } else {//未结案
                            $closingTime = time() - strtotime($v1['create_time']);
                            if ($logNum>=1) {
                                $followTime = time() - strtotime($log[$logNum-1]['ptime']);
                            } else {
                                $followTime = 0;
                            }
                            if ($logNum<2) {
                                $unFnum ++;
                            }
                            $unCnum ++;
                        }

                        if ($should_follow_hour < round($followTime/3600,2)) {//未及时跟进
                            $delayedFnum ++;
                        }
                        if ($should_closing_hour < round($closingTime/3600,2)) {//未及时结案
                            $delayedCnum ++;
                        }
                    }
                    //$listArr[$v['id']] = array();
                    $listArr[$v['id']]['category'] = $typeInfo['category'];
                    $listArr[$v['id']]['top_type'] = $parentTypeInfo['name'];
                    $listArr[$v['id']]['second_type'] = $typeInfo['name'];
                    $listArr[$v['id']]['complainNum'] = $complainNum;
                    $listArr[$v['id']]['delayedRnum'] = $delayedRnum;//未及时响应数
                    $listArr[$v['id']]['delayedFnum'] = $delayedFnum;//未及时跟进数
                    $listArr[$v['id']]['delayedCnum'] = $delayedCnum;//未及时结案数
                    $listArr[$v['id']]['unRnum'] = $unRnum;//累计未响应数
                    $listArr[$v['id']]['unFnum'] = $unFnum;//累计未跟进数
                    $listArr[$v['id']]['unCnum'] = $unCnum;//累计未结案数
                }
                CustomerComplainMonitorDownload::model()->saveData('download_process_type_'.Yii::app()->user->user_id,$listArr);
                break;
            case 'process_operator':
                //按投诉任务人分组，投诉数量、未及时响应数、未及时跟进数、未及时结案数、累计未响应数、累计未跟进数、累计未结案数
                echo "<meta charset='utf-8'/>";
                echo '<script>alert("还未实现哦！");window.history.go(-1);window.close()</script>';
                Yii::app ()->end ();
                exit ();
                break;
            case 'download_stat_type':
                $data = CustomerComplainMonitorDownload::model()->getData('download_stat_type_'.Yii::app()->user->user_id);//查数据
                if ($data) {
                    $keynames = array (
                        'category' => '投诉大类',
                        'top_type' => '投诉一级分类',
                        'second_type' => '投诉二级分类',
                        'complainNum' => '投诉数量',
                        'closingNum' => '结案数量',
                        'avgRtime' => '平均响应时间',
                        'fastestRtime' => '最快响应时间',
                        'lowestRtime' => '最慢响应时间',
                        'avgCtime' => '平均结案时间',
                        'fastestCtime' => '最快结案时间',
                        'lowestCtime' => '最慢结案时间',
                    );

                    BonusAreaStatic::model ()->down_xls ( $data, $keynames );
                } else {
                    echo "<meta charset='utf-8'/>";
                    echo '<script>alert("未查询出匹配数据，请确认各条件后重试！");window.close()</script>';
                    Yii::app ()->end ();
                    exit ();
                }
                break;
            case 'download_stat_operator':
                break;
            case 'download_process_type':
                $data = CustomerComplainMonitorDownload::model()->getData('download_process_type_'.Yii::app()->user->user_id);//查数据
                if ($data) {
                    $keynames = array (
                        'category' => '投诉大类',
                        'top_type' => '投诉一级分类',
                        'second_type' => '投诉二级分类',
                        'complainNum' => '投诉数量',
                        'delayedRnum' => '未及时响应数',
                        'delayedFnum' => '未及时跟进数',
                        'delayedCnum' => '未及时结案数',
                        'unRnum' => '累计未响应数',
                        'unFnum' => '累计未跟进数',
                        'unCnum' => '累计未结案数',
                    );

                    BonusAreaStatic::model ()->down_xls ( $data, $keynames );
                } else {
                    echo "<meta charset='utf-8'/>";
                    echo '<script>alert("未查询出匹配数据，请确认各条件后重试！");window.close()</script>';
                    Yii::app ()->end ();
                    exit ();
                }
                break;
            case 'download_process_operator':
                break;
            default:
                exit();
                break;
        }

        $this->controller->render('monitor_'.$type,array(
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'category'=>$category,
            'top_type'=>$top_type,
            'second_type'=>$second_type,
            'secondTypeList'=>$secondTypeList,

            'task_group'=>$group,
            'task_operator'=>$operator,
            'typeList'=>$typeArr,
            'cateList'=>$cateArr,
            'groupList'=>$groupArr,
            'operatorList'=>$operatorArr,

            'list'=>$listArr
        ));
    }
}