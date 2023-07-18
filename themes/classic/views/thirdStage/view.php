<h1>商家登录信息 #<?php echo $model->id; ?></h1>

<?php

$this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
        'channel',
        'initPassword',
    ),
)); ?>