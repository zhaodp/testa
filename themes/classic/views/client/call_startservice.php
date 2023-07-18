<?php
$params = $_GET;

if (isset($params['actiontype'])) {
	$sign = md5('LogonCorpName'.convert($params['corpname']).'AgentName'.($params['agentname']).'Password'.'123456');
	if (strtoupper($sign)==$params['logoninfo']) {
		echo '登录成功';
	}
}

function convert($content) {
	return iconv('utf-8', 'gb2312', $content);
}