<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-9-13
 * Time: 上午11:16
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '上线城市管理';
?>
<div class="container">
    <h1>开通城市管理 </h1>
    <div class="row-fluid" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="span2">
            <a href="javascript:void(0)" class="btn btn-success" onclick="addCity()">增加新城市</a>
        </div>
        <div class="span8">
        </div>
        <div class="span2">
            <a href="javascript:void(0)" class="btn btn-success" onclick="delCached()">清除缓存</a>
        </div>
    </div>
    <?php if (is_array($data) && count($data)) { ?>
        <table class="table table-striped table-bordered">
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>前缀</th>
                <th>bonus_code</th>
                <th>操作</th>
            </tr>
            <?php foreach ($data as $v) {?>
                <?php if ($v['id']) { ?>
                <tr>
                    <td><?php echo CHtml::encode($v['id']);?></td>
                    <td><?php echo CHtml::encode($v['name']);?></td>
                    <td><?php echo CHtml::encode($v['prefix']);?></td>
                    <td><?php echo $v['bonus_code'];?></td>
                    <td>
                        <?php if (is_array($v['district']) && count($v['district'])) {?>
                            <div style="display: none" id="district_<?php echo $v['id'];?>" >
                            <ul class="inline">
                                <?php foreach($v['district'] as $d_id=>$d_name) {?>
                                    <li>
                                        <div class="alert alert-success alert-dismissable" id="d_c_<?php echo $d_id;?>">
                                            <button class="close" aria-hidden="true" onclick="delDistrict(<?php echo $d_id;?>)">×</button>
                                            <?php echo $d_name;?>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                            </div>
                        <?php } ?>
                        <ul class="inline">
                            <li><a class="btn btn-info" href="javascript:;" onclick="showDistrict(<?php echo $v['id'];?>, '<?php echo $v['name'];?>')">显示区域</a></li>
                            <li><a class="btn btn-primary" href="javascript:;" onclick="addDistrict(<?php echo $v['id'];?>)">新增区域</a></li>
                        </ul>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
        </table>
    <?php } ?>

    <div class="modal hide fade" id="myModal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="title"></h3>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <a href="javascript:void(0)" style="display: none" class="btn btn-primary" func="submit">保存</a>
            <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        </div>
    </div>

    <div id="add_modal" style="display: none">
        <form id="frm_city">
            <div class="row-fluid">
                <div class="span6">城市名称：<input type="text" name="city_name" /></div>
                <div class="span6">城市前缀：<input type="text" name="city_prefix" /></div>
            </div>
            <div class="row-fluid" id="dis_container">
                <div class="span12">区域</div>
                <input type="hidden" name="district[]" class="span6">
            </div>
            <div class="row-fluid">
                <a href="javascript:void(0)" class="btn" onclick="insertDistrict()">增加区域</a>
            </div>
            <div class="row-fluid">
                <input type="hidden" name="act" value="add_city"/>
            </div>
        </form>
    </div>
</div>

<script>
    function showDistrict(city_id, name) {
        var o = $('#myModal');
        o.find('.title').html(name+'区域');
        //o.find('.modal-body').html('').append(jQuery('#district_'+city_id).show());
        o.find('.modal-body').empty();//add by aiguoxin清空缓存 fix bug
        o.find('.modal-body').html(jQuery('#district_'+city_id).html());
        o.modal('show')
    }

    function delDistrict(id) {
        if (confirm('确认删除该区域')) {
        jQuery.get(
            '<?php echo Yii::app()->createUrl('driverControl/cityAjax');?>',
            {
                act : 'del_district',
                district_id : id
            },
            function (d) {
                if (d.status) {
                    jQuery('#d_c_'+id).hide();
                } else {
                    alert('删除失败');
                }
            },
            'json'
        );
        }
    }

    function addDistrict(city_id) {
        var city_id = city_id;
        var district_name = window.prompt("请在此输入区域名称");
        if (district_name.length<=0) {
            alert('请在此输入区域名称');
            return false;
        }
        jQuery.get(
            '<?php echo Yii::app()->createUrl('driverControl/cityAjax');?>',
            {
                act : 'add_district',
                city_id : city_id,
                district_name : district_name
            },
            function (d) {
                if (d.status) {
                    alert('添加成功');
                    window.location.reload();
                } else {
                    alert('添加失败');
                }
            },
            'json'
        );
    }

    function addCity() {
        var html = jQuery('#add_modal').html();
        var o = $('#myModal');
        o.find('.title').html('新增城市');
        o.find('.modal-body').html(html);
        o.find('[func="submit"]').click(function(){saveCity();});
        o.find('[func="submit"]').show();
        o.modal('show')
    }

    function insertDistrict() {
        var html = "<input type='text' class='span6' name='district[]' /><br>";
        var o = jQuery(html);
        jQuery('#dis_container').append(o);
    }

    function delCached() {
        jQuery.get(
            '<?php echo Yii::app()->createUrl('driverControl/cityAjax');?>',
            {
                'act' : 'del_cached'
            },
            function(d) {
                if (d) {
                    alert('成功');
                    window.location.reload();
                } else {
                    alert('失败');
                }
            } ,
            'json'
        );
    }

    function saveCity() {
        var post_data = jQuery('#frm_city').serialize();
        jQuery.post(
            '<?php echo Yii::app()->createUrl('driverControl/cityAjax');?>',
            post_data,
            function(d) {
                if (d.status) {
                    alert(d.msg);
                    window.location.reload();
                } else {
                    alert(d.msg);
                }
            },
            'json'
        );
    }
</script>