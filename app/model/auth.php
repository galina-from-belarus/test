<?php

include_once 'user.php';
include_once '../db/datson.php';

class Auth {

    private static $_cookie_lifetime = 2592000;
    private $login;
    private $password;
    private $name;
    private $key;
    
    protected static $error_messages = [
        'login' => ['Неверный логин'],
        'password' => ['Неверный пароль']
    ];
    protected $errors;

    public function __construct(array $data) {
        $this->login = trim($data['login']);
        $this->password = trim($data['password']);
    }

    private static function db() {
        return Datson::getInstance('../data/');
    }

    protected static function table_name() {
        return "session";
    }

    public function errors() {
        return $this->errors;
    }

    public function validate() {
        $data = [
            'login' => $this->login,
            'password' => $this->password
        ];
       
        $candidate = User::getByLogin($this->login);
        
        if (!$candidate) {
            $this->errors['login'] = [
                [self::$error_messages['login']]
            ];
            return FALSE;
        } else {
            if (!$candidate->auth($data)) {
                $this->errors['password'] = [
                    [self::$error_messages['password']]
                ];
                return FALSE;
            }
        }
        $this->name = $candidate->name();
        return TRUE;
    }

    private function sessionUnique() {
        return is_null(self::db()->get(self::table_name(), ['login' => $this->login]));
    }

    private static function getKey($login) {
        $data = self::db()->get(self::table_name(), ['login' => $login]);
        if ($data) {
            return $data['key'];
        }
        return;
    }

    private static function validateKey($login, $key) {
        return strcmp($key, self::getKey($login)) === 0;
    }

    private static function isOnline() {
        return isset($_SESSION['login'])
        && isset($_SESSION['key'])
        && isset($_SESSION['name'])
        && self::validateKey($_SESSION['login'], $_SESSION['key']);
    }

    private static function isLogged() {
        return isset($_COOKIE['login'])
        && isset($_COOKIE['key'])
        && self::validateKey($_COOKIE['login'], $_COOKIE['key']);
    }
    
    private static function generateKey() {
        return Crypto::crypt(Crypto::salt(), Crypto::salt());
    }
    
    private function updateDataTable() {
        $key = self::generateKey();
        if ($this->sessionUnique()) {
                self::db()->insert(self::table_name(), [
                    'login' => $this->login,
                    'key' => $key
                ]);
            } else {
                self::db()->update(self::table_name(), [
                    'login' => $this->login,
                    'key' => $key
                        ], [
                    'login' => $this->login
                ]);
            }
            return $key;
    }

    private function createSession() {
        $_SESSION['login'] = $this->login;
        $_SESSION['key'] = $this->key;
        $_SESSION['name'] = $this->name;
        return;
    }
    
    private static function updateCookies($login, $key) {
        setcookie('login', $login, self::$_cookie_lifetime);
        setcookie('key', $key, self::$_cookie_lifetime);
        return;
    }

    public static function online() {
        if (self::isOnline()) {
            self::updateCookies($_SESSION['login'], $_SESSION['key']);
            return TRUE;
        }
        else if(self::isLogged()){
            self::createSession($_COOKIE['login'], $_COOKIE['key']);
            return TRUE;
        }
        return FALSE;
    }
    
    public function login() {
        if ($this->validate()) {
            $key = $this->updateDataTable();
            if ($key) {
                $this->key = $key;
                $this->createSession();
                return TRUE;
            };
            return FALSE;
        }
        return FALSE;
    }
    
    public static function logOut($login) {
        $_SESSION = [];
        session_destroy();
        setcookie("login", '', time()-3600);
        setcookie("key", '', time()-3600);
        setcookie("name", '', time()-3600);
        self::db()->delete(self::table_name(), ['login' => $login]);
        header("Location: index.php");
    }
}
