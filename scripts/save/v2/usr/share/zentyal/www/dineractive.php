<?php if(isset($_GET['act'])) { system("sudo activate ".$_GET['uid']." ".$_GET['stay']); } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="refresh" content="10;url=dineractive.php<?php if(isset($_GET['uid'])) { echo "?uid=".$_GET['uid']."&stay=".$_GET['stay']; } ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<link rel="stylesheet" href="css/print.css" type="text/css" />
	<title>Active Access Codes</title>
</head>
<body>
<div class="base title">Active Users</div>
<h4><a href="dinerinactive.php" title="Click to see available codes">Available</a></h4>
<h4><a href="#" onclick="window.print();">Print</a></h4>
<h4><a href="diner.php">Details</a></h4>
<h4><a href="logout.html">Logout</a></h4>
<div class="main">

<!-- h3>NOTE: Click on +/- and the Date at which the code should terminate.</h3 -->

<div class="base lft box rowhead" style="text-decoration:underline" >
  <div class="join col">User</div>
  <div class="join col">Password</div>
  <div class="join col buttons">Expiry/Today's Usage</div>
</div>
<br />

<?php system("sudo listactive-html '".$_GET['uid']."' '".$_GET['stay']."' '^diner' 'diner' 2"); ?>
</div>

<br /><hr />
</body>
</html>