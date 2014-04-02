<?php

$user=$_GET['user'];
echo("<html><head><title>$user usage</title></head><body>");
echo "<h1>$user usage</h1><pre>";
echo("<table border=1>");
echo("<tr><th>Time</th><th>Bytes</th><th>User</th><th>MAC Addr</th><th>Host Name</th></tr>");

	$row=0;
	if (($handle = popen("/usr/local/bin/showusage ".$user, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				echo("<tr>");
        $num = count($data);
        $row++;
        for ($c=0; $c < $num; $c++) {
            echo "<td align=right>&nbsp;".$data[$c]."</td>";
        }
				echo("</tr>");
    }
    pclose($handle);
	}
echo("</table></body></html>");

?>