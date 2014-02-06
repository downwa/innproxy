<?php if($_GET['deluid'] != "") { system("/home/administrator/scripts/deluser ".$_GET['deluid']); system("/home/administrator/scripts/listusers >/tmp/curusers.txt"); } ?>
<html>
<head>
	<meta http-equiv="refresh" content="10;url=listusers.php" />
</head>
<body style="background:#fed;">
<h2>Current Users</h2>
<!-- a href="listusers.php" style="font-size:8pt">Refresh</a -->
<table border="0" style="width:90%">
<tr><td style="width:50%">
<?php
	system("/home/administrator/scripts/userlist | grep -v 'deluid=diner[0-9]'");
?>
</td><td style="vertical-align:top; background:#fed;">
<?php if($_GET['uid'] != "") echo '<span style="font-size:9pt">User Tag:</span>'; ?>
<h2 style="background:white">
	<?php
		if($_GET['uid'] != "") system("sudo /home/administrator/scripts/listuser ".$_GET['uid']);
	?>
</h2>
</td></tr>
</table>
</body></html>
