<?php

define('CURRENT_DIR', dirname(__FILE__).'/');
include(CURRENT_DIR.'../base.php');

class alert {
	const LOG_PATH = LOG_PATH;
	const MAX_RETRY = 50;

	private $_THRESHOD;
	private $gearman;

    public function __construct($threshod) {
		$this->_THRESHOD = $threshod;
        $this->gearman = new GearmanClient();
    }

    /*
     * Do write log
     *
     * */
	private function do_log($log_type, $log_line='') {
		switch ($log_type) {
			case 'alert':
				$currDay = date('Y-m-d', time());
				$log_file = self::LOG_PATH."alert_{$currDay}.log";

				$time = date('Y-m-d H:i:s', time());
				$log = "[$time][$log_line]\n";
				file_put_contents($log_file, $log, FILE_APPEND);
				break;
		}
		return;
	}

    /*
     * Add service worker
     *
     * */
	private function addServer($retry=0) {
		try {
        	$this->gearman->addServer(GEARMAN_HOST, GEARMAN_PORT);
		} catch(GearmanException $e) {
			$retry++;
			if ($retry > self::MAX_RETRY) {
				$log = "Maximum retry times of '".self::MAX_RETRY."' reached, exit the retry process!";
				$this->do_log('alert', $log);
				return false;
			}
			sleep(6);
			$log = "Could not add the service server, retry ".$retry;
			$this->do_log('alert', $log);
			return $this->addServer($retry);
		}
		return true;
	}

    /*
     * submit alert mission
     *
     * */
	private function submit_mission($retry=0) {
		$ping = @$this->gearman->ping('test');
		$addServer = FALSE;
		if ($ping == FALSE) {
			$addServer = $this->addServer();
		}
		if ($ping || (!$ping && $addServer)) {
			$run = $this->gearman->runTasks();
			return $run;
		} else {
			return FALSE;
		}
	}

	private function parse_worker($filename, $date) {
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

	private function parseAndCalculate($file) {
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

	public function run() {
		$addServer = $this->addServer();
		if (!$addServer) {
			$log = "Can't add mailer server, Exit the process!";
			$this->do_log('alert', $log);
			exit();
		}
		while (true) {
			$currDay = date('Y-m-d', time());
			$monitor_log = LOG_PATH.'monitor/';
			foreach (glob($monitor_log."*_{$currDay}.log") as $filename) {
				$worker = $this->parse_worker($filename, $currDay);
				$threshod = $this->_THRESHOD[$worker['host']][$worker['worker']];
				$result = $this->parseAndCalculate($filename);
				//Eg: all|127.0.0.1:4730-20/20/20-3/3/3
				$maillog = $threshod['send_group'].'|'.$worker['host'];
				
				$alert_log = '';
				$alert_log .= (isset($result['1min']) && $result['1min']<$threshod['1min']) ? $result['1min'].'('.$threshod['1min'].')' : 'none';
				$alert_log .= '/';
				$alert_log .= (isset($result['5min']) && $result['5min']<$threshod['5min']) ? $result['5min'].'('.$threshod['5min'].')' : 'none';
				$alert_log .= '/';
				$alert_log .= (isset($result['15min']) && $result['15min']<$threshod['15min']) ? $result['15min'].'('.$threshod['15min'].')' : 'none';
				$threshod_log = $threshod['1min'].'/'.$threshod['5min'].'/'.$threshod['15min'];

				$maillog .= '-'.$threshod_log.'-'.$alert_log;
				if ($threshod_log != 'none/none/none') {
					$this->gearman->addTaskBackground('mailer_worker', $maillog, 'mailer_worker');
					$submit = $this->submit_mission();
					if (!$submit) {
						$log = "Submit error, exit the process!";
						$this->do_log('alert', $log);
						exit();
					} else {
						$log = "Submit job";
						$this->do_log('alert', $log);
					}
				}
			}

			sleep(60);
		}
	}
}

$alert = new alert($_THRESHOD);
$alert->run();
