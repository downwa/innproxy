<?php require_once "lib/controller.php"; ?><!DOCTYPE html>
<html lang="en">
  <head>
    <title>WiFI Access Codes</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" href="lib/css/style.css" type="text/css" />
    <script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
    <script src="lib/js/utils.js"></script>
    <?php J4P::outputJs(1); ?>
  </head>
  <body onload="data()">
    <form name="userCreateForm" id="userCreateForm" style="display:none">
      <span>User</span>&nbsp;<input name="uid" id="uid" value="" />&nbsp;
      <span>Leaving</span>&nbsp;<input id="datepicker" name="end" value="<?=today()?>" type="text" /><input 
        type="button" value="Create" onclick="post('parseForm', 'userCreateForm');" />
    </form>
    
    <div class="base title">WiFi Access Codes</div>
    <h4><a href="#" onclick="window.print();return false;" class="button">Print</a></h4>
    <h4><a href="logout.html" class="button">Logout</a></h4>
    <div class="main">
      <h3>NOTE: Passwords are no longer case-sensitive.</h3>

      <div class="base lft box rowhead" style="text-decoration:underline" >
        <div class="join col">User</div>
        <div class="join col">Password</div>
        <div class="join col buttons">Expiry</div>
        <div class="joinmeter col">Today's Usage (100 Mb max)</div>
      </div>
      <br />

      <div class="base box" data-iglooware-printclasses="lft,mid,rht" data-iglooware-datasrc="users" style="display:none">
        <div class="join">
          <span class="print">&nbsp;&nbsp;User: </span><a href="#" onclick="run('able',{user:'$user'}); return false;" title="Enable/Disable account" class="button"><b>$user</b></a>
        </div>
        <div class="join">
          <span class="print">Pass: </span><b>$pass</b>
        </div>
        <div class="join buttons">
          <span class="small" title="mac:$macaddr ip:$ipaddr bytes:$bytes">$leave</span>
        </div>
        <div class="meter">
					<span style="width: $pct%;white-space: nowrap;overflow:visible;" title="$mbytes Mb"><a href="usage.php?user=$user" target="_blank" title="Click for usage details">&nbsp;$mbytes Mb</a></span>
        </div>
      </div>

    <br />
    <hr />
     
    <div class="base title noprint">Firewalls</div>
    <br />
      <div class="base lft box rowhead noprint" style="text-decoration:underline" >
        <div class="join col">MAC Addr</div>
        <div class="join col">IP Addr</div>
      </div>
      <!-- br / -->

      <div class="base box noprint" data-iglooware-printclasses="lft,mid,rht" data-iglooware-datasrc="firewalls" style="display:none">
        <div class="join">
          <span class="small">$macaddr</span>
        </div>
        <div class="join iphost">
          <span class="small">$ipaddr</span>
        </div>
      </div>

      
     </div>
  </body>
</html>
