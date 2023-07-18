
<h1>司机被投诉列表</h1>
<div id="accordion2" class="accordion">
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: daiyihui
 * Date: 13-10-29
 * Time: 下午3:20
 * To change this template use File | Settings | File Templates.
 */

$this->widget('zii.widgets.CListView', array(
    'id' => 'count_detail',
    'dataProvider'=>$data,
    'itemView'=>'_count_detail',   // refers to the partial view named '_post'
    'template'=>'{items}',
));
?>
</div>
