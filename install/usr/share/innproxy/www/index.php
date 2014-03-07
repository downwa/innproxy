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

  $authenticated = false;
  $reason="";
  if($submit != "") {
    $users = json_decode(file_get_contents("/var/lib/innproxy/users.json"));
    if($users->$user->pass == $pass) { $authenticated=1; }
    else { $reason="Invalid username or password."; }
    //$authenticated=shell_exec("sudo /home/administrator/innproxy/scripts/auth ".escapeshellarg($user)." ".escapeshellarg($pass)." 2>&1");
    //if($authenticated != 1) { $reason="Login failed (".$authenticated.")"; }
  }
  if($authenticated != 1) {
    include "login.php";
  } else {
    include "redirect.php";
    exec("ssh pi@innportal sudo /usr/bin/allowip $ipaddr"); // Enable address in firewall
  }

  saveSession();
  
?> 
