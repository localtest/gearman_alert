<?php

date_default_timezone_set('Etc/UTC');

define('CURRENT_DIR', dirname(__FILE__).'/');
include(CURRENT_DIR.'../base.php');

/*
 * 发送告警邮件
 *
 * */
class mailer_worker {

	const LOG_PATH = LOG_PATH;
	const DEBUG = false;

	const MAILER_HOST = MAILER_HOST;
	const MAILER_PORT = MAILER_PORT;
	const MAILER_USER = MAILER_USER;
	const MAILER_USER_NAME = MAILER_USER_NAME;
	const MAILER_PASSWD = MAILER_PASSWD;
	private $mail_group = array();
	private $base_mailer;

	public function __construct() {
		$this->base_mailer_init();
	}

    /*
     * Do write log
     *
     * */
	private function do_log($log_type, $log_line='') {
		switch ($log_type) {
			case 'mailer':
				$currDay = date('Y-m-d', time());
				$log_file = self::LOG_PATH."mailer_{$currDay}.log";

				$time = date('Y-m-d H:i:s', time());
				$log = "[$time][$log_line]\n";
				file_put_contents($log_file, $log, FILE_APPEND);
				break;
		}
		return;
	}

	private function base_mailer_init() {
		$this->base_mailer = new PHPMailer;
		$this->base_mailer->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$this->base_mailer->SMTPDebug = 0;
		$this->base_mailer->Debugoutput = 'html';

		$this->base_mailer->Host = self::MAILER_HOST;
		$this->base_mailer->Port = self::MAILER_PORT;

		$this->base_mailer->SMTPAuth = true;
		$this->base_mailer->Username = self::MAILER_USER;
		$this->base_mailer->Password = self::MAILER_PASSWD;
		$this->base_mailer->setFrom(self::MAILER_USER, self::MAILER_USER_NAME);

		include(CURRENT_DIR.'../config/config_group.php');
		$this->mail_group = $_MAIL_GROUP;
		return;
	}

	private function set_group($send_group='all') {
		foreach ($this->mail_group as $group => $members) {
			if ($group==$send_group) {
				foreach ($members as $member) {
					$this->base_mailer->addAddress($member['email'], $member['name']);
				}
			}
		}
		return;
	}

	private function send_mail($data, $template='template_1.html') {
		$this->base_mailer->Subject = '负载告警-'.$data['machine'];
		$this->base_mailer->msgHTML(file_get_contents(CURRENT_DIR.$template));
		$this->base_mailer->Body = str_replace('[machine]', $data['machine'], $this->base_mailer->Body);
		$this->base_mailer->Body = str_replace('[threshold]', $data['threshold'], $this->base_mailer->Body);
		$this->base_mailer->Body = str_replace('[job_detail]', $data['job_detail'], $this->base_mailer->Body);
		return $this->base_mailer->send();
	}

	public function run($job, &$log) {
		//Eg: all|127.0.0.1:4730-200-test_job/205/200
		$log = $load = $job->workload();
		$load = explode('|', $load);

		//Todo: 存在test_job Array, 则给test_job组发送邮件, 否则直接发给all
		$this->set_group($load[0]);

		$tmp_data = explode('-', $load[1]);
		$data = array(
			'machine' => $tmp_data[0],
			'threshold' => $tmp_data[1],
			'job_detail' => $tmp_data[2],
		);
		if ($this->send_mail($data)) {
			$send_result =  $data['job_detail'].' Message sent';
		} else {
			$send_result =  $data['job_detail'].' Mailer Error: '.$this->base_mailer->ErrorInfo;
		}
		$this->do_log('mailer', $send_result);
		return 'OK';
	}
}
