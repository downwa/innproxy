<?php

header("Connection: close");

$CLIENTIPS="192.168.42.";

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$isSecure = false;
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
    $isSecure = true;
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
//if($isSecure) { header("Location: https://".$_SERVER['SERVER_ADDR']."/login.php".$path); exit; }

echo "It works: path=".$_GET['redirect'];

// Calculate IP address of client
$ipaddr=$CLIENTIPS.(($_SERVER['REMOTE_PORT']-1024)%256);
echo "ip=".$ipaddr;
echo "<pre>";
print_r($_SERVER);
echo "</pre>";







    // Get the private context
    session_name('Private');
    session_start();
    $private_id = session_id();
    $b = $_SESSION['pr_key'];
    session_write_close();
   
    // Get the global context
    session_name('Global');
    session_id('TEST');
    session_start();
   
    $a = $_SESSION['key'];
    session_write_close();

    // Work & modify the global & private context (be ware of changing the global context!)
 ?>
<html>
    <body>
        <h1>Test 2: Global Count is: <?=++$a?></h1>
        <h1>Test 2: Your Count is: <?=++$b?></h1>
        <h1>Private ID is <?=$private_id?></h1>
        <h1>Gloabl ID is <?=session_id()?></h1>
        <pre>
        <?php print_r($_SESSION); ?>
        </pre>
    </body>
 </html>
 <?php
    // Store it back
    session_name('Private');
    session_id($private_id);
    session_start();
    $_SESSION['pr_key'] = $b;
    session_write_close();

    session_name('Global');
    session_id('TEST');
    session_start();
    $_SESSION['key']=$a;
    session_write_close();
?>
