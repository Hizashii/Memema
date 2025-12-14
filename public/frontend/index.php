<?php
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($scriptName);
$publicDir = dirname($scriptDir);
$redirectUrl = $publicDir . '/index.php';
if (isset($_GET['route']) && !empty($_GET['route'])) {
    $redirectUrl .= '?route=' . urlencode($_GET['route']);
} elseif (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $params);
    if (!empty($params) && !isset($params['route'])) {
        $redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
    }
}
header('Location: ' . $redirectUrl, true, 301);
exit;

