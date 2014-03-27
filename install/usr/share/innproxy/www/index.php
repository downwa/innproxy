<?php
  global $a,$b,$private_id,$site_name,$ipaddr,$redirect,$usersjson;

  $site_name="Bristol Inn";
  $CLIENTIPS="192.168.42.";
  $SESSIONS="/var/lib/innproxy/sessions";

  ini_set('display_errors',1); 
  error_reporting(E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

  include "sessions.php";

  loadSession();
  setGlobalIni();
  header("Connection: close");

  /** HANDLE INPUTS **/
  $redirect=input("redirect");
  $user=input("user");
  $pass=input("pass");
  $submit=input("submit");
  $doauth=input("doauth");
  
  $ipaddr=$CLIENTIPS.(($_SERVER['REMOTE_PORT']-1024)%256); // Calculate IP address of client

  $authenticated = 0;
  $reason="";
  $overuse=$GLOBALS['innproxy_OVERUSE'];
  $mblimit=floor($overuse/1000000);
  if($submit != "") {
    $users = json_decode(file_get_contents("/var/lib/innproxy/users.json"));
    $user = preg_replace('/[^\p{L}\p{N}\s]/u', '', $user); // Replace symbols
    $pass = preg_replace('/[^\p{L}\p{N}\s]/u', '', $pass); // Replace symbols
    if(isset($users->$user) && $users->$user->pass == $pass) {
			$bytes=$users->$user->bytes;
			$pct=round($bytes*100/$overuse);
			if($users->$user->disabled == true) {
				$reason="This account is disabled.";
				file_put_contents("/tmp/auth-".$ipaddr,0);
			}
			else if($pct > 100) {
				$reason="Daily usage exceeds limit.<br />Wait until 11 am to try again.";
				file_put_contents("/tmp/auth-".$ipaddr,0);
			}
			else {
				$authenticated=1;
				file_put_contents($SESSIONS."/sess-".$private_id,$user);
			}
		}
    else {
			$reason="Invalid username or password.";
			file_put_contents("/tmp/auth-".$ipaddr,0);
		}
  }
  if($doauth == 1) {
		echo $authenticated;
		return;
  }
  if($authenticated != 1) {
		$usersjson=file_get_contents("/var/lib/innproxy/users.json");
    include "login.php";
    //echo "<!--"; print_r($_SERVER); echo "-->";
  } else {
    if(file_put_contents("/tmp/auth-".$ipaddr,date_timestamp_get(date_create())." ".escapeshellarg($user)) === FALSE) {
			echo "Server authentication error.";
    }
    else {
			header("Location: http://192.168.42.1:8080/status/?session=$private_id&count=0&redirect=$redirect");
			//header("Location: http://192.168.42.1:8080/status/?session=redirect=".$redirect);
			//header("Location: http://192.168.42.1:8080/status/redirect.php?redirect=".$redirect);
			//include "redirect.php";
		}
  }

  saveSession();
//echo $private_id; 
//	echo hash('sha256', 'g8xxlx');
?>