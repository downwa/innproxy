<?php

	$USERS="/var/lib/innproxy/users.json";

	ini_set('display_errors',1); 
	error_reporting(E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

//	$users = json_decode(file_get_contents($USERS));
	$users = json_decode(file_get_contents("-"));

//	$users->$uid=new stdClass();
//	$users->$uid->user=$uid;
//	$users->$uid->pass=$password;
//	$users->$uid->active=$datetime1;
//	$users->$uid->stay=$secs;
//	$users->$uid->leave=fdate($datetime2);
//	$users->$uid->macaddr='';
//	$users->$uid->ipaddr='';
//	$users->$uid->bytes=0;
//	$users->$uid->disabled=false;

	echo json_encode($users);
	echo "\n";

	file_put_contents($USERS.".tmp", json_encode($users));
?>
