<?php

include_once 'user.php';

class Signup extends User {

    protected $confirm_password;
    protected static $unique = ['login', 'email'];
    protected static $unique_error_messages = [
        'login' => ['Такой логин уже используется'],
        'email' => ['Такой email уже используется']
    ];

    public function __construct(array $data) {
        parent::__construct($data);

        $this->confirm_password = trim($data['confirm_password']);
        self::$rules['confirm_password'] = ['pattern' => $this->password, 'min_len' => 1];
        self::$error_messages['confirm_password'] = ['pattern' => 'Пароль и подтверждение не совпадают', 'min_len' => 'Подтвердите пароль'];
    }

    private function validateUnique() {
        $errors = $this->errors ? $this->errors : array();
        $getter = 'getBy';

        for ($i = 0; $i < count(self::$unique); $i++) {
            $field = self::$unique[$i];
            $method = $getter . ucfirst($field);
            if (method_exists($this, $method)) {
                if (!is_null($this->{$method}($this->$field))) {
                    $errors[$field] = [self::$unique_error_messages[$field]];
                }
            } else {
                die("Недопустимое поле для валидации");
            }
        }
        if (count($errors)) {
            $this->errors = $errors;
            return FALSE;
        }
        return TRUE;
    }

    public function validate() {
        parent::validate();
        $this->validateUnique();

        if ($this->errors && count($this->errors)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function create() {
        if ($this->validate()) {
            return parent::create();
        }
    }

}
