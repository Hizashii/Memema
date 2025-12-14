<?php
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = dirname($scriptDir);
$redirectUrl = $basePath . '/index.php';
if (isset($_GET['route'])) {
    $redirectUrl .= '?route=' . urlencode($_GET['route']);
} elseif (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
    $redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
}
header('Location: ' . $redirectUrl, true, 301);
exit;

