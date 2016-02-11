<?php

	require('./config.php');

	foreach ($monitors as $monitor) {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_connect($socket, $monitor['host'], $monitor['port']);
		$cmd = "status\n;sleep 0.1";
		$send = socket_send($socket, $cmd, strlen($cmd), MSG_OOB);
		echo $send."\n";
		$reply = socket_read($socket, 1000);
		echo $reply;
		socket_close($socket);
	}
