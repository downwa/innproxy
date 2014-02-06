<?php
	$acted=false;
	if(isset($_GET['act'])) { system("sudo /home/administrator/scripts/activate ".$_GET['uid']." ".$_GET['stay']); $acted=true; }
	if($_GET['uid'] != "" && $_GET['staydays'] != "") {
        	$uid = $_GET['uid'];
        	$seconds = $_GET['staydays']*86400;
        	// echo "Creating ".$uid;
        	system("sudo /home/administrator/scripts/adduser ".$uid." Room ".$uid." '' ".$seconds." | tee -a /tmp/useradd.log");
        	system("sudo /home/administrator/scripts/listusers >/tmp/curusers.txt");
		$acted=true;
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="refresh" content="<?php if(!$acted) { echo 360; } ?>1;url=list<?php if(!$acted) { echo "in"; } ?>active.php<?php if(isset($_GET['uid'])) { echo "?uid=".$_GET['uid']."&stay=".$_GET['stay']; } ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<link rel="stylesheet" href="css/print.css" type="text/css" />
	<title>Available Access Codes</title>
</head>
<body>
<div class="base title">Available Access Codes</div>
<h4><a href="listactive.php" title="Click to see active users">Active</a></h4>
<h4><a href="#" onclick="window.print();">Print</a></h4>
<h4><a href="logout.html">Logout</a></h4>
<div class="main">

<h3>NOTE: Click on +/- and the Date at which the code should terminate.</h3>

<div class="base lft box rowhead" style="text-decoration:underline" >
  <div class="join col">User</div>
  <div class="join col">Password</div>
  <div class="join col buttons">Set Expiry</div>
</div>
<br />

<?php system("sudo /home/administrator/scripts/listinactive-html '".$_GET['uid']."' '".$_GET['stay']."' '^[^d]|^d[^i]|^di[^n]|^din[^e]|^dine[^r]' 'list' 1"); ?>

<br />
<form>
        New User #
        <input name="uid" style="width:50px;"; />
        <input name="staydays" style="width:40px;"; value="1"/>
	Days
        <input name="submit" type="submit" value="Submit" />
</form>

</div>
<br /><hr />
</body>
</html>
