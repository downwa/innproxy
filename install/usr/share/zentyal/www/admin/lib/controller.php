<?php

  $CLIENTIPS="192.168.42.";
  $USERS="/var/lib/innproxy/users.json";
  $FWDIR="/var/lib/innproxy/firewalls/";

  ini_set('display_errors',1); 
  error_reporting(E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

//require_once "lib/j4p/J4P.php";
//require_once "lib/utils.php";
include(dirname(__FILE__) . "/j4p/J4P.php");
include(dirname(__FILE__) . "/utils.php");
include(dirname(__FILE__) . "/pwgen.class.php");

function now() { // Output the date in m/d/Y H:i:s format
  $date = new DateTime();
  return $date->format('m/d/Y H:i:s');
}

function today() { // Output the date in m/d/Y format
  $date = new DateTime();
  return $date->format('m/d/Y');
}

function fdate($timestamp) {
  return date("m/d/Y",$timestamp);
}

function saveUsers($users) {
	global $USERS;
  if(@file_put_contents($USERS.".tmp", json_encode($users)) === FALSE) {
		J4P::addResponse()->alert('1:Unable to save user.');
  }
  else {
		if(rename($USERS.".tmp",$USERS) === FALSE) {
			J4P::addResponse()->alert('2:Unable to save user.');
		}
	}
}

function j4p_parseForm($input) {
	global $USERS;

  parse_str($input, $formData);
  $uid = $formData['uid'];
  $datetime1 = strtotime(now());
  $datetime2 = strtotime($formData['end']." 11:00 AM");
	if(createUser($uid, $datetime1, $datetime2) === -1) {
    J4P::addResponse()->alert("Invalid user or date.");
    return;
  }
  
  $users = json_decode(file_get_contents($USERS));
  J4P::addResponse()->fillTables('users',$users);
}

/** PURPOSE: Create user active at datetime1 (zero if not active yet), expiring at datetime2 (if active)
    NOTE: Returns -1 if specified user or dates are invalid, 0 on success.
**/
function createUser($uid, $datetime1, $datetime2) {
	global $USERS;
  $secs = $datetime2 - $datetime1;// == <seconds between the two times>
  if($uid == "" || $secs<0) {
    return -1;
  }
  
	require_once(dirname(__FILE__) . "/pwgen.class.php");
  $pwgen = new PWGen();
  $pwgen->setNoVovels(true);
  $password = $pwgen->generate();
  $password = strtolower(substr($password,0,6));

	$fh=fopen($USERS,"r");
	if(!flock($fh, LOCK_EX)) { die("Locking failed."); }
  $users = json_decode(file_get_contents($USERS));
	$users->$uid=new stdClass();
	$users->$uid->user=$uid;
	$users->$uid->pass=$password;
	$users->$uid->active=$datetime1;
	$users->$uid->stay=$secs;
	$users->$uid->leave=fdate($datetime2);
	$users->$uid->macaddr='';
	$users->$uid->ipaddr='';
	$users->$uid->bytes=0;
	$users->$uid->disabled=false;
	saveUsers($users);
	flock($fh, LOCK_UN); fclose($fh);
	return 0;
}

function createMissingDinerUsers($users) {
	global $USERS;
	$anyCreated=false; 
	for($i = 1; $i <= 27; $i++) {
		$user=sprintf("diner%02d",$i);
		if(!isset($users->$user)) {
			if(createUser($user,0,3600) != -1) { $anyCreated=true; }
		}
	}
	if($anyCreated) { $users=json_decode(file_get_contents($USERS)); }
	return $users;
}

function j4p_datasrc($input) {
	global $USERS;
  parse_str($input, $formData);
  $users = createMissingDinerUsers(json_decode(file_get_contents($USERS)));
  //J4P::addResponse()->document->getElementById("output2")->innerHTML = print_r($users, true);
  $firewalls = getFirewalls();
  J4P::addResponse()->fillTables('users',$users);
  J4P::addResponse()->fillTables('firewalls',$firewalls);
  J4P::addResponse()->eval("setTimeout('data()',5000);");
}

function getFirewalls() {
	global $FWDIR;

	$fwalls=new stdClass();
	if (is_dir($FWDIR)){
		if ($dh = opendir($FWDIR)){
			while (($file = readdir($dh)) !== false){
				if($file == "." || $file == "..") { continue; }
				$fwalls->$file=new stdClass();
				$fwalls->$file->macaddr=$file;
				$fwalls->$file->ipaddr=file_get_contents($FWDIR."/".$file);
			}
			closedir($dh);
		}
	}
	return $fwalls;
}

function j4p_able($input) {
	global $USERS;
  parse_str($input, $formData);
  $uid = $formData['user'];  
  if($uid == "") {
    J4P::addResponse()->alert("Invalid user or date.");
    return;
  }
  
	$fh=fopen($USERS,"r");
	if(!flock($fh, LOCK_EX)) { die("Locking failed."); }
  $users = json_decode(file_get_contents($USERS));
  $users->$uid->disabled=!$users->$uid->disabled;
	saveUsers($users);
	flock($fh, LOCK_UN); fclose($fh);
	
  J4P::addResponse()->fillTables('users',$users);
}
?>