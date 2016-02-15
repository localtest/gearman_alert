<?php

	define('CURRENT_DIR', dirname(__FILE__).'/');
	include(CURRENT_DIR.'../base.php');

	$t_list = array();
	foreach ($monitors as $monitor_item) {
		$t_list[] = array(
			'name' => $monitor_item['host'].':'.$monitor_item['port'],
			'link' => WEB_DOMAIN.'job_list.php?job_str='.$monitor_item['host'].':'.$monitor_item['port'],
		);
	}
	include(CURRENT_DIR.'template/list.php');
