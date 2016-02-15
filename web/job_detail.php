<?php

	define('CURRENT_DIR', dirname(__FILE__).'/');
	include(CURRENT_DIR.'../base.php');

	$currDay = date('Y-m-d', time());
	$date = isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : $currDay;

	//10.10.68.117:4730_mailer_worker
	$job_str = isset($_GET['job_str']) && !empty($_GET['job_str']) ? $_GET['job_str'] : '';
	$log_file = LOG_PATH.'monitor/'.$job_str.'_'.$date.'.log';

	include(CURRENT_DIR.'template/detail.php');
