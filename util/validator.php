<?php

final class Validator {

    private static $actions = [
        'pattern' => 0,
        'len' => 1,
        'min_len' => 2
    ];

    private function __construct() {
        
    }

    public static function pattern($params) {
        return mb_strlen(mb_ereg_replace($params['pattern'], '', $params['item'])) === 0;
    }

    public static function len($params) {
        return mb_strlen($params['item']) === (int) $params['pattern'];
    }

    public static function min_len($params) {
        return mb_strlen($params['item']) >= (int) $params['pattern'];
    }

    public static function validate($action, $params) {
        if (self::$actions[$action] === self::$actions['pattern']) {
            return self::pattern($params);
        } else if (self::$actions[$action] === self::$actions['len']) {
            return self::len($params);
        } else if (self::$actions[$action] === self::$actions['min_len']) {
            return self::min_len($params);
        } else {
            die("Неизвестный метод валидации");
        }
    }

}
