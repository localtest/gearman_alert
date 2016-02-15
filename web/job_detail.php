<?php

	define('CURRENT_DIR', dirname(__FILE__).'/');
	include(CURRENT_DIR.'../base.php');

	$currDay = date('Y-m-d', time());
	$date = isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : $currDay;

	function get_worker($job_str) {
		$job = explode('_', $job_str);
		array_shift($job);
		$worker = implode('_', $job);
		return $worker;
	}

	//host:port_worker
	$job_str = isset($_GET['job_str']) && !empty($_GET['job_str']) ? $_GET['job_str'] : '';
	$log_file = LOG_PATH.'monitor/'.$job_str.'_'.$date.'.log';

	$worker = get_worker($job_str);
	$title = $worker.' - 负载';
	$show_return = true;
	include(CURRENT_DIR.'template/detail.php');
