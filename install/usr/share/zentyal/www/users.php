<?php
if($_GET['uid'] != "") {
	$uid = $_GET['uid'];
	$seconds = $_GET['stay']*$_GET['unit'];
	// echo "Creating ".$uid;
	system("adduser ".$uid." Room ".$uid." '' ".$seconds." | tee -a /tmp/useradd.log");
	system("listusers >/tmp/curusers.txt");
}
?>
<html><head><title>Activate User Internet</title>
<?php
	if($_GET['uid'] != "") {
		echo '<meta http-equiv="refresh" content="10;url=/" />';
	}
?>
</head><body style="background:#fed;"><h1>Activate User Internet</h1>
<form>
	Room #
	<input name="uid" style="width:50px;"; />
	<input name="stay" style="width:40px;"; value="1"/>
	<select name="unit">
		<option value="3600">Hour(s)</option>
		<option value="86400">Day(s)</option>
		<option value="604800">Week(s)</option>
	</select>
	<input name="submit" type="submit" value="Submit" />
</form>
<iframe src="listusers.php" style="width:90%; height:70%;" frameBorder="0">
</iframe>
<br />
<a href="logout.html">Logout</a>
</body></html>
