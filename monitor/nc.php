<?php

	date_default_timezone_set('Etc/UTC');

	define('CURRENT_DIR', dirname(__FILE__).'/');
	include(CURRENT_DIR.'../base.php');

	while (true) {
		foreach ($monitors as $monitor) {
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			socket_connect($socket, $monitor['host'], $monitor['port']);
			$cmd = "status\n;sleep 0.1";
			$send = socket_send($socket, $cmd, strlen($cmd), MSG_OOB);
			$status_items = socket_read($socket, 1000);
			$status_items = explode("\n", $status_items);
			array_pop($status_items);
			array_pop($status_items);
			foreach ($status_items as $item) {
				preg_match_all("/([\w]+)[\s]+([\d]+)[\s]+([\d]+)[\s]+([\d]+)/", $item, $match_item);
				$item = array(
					'job' => $match_item[1][0],
					'queue' => $match_item[2][0],
					'processing' => $match_item[3][0],
					'workers' => $match_item[4][0],
				);
			}

			socket_close($socket);
		}
		sleep(1);
	}
