<?php $this->pageTitle = '批量VIP充值'; ?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h2>批量VIP充值</h2>

            <a  class="btn search-button" href="<?php echo Yii::app()->createUrl('vip/downloadTemplet');?>">下载批量VIP充值模板</a>&nbsp;
            <div class="search-form">
                <div class="well span12" style="border:0px">

                    <form class="form-inline" id="yw0" action="<?php echo Yii::app()->createUrl('vip/rechargeBatch');?>" method="post" enctype="multipart/form-data">
                        <div class="controls controls-row">
                            <div class="span3">
                                <label for="vip_rechargeBatch">根据模板修改的txt 文件<br><span style="color:#ff3c38;font-size:20px;">注意, txt中的姓名和电话号码之间只能用半角逗号</span></label>
                                <input  maxlength="255" class="span12" name="vip" id="vip_rechargeBatch" type="file">
                            </div>
                            <div class="span3">
                                <label >&nbsp; </label>
                                <input class="btn span8" type="submit" name="yt0" value="确认充值">
                            </div>
                            <div class="span3">
                                 <span style="color:#ff3c38;font-size:20px;">充值的时候程序运行时间比较长,充值按钮点击一次即可</span>
                            </div>
                        </div>

                    </form>
                </div>
                <!-- search-form --></div>
            <!-- search-form -->
            </div>
        <div>
            <?php echo $msg;?>
        </div>
    </div>
</div>