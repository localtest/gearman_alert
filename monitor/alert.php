<?php

	define('CURRENT_DIR', dirname(__FILE__).'/');
	include(CURRENT_DIR.'../base.php');

	function parse_worker($filename, $date) {
		$basename = basename($filename);
		$basename = explode('_', $basename);
		array_pop($basename);
		$worker = array(
			'host' => $basename[0],
		);
		array_shift($basename);	
		$worker['worker'] = implode('_', $basename);
		return $worker;
	}

	function parseAndCalculate($file) {
		$log = file_get_contents($file);
		$log = trim($log);
		$log = explode("\n", $log);
		$buffer = array(
			'1min' => array(),
			'5min' => array(),
			'15min' => array(),
		);
		$now = time();
		$last_one_min = $now - 60;
		$last_five_min = $now - 5*60;
		$last_fifth_min = $now - 15*60;
		foreach ($log as $k=>$logline) {
			$logline = explode('|', $logline);
			$logline[0] = strtotime($logline[0]);
			if ($logline[0]>=$last_one_min) {
				$buffer['1min'][] = intval($logline[1]);
			}
			if ($logline[0]>=$last_five_min) {
				$buffer['5min'][] = intval($logline[1]);
			}
			if ($logline[0]>=$last_fifth_min) {
				$buffer['15min'][] = intval($logline[1]);
			}
		}
		$cal_result = array();
		if (count($buffer['1min'])>0) {
			$cal_result['1min'] = array_sum($buffer['1min'])/count($buffer['1min']);
		}
		if (count($buffer['5min'])>0) {
			$cal_result['5min'] = array_sum($buffer['5min'])/count($buffer['5min']);
		}
		if (count($buffer['15min'])>0) {
			$cal_result['15min'] = array_sum($buffer['15min'])/count($buffer['15min']);
		}
		return $cal_result;
	}

	while (true) {
		$currDay = date('Y-m-d', time());
		$monitor_log = LOG_PATH.'monitor/';
		foreach (glob($monitor_log."*_{$currDay}.log") as $filename) {
			$worker = parse_worker($filename, $currDay);
			$threshod = $_THRESHOD[$worker['host']][$worker['worker']];
			$result = parseAndCalculate($filename);
			//Eg: all|127.0.0.1:4730-123/205/200
			$maillog = $threshod['send_group'].'|'.$worker['host'];
			
			$threshod_log = '';
			$threshod_log .= (isset($result['1min']) && $result['1min']<$threshod) ? $result['1min'] : 'none';
			$threshod_log .= '/';
			$threshod_log .= (isset($result['5min']) && $result['5min']<$threshod) ? $result['5min'] : 'none';
			$threshod_log .= '/';
			$threshod_log .= (isset($result['15min']) && $result['15min']<$threshod) ? $result['15min'] : 'none';
			$maillog .= '-'.$threshod_log;
			if ($threshod_log != 'none/none/none') {
				//send mail
			}
		}

		sleep(60);
	}
