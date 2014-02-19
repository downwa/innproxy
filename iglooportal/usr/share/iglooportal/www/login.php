<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$isSecure = false;
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
    $isSecure = true;
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
if($isSecure) { header("Location: https://".$_SERVER['SERVER_ADDR']."/login.php".$path); exit; }

echo "<pre>";
print_r($_SERVER);
echo "</pre>";

echo "It works: path=$path";

?>
