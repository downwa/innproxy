<?php

  $CLIENTIPS="192.168.42.";

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

function j4p_parseForm($input) {

require_once(dirname(__FILE__) . "/pwgen.class.php");

  $users = json_decode(file_get_contents("/var/lib/innproxy/users.json"));

  parse_str($input, $formData);
  
  $datetime1 = strtotime(now());
  $datetime2 = strtotime($formData['end']." 11:00 AM");

  $secs = $datetime2 - $datetime1;// == <seconds between the two times>
  $days = $secs / 86400;  
  
  $pwgen = new PWGen();
  $pwgen->setNoVovels(true);
  $password = $pwgen->generate();
  $password = strtoupper(substr($password,0,6));
  
  $uid = $formData['uid'];
  
  if($uid == "" || $secs<0) {
    J4P::addResponse()->alert("Invalid user or date.");
    return;
  }
  
  $users->$uid=new stdClass();
  $users->$uid->user=$uid;
  $users->$uid->pass=$password;
  $users->$uid->active=$datetime1;
  $users->$uid->stay=$secs;
  $users->$uid->leave=fdate($datetime2);
  $users->$uid->macaddr='';
  $users->$uid->ipaddr='';

  file_put_contents("/var/lib/innproxy/users.json", json_encode($users));
  J4P::addResponse()->fillTables('users',$users);
}

function j4p_datasrc($input) {
  parse_str($input, $formData);
  $users = json_decode(file_get_contents("/var/lib/innproxy/users.json"));
  //J4P::addResponse()->document->getElementById("output2")->innerHTML = print_r($users, true);
  J4P::addResponse()->fillTables('users',$users);
  J4P::addResponse()->eval("setTimeout('data()',5000);");
}

function j4p_logout($input) {
  parse_str($input, $formData);
  global $CLIENTIPS;
  $ipaddr=$CLIENTIPS.(($_SERVER['REMOTE_PORT']-1024)%256); // Calculate IP address of client
  $result=shell_exec("ssh pi@innportal sudo /usr/bin/logoutip $ipaddr \'\' ".escapeshellarg($formData['user'])." 2>&1"); // Disable address in firewall, record user
  if(strpos($result,"does not translate to a MAC address") !== FALSE) {
    J4P::addResponse()->alert("User is not currently logged in.  To block user, Create a new password for the same User. (result=".$result.")");
  }
}
?>