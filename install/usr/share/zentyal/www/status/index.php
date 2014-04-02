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
  setGlobalIni();
  $overuse=$GLOBALS['innproxy_OVERUSE'];
  $mblimit=floor($overuse/1000000);
  

  $redirect=input("redirect");
  $session=input("session");
  $count=input("count");
  $logout=input("logout");
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
		$fh=fopen($USERS,"r");
		if(!flock($fh, LOCK_EX)) { die("Locking failed."); }
	  $users = json_decode(file_get_contents($USERS));
	  if(isset($users->$user)) {
			if($logout=="") {
				$userinfo=$users->$user;
				//echo json_encode($userinfo);
				if($userinfo->ipaddr == $ipaddr || $userinfo->ipaddr == "") {
					$active=$userinfo->active;
					$stay=$userinfo->stay;
					$leave=$userinfo->leave;
					$disabled=$userinfo->disabled?"Yes":"No";
					$bytes=$userinfo->bytes;
					$pct=round($bytes*100/$overuse);
					$mbytes=round($bytes/1000000);
					
					$datetime1 = strtotime(now());
					$dt2=strtotime($leave." 11:00 AM");
					$datetime2 = $active+$stay; 
					if($dt2 < $datetime2) { $leave=""; }
					else { $leave=$leave." at 11 am "; }
					$secsleft = ($datetime2 - $datetime1);
					if($secsleft > 0) {
						$hh = floor($secsleft / 3600);
						$ss = floor($secsleft % 3600);
						$mm = floor($ss / 60);
						$ss = floor($ss % 60);
						$hhmmss = sprintf("%02d:%02d:%02d",$hh,$mm,$ss);
					}
					else { $hhmmss="00:00:00"; }
					//$hrsleft = round(($datetime2 - $datetime1) / 3600, 3);
				}
				$loggedin=($userinfo->ipaddr == "")?"No":"Yes";
				if($loggedin == "Yes") { touch($SESSIONS."/sess-".$session); } // Keep this session alive, if it is logged in
			}
			else { // logout is non-blank
				unlink($SESSIONS."/sess-".$session);
				$users->$user->ipaddr = $users->$user->macaddr = "";
				if(@file_put_contents($USERS.".tmp", json_encode($users)) === FALSE) {
					J4P::addResponse()->alert('1:Unable to save user.');
				}
				else {
					if(rename($USERS.".tmp",$USERS) === FALSE) {
						J4P::addResponse()->alert('2:Unable to save user.');
					}
				}
				sleep(3);
				header("Location: https://reserve.bristolinn.com:447/?redirect=http://google.com");
				return;
			}
		}
		flock($fh, LOCK_UN); fclose($fh);
	}
	else { $user="Authenticating..."; $hhmmss="unknown time"; }
	
?> 
<html>
  <head><title>Session Status</title>
    <meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
		<!-- meta http-equiv="refresh" content="30; url=/status/index.php?session=<?=$session?>&count=<?=$count?>&redirect=<?=$redirect?>" / -->
		<LINK rel="stylesheet" type="text/css" href="styles/style.css" />
		<script type="text/javascript">
			var logout='<?=$logout?>';
			var user='<?=$user?>';
			var count='<?=$count?>';
			// Blank/Close at logout, or after two minutes of no login
			if(logout == 'true' || (count > 4 && user == "Authenticating...")) {
				document.location='about:blank'; window.close();
			}
		</script>		
  </head> 
  <body>
    <div class="base title">Session Status</div>
    
		<div style="color:blue;font-size:9pt;">
                        NOTE: This status window must remain open
                        to validate access.  Usage is measured from
                        checkout time (11 am).
    </div>
<?php if($redirect != "") { ?>    
    <a href="<?=$redirect?>" target="_blank" title="<?=$redirect?>" target="_top">Visit original site</a>
    <br />
<?php } ?>    
    
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
          <b>&nbsp;<?=$leave?><span style="font-size:9pt;">(<?=$hhmmss?> left)</span></b> 
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
          <b>Today's usage<b>
        </div>
        <div class="meter">
					<span style="width: <?=$pct?>%;white-space: nowrap;overflow:visible;" title="<?=$mbytes?> Mb">&nbsp;<?=$mbytes?> of <?=$mblimit?> Mb</span>
        </div>
      </div>
      
    </div>
    <br />

		<div clas="base box">
			<div class="join">
<?php if($loggedin == "Yes") { ?>    
				<a href="/status/index.php?logout=true&session=<?=$session?>&count=<?=$count?>&redirect=<?=$redirect?>" target="_top" title="Logout session">&nbsp;Logout&nbsp;</a>
				<br />
<?php } ?>    
			</div>
		</div>
	</body>
</html>