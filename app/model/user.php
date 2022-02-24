<?php

include_once '../db/datson.php';

class User {

    protected $login = NULL;
    protected $salt = NULL;
    protected $password = NULL;
    protected $email = NULL;
    protected $name = NULL;
    protected static $rules = [
        'login' => ['min_len' => 6],
        'password' => ['pattern' => '([а-яА-ЯёЁa-zA-Z]|[0-9])+([а-яА-ЯёЁa-zA-Z]+[0-9]+)|[0-9]+[а-яА-ЯёЁa-zA-Z]+([а-яА-ЯёЁa-zA-Z]|[0-9])+', 'min_len' => 6],
        'email' => ['min_len' => 1, 'pattern' => '^([A-Za-z0-9\+_\-]+)(\.[A-Za-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+([a-z]{2,6})$'],
        'name' => ['pattern' => '[A-zА-яЁё]', 'len' => 2]
    ];
    protected static $error_messages = [
        'login' => ['min_len' => 'Логин должен быть длиннее шести знаков'],
        'password' => ['pattern' => 'Пароль должен состоять из букв и цифр', 'min_len' => 'Пароль должен содержать не менее шести знаков'],
        'email' => ['min_len' => 'Введите адрес email', 'pattern' => 'Проверьте правильность email'],
        'name' => ['pattern' => 'Имя может содержать только буквы', 'len' => 'Имя должно содержать ровно две буквы']
    ];
    protected $errors;

    public function __construct(array $data) {
        $this->login = trim($data['login']);
        $this->password = trim($data['password']);
        $this->salt = isset($data['salt']) ? trim($data['salt']) : "";
        $this->email = isset($data['email']) ? trim($data['email']) : "";
        $this->name = isset($data['name']) ? trim($data['name']) : "";
    }
    
    private static function db() {
        return Datson::getInstance('../data/');
    }

    protected static function table_name() {
        return "user";
    }

    public function errors() {
        return $this->errors;
    }

    private function prepareTableData() {
        $this->salt = self::getSalt();
        $this->password = self::passwordCrypt($this->salt, $this->password);

        return array(
            'login' => $this->login,
            'name' => $this->name,
            'email' => $this->email,
            'salt' => $this->salt,
            'password' => $this->password
        );
    }
                
    private static function getSalt() {
        return Crypto::salt();
    }
    
    private static function passwordCrypt($salt, $password) {
        return Crypto::crypt($salt, $password);
    }

    public function auth($data) {
        $password_crypted = self::passwordCrypt($this->salt, $data['password']);
        return strcmp($this->login, $data['login']) === 0 && (strcmp($this->password, $password_crypted) === 0);
    }
    
    public function validate() {
        $errors = array();

        foreach (self::$rules as $rule_field => $rule_pattern) {
            $field_errors = array();
            foreach ($rule_pattern as $key => $value) {
                if (!Validator::validate($key, array('field' => $rule_field, 'item' => $this->$rule_field, 'pattern' => $value))) {
                    array_push($field_errors, self::$error_messages[$rule_field][$key]);
                }
            }
            if (count($field_errors)) {
                $errors[$rule_field] = $field_errors;
            }
        }
        if (count($errors)) {
            $this->errors = $errors;
            return FALSE;
        }
        return TRUE;
    }
    
    public function create() {
        if ($this->validate()) {

            $data = $this->prepareTableData();
            self::db()->insert(self::table_name(), $data);

            return $this->checkUser($this->login, $this->email);
        }
        return FALSE;
    }

    protected static function getByField($field, $value) {
        $data = self::db()->get(self::table_name(), array($field => $value));
        return $data ? new User($data) : NULL;
    }
    
    public function name() {
        return $this->name;
    }

    public static function getByLogin($login) {
        return self::getByField('login', $login);
    }

    static function getByEmail($email) {
        return self::getByField('email', $email);
    }

    static function checkUser($login, $email) {
        return self::getByLogin($login) || self::getByEmail($email);
    }

}
