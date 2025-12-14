<?php
$scriptPath = $_SERVER['SCRIPT_NAME'];
$pagesDir = dirname($scriptPath);
$frontendDir = dirname($pagesDir);
$publicDir = dirname($frontendDir);
$redirectUrl = $publicDir . '/index.php?route=/';
header('Location: ' . $redirectUrl, true, 301);
exit;

