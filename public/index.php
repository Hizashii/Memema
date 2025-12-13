<?php
/**
 * Front Controller
 */

if (isset($_GET['route'])) {
    $_SERVER['REQUEST_URI'] = $_GET['route'];
    if (!isset($_SERVER['REQUEST_METHOD'])) {
        $_SERVER['REQUEST_METHOD'] = $_POST ? 'POST' : 'GET';
    }
}

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');

$corePath = APP_PATH . DIRECTORY_SEPARATOR . 'Core';
require_once $corePath . DIRECTORY_SEPARATOR . 'Database.php';
require_once $corePath . DIRECTORY_SEPARATOR . 'Router.php';
require_once $corePath . DIRECTORY_SEPARATOR . 'Controller.php';
require_once $corePath . DIRECTORY_SEPARATOR . 'View.php';
require_once $corePath . DIRECTORY_SEPARATOR . 'Csrf.php';
require_once $corePath . DIRECTORY_SEPARATOR . 'helpers.php';
require_once $corePath . DIRECTORY_SEPARATOR . 'App.php';

$repoPath = APP_PATH . DIRECTORY_SEPARATOR . 'Infrastructure' . DIRECTORY_SEPARATOR . 'Repositories';
require_once $repoPath . DIRECTORY_SEPARATOR . 'MovieRepository.php';
require_once $repoPath . DIRECTORY_SEPARATOR . 'NewsRepository.php';
require_once $repoPath . DIRECTORY_SEPARATOR . 'VenueRepository.php';
require_once $repoPath . DIRECTORY_SEPARATOR . 'BookingRepository.php';
require_once $repoPath . DIRECTORY_SEPARATOR . 'ContactInfoRepository.php';
require_once $repoPath . DIRECTORY_SEPARATOR . 'ContactMessageRepository.php';
require_once $repoPath . DIRECTORY_SEPARATOR . 'ShowRepository.php';
require_once $repoPath . DIRECTORY_SEPARATOR . 'CompanySettingsRepository.php';

$servicesPath = APP_PATH . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Services';
require_once $servicesPath . DIRECTORY_SEPARATOR . 'AuthService.php';
require_once $servicesPath . DIRECTORY_SEPARATOR . 'BookingService.php';
require_once $servicesPath . DIRECTORY_SEPARATOR . 'InvoiceService.php';
require_once $servicesPath . DIRECTORY_SEPARATOR . 'MailService.php';
require_once $servicesPath . DIRECTORY_SEPARATOR . 'ImageService.php';

$app = App::getInstance()->boot();
$app->loadRoutes(ROOT_PATH . '/routes/web.php');
$app->loadRoutes(ROOT_PATH . '/routes/admin.php');
$app->run();

