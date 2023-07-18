<?php
$this->pageTitle = 'e代驾';
?>
<div class="container">
    <?php if(!empty($data)){?>
    <p style="text-align: center;"><?php echo $data->title;?></p>
    <p>
        <?php echo $data->content;?>
    </p>
    <?php }?>
</div>