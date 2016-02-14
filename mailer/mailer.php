<?php

date_default_timezone_set('Etc/UTC');

define('CURRENT_DIR', dirname(__FILE__).'/');
include(CURRENT_DIR.'../base.php');

/*
 * 发送告警邮件
 *
 * */
class mailer {

	const BASE_NAME = '';
	const LOG_PATH = '';
	const DEBUG = false;

	const MAILER_HOST = MAILER_HOST;
	const MAILER_PORT = MAILER_PORT;
	const MAILER_USER = MAILER_USER;
	const MAILER_USER_NAME = MAILER_USER_NAME;
	const MAILER_PASSWD = MAILER_PASSWD;
	private $mail_group = array();
	private $mailer;

	public function __construct() {
		$this->mailer_init();
	}

    /*
     * Do write log
     *
     * */
	private function do_log($log_type, $log_line='') {
		switch ($log_type) {
			case 'run':
				$currDay = date('Y-m-d', time());
				$log_file = self::LOG_PATH."run_{$currDay}.log";

				$time = date('Y-m-d H:i:s', time());
				$log = "[$time][$log_line]\n";
				file_put_contents($log_file, $log, FILE_APPEND);
				break;
		}
		return;
	}

	private function mailer_init() {
		$this->mailer = new PHPMailer;
		$this->mailer->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$this->mailer->SMTPDebug = 0;
		$this->mailer->Debugoutput = 'html';

		$this->mailer->Host = self::MAILER_HOST;
		$this->mailer->Port = self::MAILER_PORT;

		$this->mailer->SMTPAuth = true;
		$this->mailer->Username = self::MAILER_USER;
		$this->mailer->Password = self::MAILER_PASSWD;
		$this->mailer->setFrom(self::MAILER_USER, self::MAILER_USER_NAME);

		include(CURRENT_DIR.'../config/config_group.php');
		$this->mail_group = $_MAIL_GROUP;
		return;
	}

	private function set_group($send_group='all') {
		foreach ($this->mail_group as $group => $members) {
			if ($group==$send_group) {
				foreach ($members as $member) {
					$this->mailer->addAddress($member['email'], $member['name']);
				}
			}
		}
		return;
	}

	private function send_mail($data, $template='template_1.html') {
		$this->mailer->Subject = '负载告警-'.$data['machine'];
		$this->mailer->msgHTML(file_get_contents($template), dirname(__FILE__));
		$this->mailer->Body = str_replace('[machine]', $data['machine'], $this->mailer->Body);
		$this->mailer->Body = str_replace('[threshold]', $data['threshold'], $this->mailer->Body);
		$this->mailer->Body = str_replace('[job_detail]', $data['job_detail'], $this->mailer->Body);
		return $this->mailer->send();
	}

	public function run($job, &$log) {
		//Eg: all|127.0.0.1:4730-200-test_job/205/200
		$log = $load = $job->workload();
		$load = explode('|', $load);

		$this->set_group($load[0]);
		$tmp_data = explode('-', $load[1]);
		$data = array(
			'machine' => $tmp_data[0],
			'threshold' => $tmp_data[1],
			'job_detail' => $tmp_data[2],
		);
		if ($this->send_mail($data)) {
			$send_result =  'Message sent';
		} else {
			$send_result =  'Mailer Error: '.$this->mailer->ErrorInfo;
		}
		echo $send_result."\n";
		return 'OK';
	}
}
