<?php

	function now() { // Output the date in m/d/Y H:i:s format
		$date = new DateTime();
		return $date->format('m/d/Y H:i:s');
	}

  global $a,$b,$private_id,$site_name,$ipaddr,$redirect;

  $site_name="Bristol Inn";
  $CLIENTIPS="192.168.42.";
  $SESSIONS="/var/lib/innproxy/sessions";
  $USERS="/var/lib/innproxy/users.json";

  ini_set('display_errors',1); 
  error_reporting(E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

  include "../../../innproxy/www/sessions.php";

  $session=input("session");
  $count=input("count");
  if($count == "") { $count=0; }
  $count++;
  $ipaddr=$CLIENTIPS.(($_SERVER['REMOTE_PORT']-1024)%256); // Calculate IP address of client
	$user=@file_get_contents($SESSIONS."/sess-".$session);
	$bytes=0;
	$mbytes=0;
	$pct=0;
	$leave="";
	$disabled="";
	$loggedin="";
	$hhmmss="";
	if($user != "") {
	  $users = json_decode(file_get_contents($USERS));
		$userinfo=$users->$user;
		//echo json_encode($userinfo);
		if($userinfo->ipaddr == $ipaddr || $userinfo->ipaddr == "") {
			$leave=$userinfo->leave;
			$disabled=$userinfo->disabled?"Yes":"No";
			$bytes=$userinfo->bytes;
			$pct=round($bytes*100/100000000);
			$mbytes=round($bytes/1000000);
			
			$datetime1 = strtotime(now());
			$datetime2 = strtotime($leave." 11:00 AM");
			$secsleft = ($datetime2 - $datetime1);
			$hh = floor($secsleft / 3600);
			$ss = floor($secsleft % 3600);
			$mm = floor($ss / 60);
			$ss = floor($ss % 60);
			$hhmmss = sprintf("%02d:%02d:%02d",$hh,$mm,$ss);
			//$hrsleft = round(($datetime2 - $datetime1) / 3600, 3);
		}
		$loggedin=($userinfo->ipaddr == "")?"No":"Yes";
		if($loggedin == "Yes") { touch($SESSIONS."/sess-".$session); } // Keep this session alive, if it is logged in
	}
	else { $user="(Not logged in)"; $hhmmss="unknown time"; }
?> 
<html>
  <head><title>Session Status</title>
    <meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
		<meta http-equiv="refresh" content="30; url=/status/?session=<?=$session?>&count=<?=$count?>" />
		<LINK rel="stylesheet" type="text/css" href="styles/style.css" />
		<script type="text/javascript">
			var user='<?=$user?>';
			var count='<?=$count?>';
			if(count > 2 && user == "(Not logged in)") { window.close(); } // Close after one minute of no login
		</script>		
  </head> 
  <body>
    <div class="base title">Session Status</div>
    
		<div style="color:blue;font-size:9pt;">
                        NOTE: This status window must remain open
                        to validate access.  Usage is measured from
                        checkout time (11 am).
                </div>
    
    <div class="main">
      <br />

      <div class="base box">
        <div class="join">
          <b>User</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$user?></b>
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>Disabled?</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$disabled?></b>
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>Logged in?</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$loggedin?></b>
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>Expiry</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$leave?> at 11 am <span style="font-size:9pt;">(<?=$hhmmss?> left)</span></b> 
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>IP Address</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$ipaddr?></b>
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>Today's usage<b><br />
          <span style="font-size:9pt;">(<?=$mbytes?> of 100 Mb max)</span>
        </div>
        <div class="meter">
					<span style="width: <?=$pct?>%" title="<?=$mbytes?> Mb">&nbsp;<?=$mbytes?> Mb</span>
        </div>
      </div>

    </div>
    <br />
    <hr />
	</body>
</html>