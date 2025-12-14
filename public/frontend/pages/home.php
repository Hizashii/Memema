<?php
$scriptPath = $_SERVER['SCRIPT_NAME'];
$basePath = dirname(dirname(dirname($scriptPath)));
$redirectUrl = $basePath . '/index.php?route=/';
header('Location: ' . $redirectUrl, true, 301);
exit;

