<?php

class AuthController {

    public function checkAuth() {
        if (Auth::online()) {
            if (isset($_GET['action']) && $_GET['action'] === 'logout') {
                Auth::logOut($_SESSION['login']);
            }
            return TRUE;
        } else if (AuthController::isXML()) {
            $auth_status = $this->auth();
            echo json_encode($auth_status, JSON_UNESCAPED_UNICODE);
        } else {
            include '../app/view/header.php';
            include '../app/view/login.php';
            include '../app/view/footer.php';
        }
        return FALSE;
    }

    private function auth() {
        if ($this->isXML()) {
            if (isset($_POST['action']) && $_POST['action'] === 'signup') {
                $signup = new Signup($_POST);
                if ($signup->create()) {
                    return $this->login($_POST);
                } else {
                    return $signup->errors();
                }
            } else if (isset($_POST['action']) && $_POST['action'] === 'login') {
                return $this->login($_POST);
            }
            return;
        }
        return;
    }

    private function login($data) {
        $auth = new Auth($data);
        if ($auth->login()) {
            return ['success' => true];
        } else {
            return $auth->errors();
        }
    }

    private static function isXML() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

}
