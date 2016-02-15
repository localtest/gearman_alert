<?php
	define('CURRENT_DIR', dirname(__FILE__).'/');
	include(CURRENT_DIR.'../base.php');

	$currDay = date('Y-m-d', time());
	$date = isset($_GET['date']) && !empty($_GET['date']) ? $_GET['date'] : $currDay;

	//10.10.68.117:4730_mailer_worker
	$job_str = isset($_GET['job_str']) && !empty($_GET['job_str']) ? $_GET['job_str'] : '';
	$log_file = LOG_PATH.'monitor/'.$job_str.'_'.$date.'.log';

	$callback = $_GET['callback'];
	if (!file_exists($log_file)) {
		$data = $callback.'([]);';
		header("Content-Type: text/javascript");
		echo $data;
		exit();
	}

	$timeseries = file_get_contents($log_file);
	$timeseries = trim($timeseries);
	$timeseries = explode("\n", $timeseries);
	$data = $callback.'([';
	foreach ($timeseries as $item) {
		$item = explode('|', $item);
		$item[0] = strtotime($item[0]);
		$Y = date('Y', $item[0]);
		$n = date('n', $item[0]);
		$j = date('j', $item[0]);
		$G = date('G', $item[0]);
		$i = date('i', $item[0]);
		$s = date('s', $item[0]);
		$data .= '[Date.UTC('.$Y.','.$n.','.$j.','.$G.','.$i.','.$s.'),'.$item[1].'],';
	}
	$data = trim($data, ',');
	$data .= ']);';
	header("Content-Type: text/javascript");
	echo $data;
