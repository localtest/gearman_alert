<?php

	define('CURRENT_DIR', dirname(__FILE__).'/');
	include(CURRENT_DIR.'../base.php');

	//host:port
	$job_str = isset($_GET['job_str']) && !empty($_GET['job_str']) ? $_GET['job_str'] : '';


	function get_workername($job_str, $filename) {
		$filename = explode($job_str, $filename);
		$filename = trim($filename[1], '_');
		$filename = explode('_', $filename);
		array_pop($filename);
		$worker = implode('_', $filename);
		return $worker;
	}

	$monitor_log = LOG_PATH.'monitor/';
	$t_list = array();
	foreach (glob($monitor_log.$job_str."_*.log") as $filename) {
		$worker = get_workername($job_str, $filename);
		$t_list[] = array(
			'name' => $worker,
			//http://WEB_DOMAIN/job_detail.php?job_str=host:port_worker
			'link' => WEB_DOMAIN.'job_detail.php?job_str='.$job_str.'_'.$worker,
		);
	}

	$title = $job_str.' - 实例';
	$show_return = true;
	include(CURRENT_DIR.'template/list.php');
