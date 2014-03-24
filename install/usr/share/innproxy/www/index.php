<?php
  global $a,$b,$private_id,$site_name,$ipaddr,$redirect;

  $site_name="Bristol Inn";
  $CLIENTIPS="192.168.42.";

  ini_set('display_errors',1); 
  error_reporting(E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

  include "sessions.php";

  loadSession();
  header("Connection: close");

  /** HANDLE INPUTS **/
  $redirect=input("redirect");
  $user=input("user");
  $pass=input("pass");
  $submit=input("submit");
  
  $ipaddr=$CLIENTIPS.(($_SERVER['REMOTE_PORT']-1024)%256); // Calculate IP address of client

  $authenticated = 0;
  $reason="";
  if($submit != "") {
    $users = json_decode(file_get_contents("/var/lib/innproxy/users.json"));
    $user = preg_replace('/[^\p{L}\p{N}\s]/u', '', $user); // Replace symbols
    $pass = preg_replace('/[^\p{L}\p{N}\s]/u', '', $pass); // Replace symbols
    if($users->$user->pass == $pass) {
			if($users->$user->disabled == true) {
				$reason="This account is disabled.";
				file_put_contents("/tmp/auth-".$ipaddr,0);
			}
			else { $authenticated=1; }
		}
    else {
			$reason="Invalid username or password.";
			file_put_contents("/tmp/auth-".$ipaddr,0);
		}
  }
  if($authenticated != 1) {
    include "login.php";
    //echo "<!--"; print_r($_SERVER); echo "-->";
  } else {
    if(file_put_contents("/tmp/auth-".$ipaddr,date_timestamp_get(date_create())." ".escapeshellarg($user)) === FALSE) {
			echo "Server authentication error.";
    }
    else {
			include "redirect.php";
			//sleep(7); header('Location: '.$redirect);
		}
  }

  saveSession();
  
?> 
