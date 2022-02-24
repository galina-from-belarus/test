<?php
session_start();

include_once '../app/model/auth.php';
include_once '../util/autoloader.php';
spl_autoload_register('Autoloader::load');

$auth_control = new AuthController;
if ($auth_control->checkAuth()) {
    include '../app/view/header.php';
    ?>
    <div class="greeting">
            <?php include '../app/view/greeting.php'; ?>

        <p><a href="./index.php?action=logout"><button >LOGOUT</button></a></p>
        <p><a href="./index.php">Index page</a></p>
    </div>
    <?php
    include '../app/view/footer.php';
}