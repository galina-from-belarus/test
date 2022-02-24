<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();

include_once '../app/model/auth.php';
include_once '../util/autoloader.php';
spl_autoload_register('Autoloader::load');

$auth_control = new AuthController;
if ($auth_control->checkAuth()) {
    include '../app/view/header.php';
    include '../app/view/index.php';
    include '../app/view/footer.php';
}