<?php

$user=$_GET['user'];
echo "<h1>USAGE for $user</h1><pre>";
system("/usr/local/bin/showusage ".$user);
echo("</pre>");

?>