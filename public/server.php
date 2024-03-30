<?php   // server.php

//var_dump($_SERVER);
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$from_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

var_dump($user_agent, $from_ip);

