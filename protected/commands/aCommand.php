<?php
class ACommand extends CConsoleCommand {

	public function actioninitAuth(){
        $arr = '运营	大区经理	贺明星|刘杰|薛雷
运营	分公司财务	陈宏阳|陈朋飞|侯茵茵|胡哲|霍倩|姜晓妃|蒋瑞娟|李倩倩|李嵩|李征|廖厚红|林伟凤|刘长粉|孙丹婷|孙锦琳|唐俊杰|吴健|吴晓玲|吴晓泉|谢国强|徐娜|易亚燕|张洁|张思燕|张魏魏
运营	分公司经理	陈海俊|崔崇|付德森|郭军|何剑松|何向宇|洪英利|黄志达|蒋雪松|李博|李磊|李友文|林徐茂|刘凯|刘磊|麦惠东|米宏|宁凯|覃兴振|王海霖|王鹏|杨明智|杨小冬|于洋01|张浩彬|赵昱龙|周志伟|朱天亮|朱天亮咸阳
运营	分公司司管	曾国平|陈宏阳|陈露|陈朋飞|陈玉芬|方森旭|房定轩|高云飞|何天旻|侯茵茵|胡哲|黄慧明|江平|姜晓妃|蒋瑞娟|井娅倩|李玲玉|李敏|李倩倩|李嵩|李弦|李颖杰|李征|廖厚红|林伟凤|刘长粉|吕刚|孙朝|孙丹婷|孙建霞|孙锦琳|唐俊杰|王斌|魏宏涛|吴健|吴晓玲|吴晓泉|肖文波|谢国强|徐广杨|徐娜|易亚燕|于丙肖|张洁|张思燕|张魏魏|张新旺|张雅倩|赵强
运营	分公司支持	耿伟|韩铁锁|汪默|王有吉|闻晓强|张福静|
运营	分公司主管	蔡潮鑫|陈开忠|陈礼波|陈泽鹏|崔永钦|高剑栋|何如军|黄文钊|冀党锋|荆国杰|柯文辉|孔越群|李勇|李宇|廉杰|林治岩|林智源|刘林|刘伟祥|刘小卫|刘智刚|马佩翔|邱明明|饶庆安|任帅|沈春旺|王俊钢|王亮亮|吴涛|项付根|肖安平|辛克玉|徐鹏|薛文华|闫成光|杨猛|杨笑|殷俊|袁海东|张凯01|张强|张燕京|赵晨|赵文波|周聪磊|周昆|周伟|朱铭良|朱燕斌
运营	物料采购	孙冉冉|张玉婷
运营	员工培训	许兴银
运营	运营部经理	杨祎琦
运营	运营部助理	李思思
市场	BD合作	曹丽娜
市场	VIP管理	金竹|孟欣
市场	大区负责人	李凯|李新宇|薛伟|张凯|张文月|赵文哲
市场	内部审核	李凯|李新宇|薛伟|张凯|张谦|张文月|赵文哲
市场	市场部外派	安洁|曹昌|曹磊|曹志强|陈伟|陈晓龙|杜毅|胡勇|金虎|刘浏|吕霖|孙宝利|孙浩溟|唐龙|向巍|杨东飞|杨政胤|张志刚|赵正冉|朱含啸|朱玉金
市场	市场部总监	柳柳
市场	市场运营	曹丽娜|金竹
市场	物料采购	王琳|杨冉|张园园|赵兴
市场	优惠券申请	曹丽娜|李凯|李新宇|薛伟|张凯|张谦|张文月|赵文哲
市场	优惠券审核	金竹
财务	财务部经理	曾艳
财务	财务部总监	孙英珍|张子超
财务	出纳	高华
财务	发票	张多多
财务	分公司账务处理	林莉|宋英芳|杨成燕
财务	核算	陆金霜
财务	信息费结算	丁润华
客服	处理组主管	刘忱	品质监控组
客服	订单组兼职	敖曼|巴兴|白静|车云鹏|陈冰|陈超|陈强1|陈子阳|崔川|崔志|丁丁|丁国栋|丁娇|杜江红2|方纪平1|冯淑雯|冯淑宇|高金凤|高志鹏|葛士国1|管峰|管晓楠|韩杰|韩哲1|郝伟伟|黄士涛|霍军伟|贾志彬|江丽丽1|雷秋蝶|李春红|李昊|李继阳|李丽丽|李梦琦|李群星|李颖|梁健|刘树存|刘匣|刘晓亮1|刘亚京|刘义超|卢春艳|卢卫平|卢一新|马海超|马莹|苗润营|明亚婷|邱敏|任晶晶|司林林1|孙慧娟|孙延召|唐敏|田金龙|田璐|王波|王静|王文玲|王羽|王治钧|王子鹏|吴雪|肖秋妹|熊睿|许彦飞|杨凤娟|杨俊平|杨茂|杨铭|杨升|杨雪03|虞凡|袁名珠|张桂旗|张李|张萌|张萌萌|张琪|张芮|张瑞姣|张文雷|张欣亚|张耀玥|赵芳芳|赵洪福|赵金娟|郑莎莎|钟亚磊1	订单组
客服	订单组培训	马艳杰|石建威	订单组
客服	订单组全职	敖强|陈龙|陈恬|丁双|杜文肖|高国伟|黄卫杰|蒋玉坤|金瑞成|刘丹丹|刘京春|牛犇|彭艳|乔丽|沈振东|石雪松|史贵省|苏若雷|王成龙|王丹1|王光宇|王婧|王媛媛|邢玥|杨鹤|杨正媛|姚劭峰|叶涛|袁峥|张红艳|张明	订单组
客服	订单组质检	李佳|张莹01	订单组
客服	订单组主管	勾哲|李邦木	订单组
客服	订单组组长	李天宇|马立元|张宸啟|张厚禄|张利敏	订单组
客服	服务组（助理）	黄涛1	服务组
客服	服务组主管	颜小琦	服务组
客服	服务组组员	白鑫晶|常国坡|成吉宏|高前康|郭祎璠|李萌|刘佳良|木申申|任爽|王倩|王秀丽|杨羡|张金娜	服务组
客服	服务组组长	杨雪2|张艳敏	服务组
客服	交通事故处理专员	李萌01|李琼	品质监控组
客服	交通事故助理	李亭	品质监控组
客服	客服中心专员	邢晔
客服	客服中心总监	赵新磊
客服	质量监控	邱晨
公关	媒体	张东鹏
公关	品牌公关部经理	李小光
公关	设计	龙涛
公关	微博	李帆
公关	用户运营	李林蔚|周达洋
技术	H5	刘宇
技术	KK拼车	焦龙|李金山|孙彦哲|王洪亮|苑明|张同凯|朱瑞
技术	UI	黄姗|刘天然
技术	测试	陈鑫|李盟|马学云|杨名利
技术	产品	龚晓萍|刘震林|乔博|石达|武显赫|袁荣|
技术	后台开发	艾国信|崔路哲|邓小明|董坤|冯广祥|技术李伟|李定才|刘团望|丘建平|王健|王子超|肖波|杨程介|于杨
技术	数据中心	高峰|李迿
技术	司机端开发	陈阳|李永峰
技术	用户端开发	郭平|申广亮|宋增宾
管委会	CEO	黄宾|杨家军
管委会	COO	孙景川
管委会	董事会	牛立雄
行政	行政组	蔡雪梅|杨婷婷|邓安妮|李伟|牟凯|冯樵|王亮';
//        $arr = '客服	订单组质检	李佳|张莹01	订单组
//客服	订单组主管	勾哲|李邦木	订单组
//行政	行政组	蔡雪梅|杨婷婷|邓安妮|李伟|牟凯|冯樵|王亮';
//        张明
        $bingji_group = array();
        $line_arr = explode("\n",$arr);
        foreach($line_arr as $line){
            $data = explode("	",$line);
            $dep_name = $data[0];
            $role_name = $data[1];
            $user = $data[2];
            $group_name = isset($data[3]) ? $data[3] : '';
            $group_info = '';
            if($group_name){
                $group_info = AdminDepartment::model()->find('name like :name',array(':name'=>$group_name));
                if(!$group_info){echo 'empty group info:'.$group_name;die;}
            }
            $dep_info = AdminDepartment::model()->find('name like :name',array(':name'=>$dep_name));
//            print_r($dep_info);
//            echo 'group_name:';
//            print_r($group_name);echo ';-----';
            if($group_name){
                $role_info = AdminRole::model()->find('name like :name and department_id = :dep_id',array(':name'=>$role_name,':dep_id'=>$group_info->id));
            }else{
                $role_info = AdminRole::model()->find('name like :name and department_id = :dep_id',array(':name'=>$role_name,':dep_id'=>$dep_info->id));
            }
            if(!$role_info) {echo 'empty role name:'.$role_name;die;}

            //$role_info = AdminRole::model()->find('name like :name and department_id = :dep_id',array(':name'=>$role_name,':dep_id'=>$dep_info->id));
//            echo $role_name.'----';print_r($role_info);echo $role_name;
            $old_user_actions = $bingji_tmp = array();
            //if($role_info->department_id == $dep_info->id){
                $users_arr = explode('|',$user);
                $users_arr = array_filter($users_arr);
//                print_r($users_arr);
                foreach($users_arr as $user_name){

                    $user_name = trim($user_name);
                    $old_user_info = AdminUser::model()->find('name = :name',array(':name'=>trim($user_name)));
                    if(!$old_user_info) {
                        echo $user_name.'&&-- no old_uer info --'."\n";
                        continue;
                    }
                    $user_info_new = AdminUserNew::model()->find('name = :name',array(':name'=>$user_name));
                    if($user_info_new){
                        //修改用户的部门和小组信息
                        if($user_info_new->department_id != 0 && $user_info_new->department_id != $dep_info->id){
                            echo '重复的用户部门'.$user_name.$dep_info->id.'---'.$user_info_new->department_id."\n";
                        }
                        $contri = ( $group_name ) ? array('department_id' => $dep_info->id,
                            'group_id'=>$group_info->id) : array('department_id' => $dep_info->id);
                        AdminUserNew::model()->updateByPk($user_info_new->id,$contri);

                        //更新用户角色组信息
                        //
                        //$checkUser2RoleExist = AdminUser2role::model()->find('');
                        $userrole_mod = new AdminUser2role();
                        $array = array('user_id'=>$user_info_new->id,'role_id'=>$role_info->id);
                        $userrole_mod->attributes = $array;
                        $userrole_mod->save();
                    }

//                    //用户就表里的权限查找出来
//                    $role_array_tmp= explode(',',$old_user_info->roles);
//                    sort($role_array_tmp);
//                    $old_user_group_info = AdminGroup::model()->getGroupModsList($role_array_tmp);
//                    //echo 'user_name old_group——info'."\n";
//                    //print_r($old_user_group_info);
//                    //print_r($old_user_group_info);
//                    if($old_user_group_info){
//                        $action_arr = array();
//                        foreach($old_user_group_info as $group){
//                            if(isset($group['mods']) && $group['mods']){
//                                foreach($group['mods'] as $action_detail){
//                                    //if($action_detail['id'] == 554) {echo 'ok';die;}
//                                    $action_arr[$action_detail['id']] = $action_detail['id'];
//                                }
//                            }
//                        }
//
//                        if(!empty($action_arr) && count($action_arr) > 0) $old_user_actions[] =  $action_arr;
//                        else echo $user_name.'out';
//                        //echo $user_name.'Name.'; print_r($action_arr);
//                        //echo '----------------'."\n";
//                        //echo '('. count($action_arr);echo ')'."\n";
//                    }
//                    else {
//                        echo $user_name .'have no group_info'."\n";
//                        continue;
//                    }

                    $action_arr = $this->getUserOldActions($user_name);

                    if($action_arr) $old_user_actions[] = $action_arr;
                }

                if(count($old_user_actions) > 1){

                    for($i = 1 ;$i < count($old_user_actions)  ; $i ++){
                        if($i == 1){
                            $bingji_tmp = array_merge($old_user_actions[0],$old_user_actions[1]);
                            //echo 'old actions 0';print_r($old_user_actions[0]);
                            //echo 'old actions 1 '; print_r($old_user_actions[1]);
                            //echo 'bingji 1';print_r($bingji_tmp);
                        }
                        else{
                            $bingji_tmp = array_merge($bingji_tmp,$old_user_actions[$i]);
                            //echo 'old actions'.$i; print_r($old_user_actions);
                            //echo 'bingji 2 ';print_r($bingji_tmp);
                        }
                        $bingji_tmp = array_filter($bingji_tmp);
                        $bingji_tmp = array_unique($bingji_tmp);
                    }
                }
                elseif(!empty($old_user_actions)){

                    //$bingji_tmp = array_filter($old_user_actions[0]);
                    $bingji_tmp = $old_user_actions[0];
                }

            //echo 'bingji all:'; print_r($bingji_tmp);
               if(!$dep_info->id){ echo 'empty dep info:'.$dep_name;}

                //if($dep_info->id == 4 && !empty($group_info) && $group_info->id == 9){
                    //echo '4,9';print_r($bingji_tmp);
                    //if(empty($bingji_tmp)){ echo 'empty_bingji:'.$user_name;}
                //}
                $bingji = $bingji_tmp; //角色组 权限集合
                if($group_name){
                    //echo $user.'^^---';
                    //echo $group_name;
                    //print_r($bingji);
                    $bingji_group[$dep_info->id][$group_info->id][] = $bingji; //部门id ,小组id(也是dep_id)
                }
                else{
                    $bingji_dep[$dep_info->id][] = $bingji;  //整理部门管理员角色组 权限
                }

                //$bingji = '';
                //把权限集合分配给角色组
                //同步role2action



                if($bingji){
                    foreach($bingji as $action_id){
                        $role_action_mod = new AdminRole2action();
                        $check_exist =  AdminRole2action::model()->find('role_id = :role_id and action_id = :action_id',array(':role_id'=>$role_info->id,':action_id'=>$action_id));
                        if(!$check_exist){
                            $arr = array('role_id'=>$role_info->id,
                                'action_id' => $action_id,
                                'create_time' => date('Y-m-d H:i:s'));
                            $role_action_mod->attributes=$arr;
                            $role_action_mod -> save();
                        }
                        else{
                            $check_exist->status = AdminRole2action::STATUS_NORMAL;
                            $check_exist->save();
                        }
                    }
                }




            //print_r($data);die;
        }
        //echo 'group_bingji:';print_r($bingji_group);die;


        //整理小组权限 合并到小组管理员角色组

        $bingji_tmp = $bingji_tmp_group = array();
        if(!empty($bingji_group)){
            foreach($bingji_group as $dep_id => $d1){
                foreach($d1 as $group_id => $actions){ //group_id => department_id
                    if(count($actions) > 1){
                        for($i = 1 ;$i < count($actions)  ; $i ++){
                            if($i == 1){
                                $bingji_tmp_group = array_merge($actions[0],$actions[1]);
                            }
                            else{
                                $bingji_tmp_group = array_merge($bingji_tmp_group,$actions[$i]);
                            }
                            $bingji_tmp_group = array_filter($bingji_tmp_group);
                            $bingji_tmp_group = array_unique($bingji_tmp_group);
                        }
                    }elseif(!empty($bingji_tmp_group)){
                        $bingji_tmp_group  = array_filter($actions[0]);
                        $bingji_tmp_group = array_unique($bingji_tmp_group);
                    }
                    $bingji_dep[$dep_id][] = $bingji_tmp_group;
                    //找出group_id 对应的小组默认角色组id
                    $group_role_info = AdminRole::model()->find('department_id = :dep_id and type = :type',array(':dep_id'=>$group_id,':type'=>2));
                    foreach($bingji_tmp_group as $action_id){
                        //整理完每个小组的权限后 入库 分配给小组管理员角色组
                        //然后入库 role2action table
                        $role_action_mod = new AdminRole2action();
                        $check_exist =  AdminRole2action::model()->find('role_id = :role_id and action_id = :action_id',array(':role_id'=>$group_role_info->id,':action_id'=>$action_id));
                        if(!$check_exist){
                            $arr = array(
                                'role_id'   =>$group_role_info->id,
                                'action_id' => $action_id,
                                'create_time' => date('Y-m-d H:i:s'));
                            $role_action_mod->attributes=$arr;
                            $role_action_mod -> save();
                        } else {
                            $check_exist->status = AdminRole2action::STATUS_NORMAL;
                            $check_exist->save();
                        }
                    }
                }
            }
        }
        //整理每个部门的权限 付给部门管理员角色组
        /*
         * $dep_data = array(
            1 => array( //dep_id
                2=>array(234,5452,2462,246), //role_id -> action_id
                3=>array(345,645,34,634),
                4=>array(234,5452,2462,246),
                5=>array(234,5452,2462,246),
                6=>array(234,5452,2462,246),
            ),
            2 => array(
                3=>array(234,5452,2462,246),
                34=>array(345,645,34,634),
                54=>array(234,5452,2462,246),
                65=>array(234,5452,2462,246),
                66=>array(234,5452,2462,246),
            ),
        );
        */
        $dep_auth = $bingji_tmp =array();

        foreach($bingji_dep as $dep_id => $v) {
            if(count($v) > 1 ){
                for($i = 1 ;$i < count($v)  ; $i ++){
                    if($i == 1){
                        $bingji_tmp = array_merge($v[0],$v[1]);
                    }
                    else{
                        $bingji_tmp = array_merge($bingji_tmp,$v[$i]);
                    }
                    $bingji_tmp = array_filter($bingji_tmp);
                    $bingji_tmp = array_unique($bingji_tmp);
                }
            }else if(!empty($v)){
                $bingji_tmp = $v[0];
            }

            //整理完每个部门的权限后 入库 分配给部门管理员角色组

            //先要知道dep_id对应的role_id
            $role_main_info = AdminRole::model()->find('department_id = :dep_id and type = :type',array(':dep_id'=>$dep_id,':type'=>1));

            //然后入库 role2action table
            foreach($bingji_tmp as $action_id){
                $role_action_mod = new AdminRole2action();
                $check_exist =  AdminRole2action::model()->find('role_id = :role_id and action_id = :action_id',array(':role_id'=>$role_main_info->id,':action_id'=>$action_id));
                if(!$check_exist){
                    $arr = array(
                        'role_id'   =>$role_main_info->id,
                        'action_id' => $action_id,
                        'create_time' => date('Y-m-d H:i:s'));
                    $role_action_mod->attributes=$arr;
                        $role_action_mod -> save();
                } else {
                    $check_exist->status = AdminRole2action::STATUS_NORMAL;
                        $check_exist->save();
                }
            }
        }

    }


    public function getUserOldActions($user_name){

        $old_user_info = AdminUser::model()->find('name = :name',array(':name'=>trim($user_name)));
        $roles_info = AdminRoles::model()->findAll();
        $user_roles = $old_user_info->roles;
        $role_array_tmp = array();
        if($user_roles){
            $role_array_tmp= explode(',',$user_roles);
        }
        $actions = array();
        if($role_array_tmp && $roles_info){
            foreach($roles_info as $action_info){
                if($this->havingPermissions($action_info->controller,$action_info->action,$role_array_tmp)){
                    $actions[$action_info->id] = $action_info->id;
                }
            }
        }
        return $actions;

    }

    public function havingPermissions($controller, $action, $user_roles) {
        $access = 0;

        $roles = AdminRoles::model()->checkPermissions($controller, $action);
        if ($roles && $user_roles) {
            foreach($roles['roles'] as $role) {
                $role_name = AdminGroup::model()->getName($role);
                switch ($role_name) {
                    case '*' :
                    case 'guest' :
                        $access = 1;
                        break;
                    case '@' :
                        $access = 1;
                        break;
                    default :
                        if (in_array($role, $user_roles)) {
                            $access = 1;
                        }
                        break;
                }
            }
        }
        return $access;
    }





    public function actioninituserinfo(){
        $arr = '运营	大区经理	贺明星|刘杰|薛雷
运营	分公司财务	陈宏阳|陈朋飞|侯茵茵|胡哲|霍倩|姜晓妃|蒋瑞娟|李倩倩|李嵩|李征|廖厚红|林伟凤|刘长粉|孙丹婷|孙锦琳|唐俊杰|吴健|吴晓玲|吴晓泉|谢国强|徐娜|易亚燕|张洁|张思燕|张魏魏
运营	分公司经理	陈海俊|崔崇|付德森|郭军|何剑松|何向宇|洪英利|黄志达|蒋雪松|李博|李磊|李友文|林徐茂|刘凯|刘磊|麦惠东|米宏|宁凯|覃兴振|王海霖|王鹏|杨明智|杨小冬|于洋01|张浩彬|赵昱龙|周志伟|朱天亮|朱天亮咸阳
运营	分公司司管	曾国平|陈宏阳|陈露|陈朋飞|陈玉芬|方森旭|房定轩|高云飞|何天旻|侯茵茵|胡哲|黄慧明|江平|姜晓妃|蒋瑞娟|井娅倩|李玲玉|李敏|李倩倩|李嵩|李弦|李颖杰|李征|廖厚红|林伟凤|刘长粉|吕刚|孙朝|孙丹婷|孙建霞|孙锦琳|唐俊杰|王斌|魏宏涛|吴健|吴晓玲|吴晓泉|肖文波|谢国强|徐广杨|徐娜|易亚燕|于丙肖|张洁|张思燕|张魏魏|张新旺|张雅倩|赵强
运营	分公司支持	耿伟|韩铁锁|汪默|王有吉|闻晓强|张福静|
运营	分公司主管	蔡潮鑫|陈开忠|陈礼波|陈泽鹏|崔永钦|高剑栋|何如军|黄文钊|冀党锋|荆国杰|柯文辉|孔越群|李勇|李宇|廉杰|林治岩|林智源|刘林|刘伟祥|刘小卫|刘智刚|马佩翔|邱明明|饶庆安|任帅|沈春旺|王俊钢|王亮亮|吴涛|项付根|肖安平|辛克玉|徐鹏|薛文华|闫成光|杨猛|杨笑|殷俊|袁海东|张凯|张强|张燕京|赵晨|赵文波|周聪磊|周昆|周伟|朱铭良|朱燕斌
运营	物料采购	孙冉冉|张玉婷
运营	员工培训	许兴银
运营	运营部经理	杨祎琦
运营	运营部助理	李思思
市场	BD合作	曹丽娜
市场	VIP管理	金竹|孟欣
市场	大区负责人	李凯|李新宇|薛伟|张凯|张文月|赵文哲
市场	内部审核	李凯|李新宇|薛伟|张凯|张谦|张文月|赵文哲
市场	市场部外派	安洁|曹昌|曹磊|曹志强|陈伟|陈晓龙|杜毅|胡勇|金虎|刘浏|吕霖|孙宝利|孙浩溟|唐龙|向巍|杨东飞|杨政胤|张志刚|赵正冉|朱含啸|朱玉金
市场	市场部总监	柳柳
市场	市场运营	曹丽娜|金竹
市场	物料采购	王琳|杨冉|张园园|赵兴
市场	优惠券申请	曹丽娜|李凯|李新宇|薛伟|张凯|张谦|张文月|赵文哲
市场	优惠券审核	金竹
财务	财务部经理	曾艳
财务	财务部总监	孙英珍|张子超
财务	出纳	高华
财务	发票	张多多
财务	分公司账务处理	林莉|宋英芳|杨成燕
财务	核算	陆金霜
财务	信息费结算	丁润华
客服	处理组主管	刘忱	品质监控组
客服	订单组兼职	敖曼|巴兴|白静|车云鹏|陈冰|陈超|陈强1|陈子阳|崔川|崔志|丁丁|丁国栋|丁娇|杜江红2|方纪平1|冯淑雯|冯淑宇|高金凤|高志鹏|葛士国1|管峰|管晓楠|韩杰|韩哲1|郝伟伟|黄士涛|霍军伟|贾志彬|江丽丽1|雷秋蝶|李春红|李昊|李继阳|李丽丽|李梦琦|李群星|李颖|梁健|刘树存|刘匣|刘晓亮1|刘亚京|刘义超|卢春艳|卢卫平|卢一新|马海超|马莹|苗润营|明亚婷|邱敏|任晶晶|司林林1|孙慧娟|孙延召|唐敏|田金龙|田璐|王波|王静|王文玲|王羽|王治钧|王子鹏|吴雪|肖秋妹|熊睿|许彦飞|杨凤娟|杨俊平|杨茂|杨铭|杨升|杨雪03|虞凡|袁名珠|张桂旗|张李|张萌|张萌萌|张琪|张芮|张瑞姣|张文雷|张欣亚|张耀玥|赵芳芳|赵洪福|赵金娟|郑莎莎|钟亚磊1	订单组
客服	订单组培训	马艳杰|石建威	订单组
客服	订单组全职	敖强|陈龙|陈恬|丁双|杜文肖|高国伟|黄卫杰|蒋玉坤|金瑞成|刘丹丹|刘京春|牛犇|彭艳|乔丽|沈振东|石雪松|史贵省|苏若雷|王成龙|王丹1|王光宇|王婧|王媛媛|邢玥|杨鹤|杨正媛|姚劭峰|叶涛|袁峥|张红艳|张明	订单组
客服	订单组质检	李佳|张莹01	订单组
客服	订单组主管	勾哲|李邦木	订单组
客服	订单组组长	李天宇|马立元|张宸啟|张厚禄|张利敏	订单组
客服	服务组（助理）	黄涛1	服务组
客服	服务组主管	颜小琦	服务组
客服	服务组组员	白鑫晶|常国坡|成吉宏|高前康|郭祎璠|李萌|刘佳良|木申申|任爽|王倩|王秀丽|杨羡|张金娜	服务组
客服	服务组组长	杨雪2|张艳敏	服务组
客服	交通事故处理专员	李萌01|李琼	品质监控组
客服	交通事故助理	李亭	品质监控组
客服	客服中心专员	邢晔
客服	客服中心总监	赵新磊
客服	质量监控	邱晨
公关	媒体	张东鹏
公关	品牌公关部经理	李小光
公关	设计	龙涛
公关	微博	李帆
公关	用户运营	李林蔚|周达洋
技术	H5	刘宇
技术	KK拼车	焦龙|李金山|孙彦哲|王洪亮|苑明|张同凯|朱瑞
技术	UI	黄姗|刘天然
技术	测试	陈鑫|李盟|马学云|杨名利
技术	产品	龚晓萍|刘震林|乔博|石达|武显赫|袁荣|
技术	后台开发	艾国信|崔路哲|邓小明|董坤|冯广祥|技术李伟|李定才|刘团望|丘建平|王健|王子超|肖波|杨程介|于杨
技术	数据中心	高峰|李迿
技术	司机端开发	陈阳|李永峰
技术	用户端开发	郭平|申广亮|宋增宾
管委会	CEO	黄宾|杨家军
管委会	COO	孙景川
管委会	董事会	牛立雄
行政	行政组	蔡雪梅|杨婷婷|邓安妮|李伟|牟凯|冯樵|王亮';
//        $arr = '客服	服务组（助理）	黄涛1	服务组
//客服	订单组全职	敖强|陈龙|陈恬|丁双|杜文肖|高国伟|黄卫杰|蒋玉坤|金瑞成|刘丹丹|刘京春|牛犇|彭艳|乔丽|沈振东|石雪松|史贵省|苏若雷|王成龙|王丹1|王光宇|王婧|王媛媛|邢玥|杨鹤|杨正媛|姚劭峰|叶涛|袁峥|张红艳|张明	订单组';
        $line_arr = explode("\n",$arr);
        foreach($line_arr as $line){
            $data = explode("	",$line);
            $dep_name = $data[0];
            $role_name = $data[1];
            $user = $data[2];
            $group_name = isset($data[3]) ? $data[3] : '';
            $group_info = '';
            if($group_name){
                $group_info = AdminDepartment::model()->find('name like :name',array(':name'=>$group_name));
                if(!$group_info){echo 'empty group info:'.$group_name;die;}
            }
            $dep_info = AdminDepartment::model()->find('name like :name',array(':name'=>$dep_name));
//            print_r($dep_info);
//            echo 'group_name:';
//            print_r($group_name);echo ';-----';
            if($group_name){
                $role_info = AdminRole::model()->find('name like :name and department_id = :dep_id',array(':name'=>$role_name,':dep_id'=>$group_info->id));
            }else{
                $role_info = AdminRole::model()->find('name like :name and department_id = :dep_id',array(':name'=>$role_name,':dep_id'=>$dep_info->id));
            }
            if(!$role_info) {echo 'empty role name:'.$role_name;die;}

            //$role_info = AdminRole::model()->find('name like :name and department_id = :dep_id',array(':name'=>$role_name,':dep_id'=>$dep_info->id));
//            echo $role_name.'----';print_r($role_info);echo $role_name;
            $old_user_actions = $bingji_tmp = array();
            //if($role_info->department_id == $dep_info->id){
            $users_arr = explode('|',$user);
            $users_arr = array_filter($users_arr);
//                print_r($users_arr);
            foreach($users_arr as $user_name){
                $old_user_actions = array();
                $user_name = trim($user_name);
                $old_user_info = AdminUser::model()->find('name = :name',array(':name'=>trim($user_name)));
                if(!$old_user_info) {
                    echo $user_name.'&&----';
                    continue;
                }
                $user_info_new = AdminUserNew::model()->find('name = :name',array(':name'=>$user_name));
                if($user_info_new){
                    //修改用户的部门和小组信息
                    $contri = ( $group_name ) ? array('department_id' => $dep_info->id,
                        'group_id'=>$group_info->id) : array('department_id' => $dep_info->id);
//                    if($user_name == '丁娇'){
//                        print_r( $contri);//die;
//                    }
                    $re = AdminUserNew::model()->updateByPk($user_info_new->id,$contri);
//                    if($user_name == '丁娇'){
//                        var_dump($re);die;
//                    }
                    //print_r($contri);

                    //更新用户角色组信息
                    //
                    //$checkUser2RoleExist = AdminUser2role::model()->find('');
                    $userrole_mod = new AdminUser2role();
                    $array = array('user_id'=>$user_info_new->id,'role_id'=>$role_info->id);
                    $userrole_mod->attributes = $array;
                    $userrole_mod->save();
                }
                else {
                    echo 'no user '.$user_name;
                }
            }
        }
    }


    public function actiongetOldAuth(){
        $user_info = AdminUser::model()->findAll('permissions = 1 and status = 1 ');
        foreach($user_info as $obj){
            $role_array_tmp= explode(',',$obj->roles);
            sort($role_array_tmp);
            $old_user_group_info = AdminGroup::model()->getGroupModsList($role_array_tmp);
            //echo 'user_name old_group——info'."\n";
            //print_r($old_user_group_info);
            //print_r($old_user_group_info);
            if($old_user_group_info){
                $action_arr = array();
                foreach($old_user_group_info as $group){
                    if(isset($group['mods']) && $group['mods']){
                        foreach($group['mods'] as $action_detail){
                            //if($action_detail['id'] == 554) {echo 'ok';die;}
                            $action_arr[$action_detail['id']] = $action_detail['id'];
                        }
                    }
                }

                if(!empty($action_arr) && count($action_arr) > 0) {
                    $action_str = implode('|',$action_arr);
                    echo $obj->name.','.$action_str."\n";
                }


                //echo '('. count($action_arr);echo ')'."\n";
            }
            else {
                echo $obj->name .'have no group_info'."\n";
                continue;
            }
        }
    }

    public function actionSyncViewBonus(){
        $sql = "SELECT user_id FROM db_car.`t_admin_user` WHERE `roles` LIKE '%,46,%'";
        $ids = Yii::app()->db->createcommand($sql)->queryAll();
        //print_r($ids);die;
        if($ids){
            foreach($ids as $id){
                $user_special = AdminSpecialAuth::model()->find('user_id = :user_id',array(':user_id' => $id['user_id']));
                if($user_special){
                    $user_special->bonus = 1;
                    $user_special->save();
                    echo 'update->'.$id['user_id']."\n";
                }
                else {
                    $use_apecial_mod = new AdminSpecialAuth();
                    $array = array(
                        'user_id'=>$id['user_id'],
                        'bonus'=>1,
                        'create_time'=>date('Y-m-d H:i:s'),
                    );
                    $use_apecial_mod->attributes = $array;
                    $use_apecial_mod->save();
                    echo 'insert      ->'.$id['user_id']."\n";
                }
            }
        }
    }

    public function actionSyncDriverPhone(){
        $sql = "SELECT user_id FROM db_car.`t_admin_user` WHERE `roles` LIKE '%,166,%'";
        $ids = Yii::app()->db->createcommand($sql)->queryAll();

        if($ids){
            foreach($ids as $id){
                $user_special = AdminSpecialAuth::model()->find('user_id = :user_id',array(':user_id' => $id['user_id']));
                if($user_special){
                    $user_special->driver_phone = 1;
                    $user_special->save();
                    echo 'update->'.$id['user_id']."\n";
                }
                else {
                    $use_apecial_mod = new AdminSpecialAuth();
                    $array = array(
                        'user_id'=>$id['user_id'],
                        'driver_phone'=>1,
                        'create_time'=>date('Y-m-d H:i:s'),
                    );
                    $use_apecial_mod->attributes = $array;
                    $use_apecial_mod->save();
                    echo 'insert      ->'.$id['user_id']."\n";
                }
            }
        }
    }


    public function actionSyncViewUserPhone(){
        $sql = "SELECT user_id FROM db_car.`t_admin_user` WHERE `roles` LIKE '%,10,%'";
        $ids = Yii::app()->db->createcommand($sql)->queryAll();
        //print_r($ids);die;
        if($ids){
            foreach($ids as $id){
                $user_special = AdminSpecialAuth::model()->find('user_id = :user_id',array(':user_id' => $id['user_id']));
                if($user_special){
                    $user_special->user_phone = 1;
                    $user_special->save();
                    echo 'update->'.$id['user_id']."\n";
                }
                else {
                    $use_apecial_mod = new AdminSpecialAuth();
                    $array = array(
                        'user_id'=>$id['user_id'],
                        'user_phone'=>1,
                        'create_time'=>date('Y-m-d H:i:s'),
                    );
                    $use_apecial_mod->attributes = $array;
                    $use_apecial_mod->save();
                    echo 'insert      ->'.$id['user_id']."\n";

                }

            }
        }
    }

    public function actionCopyDep(){
        $array = array(
            '司机管理部'    =>array('dep_id'=>1,'group_id'=>0),
            '呼叫中心'      =>array('dep_id'=>4,'group_id'=>9),
            '市场部'       =>array('dep_id'=>2,'group_id'=>0),
            '数据中心'      =>array('dep_id'=>5,'group_id'=>0),
            '品质监控'      =>array('dep_id'=>4,'group_id'=>10),
            '财务部'       =>array('dep_id'=>3,'group_id'=>0),
            '人力资源'     =>array('dep_id'=>8,'group_id'=>0),
            '董事会'       => array('dep_id'=>7,'group_id'=>0),
            );
        $old_mod = AdminUser::model();
        $old_user  = $old_mod->findAll('permissions = 1 and status = 1');
        if($old_user){
            foreach($old_user as $old_obj){

                $new_user_info = AdminUserNew::model()->find('name = :name',array(':name'=>$old_obj->name));

                if(($new_user_info !== null) && $new_user_info->department_id == 0
                    && isset($array[$old_obj->department])){
                    $dep_name = trim($old_obj->department);
                    $new_user_info->department_id = $array[$dep_name]['dep_id'];
                    $new_user_info->group_id = $array[$dep_name]['group_id'];
                    //$new_user_info->parent_id = $array[$old_obj->department]['group_id'];
                    $new_user_info->save();
                }
            }
        }
    }


    public function actioncheckUserLoginLog($user_id){
        $nowkey = $user_id.'now_ssid';
        $oldkey = $user_id.'old_ssid';
        $now_key = Yii::app()->cache->get($nowkey);
        $old_key = Yii::app()->cache->get($oldkey);
        echo 'old_key:'.$old_key.'----now key :'.$now_key;
    }

    public function actiongetYiiCache($key,$decode = false){
        $v = Yii::app()->cache->get($key);
        if($decode){
            print_r(json_decode($v,1)); // json_decode($v,1);
        }
        else echo $v;
    }

    public function actiontest(){
        $action_id = 4;
        $info = AdminRole2action::model()->getAllDepRoleByAction($action_id);
        print_r($info);
    }

}