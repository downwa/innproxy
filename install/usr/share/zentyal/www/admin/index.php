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
    <?php J4P::outputJs(0); ?>
  </head>
  <body onload="data()">
    <form name="myForm" id="myForm">
      <span>User</span><input name="uid" value="" />&nbsp;
      <span>Leaving</span>&nbsp;<input id="datepicker" name="end" value="<?=now()?>" type="text" /><img src="lib/img/cal.gif" id="cal"/>
      <input type="button" value="Create" onclick="post('parseForm', 'myForm');" />
    </form>
    
    <div class="base title">WiFi Access Codes</div>
    <!-- h4><a href="listinactive.php" title="Click to see available codes">Available</a></h4 -->
    <h4><a href="#" onclick="window.print();">Print</a></h4>
    <h4><a href="logout.html">Logout</a></h4>
    <!-- h4><a href="listactive.php?fix=1" title="Click to repair WiFI when login prompt appears after having logged in.">Repair</a></h4 -->
    <div class="main">
      <!-- h3>NOTE: Click on +/- and the Date at which the code should terminate.</h3 -->

      <div class="base lft box rowhead" style="text-decoration:underline" >
        <div class="join col">User</div>
        <div class="join col">Password</div>
        <div class="join col buttons">Expiry/Today's Usage</div>
      </div>
      <br />

      <div class="base box" data-iglooware-printclasses="lft,mid,rht" data-iglooware-datasrc="users">
        <div class="join">
          <span class="print">&nbsp;&nbsp;User: </span><b>$user</b>
        </div>
        <div class="join">
          <span class="print">Pass: </span><b>$pass</b>
        </div>
        <div class="join buttons">
          <span class="small">$leave  <!-- $curUsage --></span>
        </div>
      </div>
    </div>
    <br />
    <hr />
  </body>
</html>
