<?php
if($_GET['uid'] != "") {
	$uid = $_GET['uid'];
	// echo "Creating ".$uid;
	system("sudo /home/administrator/scripts/adddiner ".$uid." 2>&1 | tee -a /tmp/useradd.log");
	system("sudo /home/administrator/scripts/listusers >/tmp/curusers.txt");
}
?>
<html><head><title>Activate Diner Internet</title>
	<link rel="stylesheet" href="css/diner.css" />
<?php
	if($_GET['uid'] != "") {
		echo '<meta http-equiv="refresh" content="10;url=/diner.php" />';
	}
?>
</head><body style="background:#fed;"><h1>Activate Diner Internet</h1>
<form action="diner.php">
	Diner #
	<input name="uid" style="width:50px;"; />
	<input name="submit" type="submit" value="Submit" />
</form>
<iframe src="listdiner.php" style="width:90%; height:70%;" frameBorder="0">
</iframe>
<br />
<a href="dineractive.php">Active Users</a>
<a href="logout.html">Logout</a>
</body></html>
