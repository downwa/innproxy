<?php if(isset($_GET['act'])) { system("sudo /home/administrator/scripts/activate ".$_GET['uid']." ".$_GET['stay']); } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="refresh" content="99910;url=listinactive.php<?php if(isset($_GET['uid'])) { echo "?uid=".$_GET['uid']."&stay=".$_GET['stay']; } ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<link rel="stylesheet" href="css/print.css" type="text/css" />
	<title>Available Access Codes</title>
</head>
<body>
<div class="base title">Active Users</div>
<h4><a href="listinactive.php" title="Click to see available codes">Available</a></h4>
<h4><a href="#" onclick="window.print();">Print</a></h4>
<h4><a href="users.php">Details</a></h4>
<h4><a href="logout.html">Logout</a></h4>
<div style="clear:left;" </div>



<h3>NOTE: Click on +/- and the Date at which the code should terminate.</h3>
<h4><a href="listactive.php">Click to see active users</a></h4>
<div class="base title">Available Access Codes</div>
<?php system("sudo /home/administrator/scripts/listinactive-html '".$_GET['uid']."' '".$_GET['stay']."' '^[^d]|^d[^i]|^di[^n]|^din[^e]|^dine[^r]'"); ?>
<br style="clear:left;"/>
<h4 style="float:left;margin-right:4pt;"><a href="#" onclick="window.print();">Print</a></h4>
<h4><a href="logout.html">Logout</a></h4>
<script>
	onLoaded=function() { window.parent.done(document); }
	onLoaded();
</script>
</body>
</html>
