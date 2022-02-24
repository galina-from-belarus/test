<?php

final class Autoloader {

    private static $path = [
        'model' => '../app/model/',
        'controller' => '../app/controller/',
        'view' => '../app/view/',
        'util' => '../util/',
    ];

    private static function getPath($name) {
        foreach (self::$path as $dir) {
            $file = $dir . lcfirst($name) . '.php';
            if (file_exists($file)) {
                return $file;
            }
        }
        die('Ошибка автоматической загрузки класса');
    }
    
    public static function load($name) {
        include_once self::getPath($name);
    }
}
