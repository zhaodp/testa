<?php $user_message['screen_name']?>,您好！ 

<?php if( is_array( $ms['statuses'] ) ): ?>
<?php foreach( $ms['statuses'] as $item ): ?>
<div style="padding:10px;margin:5px;border:1px solid #ccc">
	<?=$item['text'];?>
</div>
<?php endforeach; ?>
<?php endif; ?>