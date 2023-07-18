<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-7-16
 * Time: 下午1:54
 * To change this template use File | Settings | File Templates.
 */
?>
<legend>司机路考信息</legend>
<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTab">
        <li class=""><a href="<?php echo Yii::app()->createUrl('recruitment/roadSetting'); ?>">设置</a></li>
        <li class="active"><a data-toggle="tab"  href="#profile">展现</a></li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div id="home" class="tab-pane fade"></div>
        <div id="profile" class="tab-pane active">
            <form action="<?php echo Yii::app()->request->url ;?>" method="POST" class="form-inline">
                <div class="row-fluid">
                    <div class="span4">
                        <p>司机路考时间</p>
                        <?php
                        $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                            'attribute'=>'visit_time',
                            'language'=>'zh_cn',
                            'name'=>"exam_date_start",
                            'options'=>array(
                                'showAnim'=>'fold',
                                'showOn'=>'both',
                                //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                                'buttonImageOnly'=>true,
                                //'minDate'=>'new Date()',
                                'dateFormat'=>'yy-mm-dd',
                                'changeYear'=>true,
                                'changeMonth'=> true,
                            ),
                            'htmlOptions'=>array(
                                'style'=>'width:100px',
                            ),
                        ));
                        ?>
                        -
                        <?php
                        $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                            'attribute'=>'visit_time',
                            'language'=>'zh_cn',
                            'name'=>"exam_date_end",
                            'options'=>array(
                                'showAnim'=>'fold',
                                'showOn'=>'both',
                                //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                                'buttonImageOnly'=>true,
                                //'minDate'=>'new Date()',
                                'dateFormat'=>'yy-mm-dd',
                                'changeYear'=>true,
                                'changeMonth'=> true,
                            ),
                            'htmlOptions'=>array(
                                'style'=>'width:100px',
                            ),
                        ));
                        ?>
                    </div>
                    <div class="span2" >
                        <p>司机工号</p>
                        <input  type="text" name="driver_id" />
                    </div>
                </div>
                <p></p>
                <div class="row-fluid">
                    <div class="span2">
                        <input type="submit" name="submit" class="btn btn-success" value="查询"/>
                    </div>
                </div>
            </form>
            <hr>
            <table class="table table-condensed">
                <tr>
                    <th>路考日期</th>
                    <th>工号</th>
                    <th>姓名</th>
                    <th>自动档路考考官</th>
                    <th>手动档路考考官</th>
                </tr>
                <?php
                if (is_array($data) && count($data)) {
                    foreach ($data as $v) {
                        ?>
                        <tr>
                            <td><?php echo $v['exam_date'];?></td>
                            <td><?php echo $v['driver_id'];?></td>
                            <td><?php echo $v['name'];?></td>
                            <td><?php echo $v['a_examiner']; ?></td>
                            <td><?php echo $v['m_examiner'];?></td>
                        </tr>
                    <?php
                    }
                }
                ?>
            </table>

            <div class="pagination text-center">
                <?php
                $this->widget('CLinkPager',array(
                        'header'=>'',
                        'firstPageLabel' => '首页',
                        'lastPageLabel' => '末页',
                        'prevPageLabel' => '上一页',
                        'nextPageLabel' => '下一页',
                        'pages' => $pages,
                        'maxButtonCount'=>13
                    )
                );
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    window.onload = function() {
        jQuery('.ui-datepicker-trigger').remove();
    }
</script>

