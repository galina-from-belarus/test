<?php

class Datson {

    private static $_instance;
    private static $data_path;
    private static $actions = [
        'insert' => 1,
        'update' => 2,
        'delete' => 0
    ];

    protected function __construct($data_path) {
        self::$data_path = $data_path;
    }

    protected function __clone() {
        
    }

    public function __wakeup() {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance($data_path): Datson {
        $cls = static::class;
        if (!isset(self::$_instance[$cls])) {
            self::$_instance[$cls] = new static($data_path);
        }

        return self::$_instance[$cls];
    }

    private static function route($name) {
        return self::$data_path . $name . '.json';
    }

    public function create($table_name) {
        $filename = self::route($table_name);
        if (file_exists($filename)) {
            die("Такая таблица уже существует");
        } else {
            $table = fopen($filename, "w");
            fwrite($table, json_encode(array()));
            fclose($table);

            if (!file_exists($filename)) {
                die("Не удалось создать таблицу");
            }
        }
        return TRUE;
    }

    public function drop($table_name) {
        return unlink(self::route($table_name));
    }

    private static function getIdByConditions($records, $conditions) {
        foreach ($records as $key => $value) {
            if (array_intersect_assoc($conditions, $value)) {
                return $key;
            }
        }
        return NULL;
    }

    private static function getRecordByConditions($records, $conditions) {
        foreach ($records as $key => $value) {
            if (array_intersect_assoc($conditions, $value)) {
                                return [$key => $value];
            }
        }
        return NULL;
    }
    
        private static function getDataByConditions($records, $conditions) {
        foreach ($records as $value) {
            if (array_intersect_assoc($conditions, $value)) {
                return $value;
            }
        }
    }
    
    private static function getNextKey($record) {
        $keys = array_keys($record);
        sort($keys, SORT_NUMERIC);
        $id = array_pop($keys) + 1;
        return $id;
    }

    private static function modifyData($file_data, $action, $data, $conditions) {
        // обрабатываем действия
        $records = $file_data;

        if ($action === self::$actions['insert']) {
            $id = self::getNextKey($file_data);
            $records[$id] = $data;
        } else if ($action === self::$actions['update']) {
            $target_id = self::getIdByConditions($records, $conditions);
            if (is_null($target_id)) {
                die("Такой записи в базе нет");
            }
            $records[$target_id] = $data;
        } else if ($action === self::$actions['delete']) {
            $target_id = self::getIdByConditions($records, $conditions);
            if (is_null($target_id)) {
                die("Такой записи в базе нет, возможно, она была удалена ранее");
            }
            unset($records[$target_id]);
        } else {
            die("Недопустимое действие");
        }
        return $records;
    }

    private static function modifyTableFile($file, $data) {
        flock($file, LOCK_EX); //блокировка файла
        ftruncate($file, 0); //УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА (нам не нужны хвосты!)
        fputs($file, json_encode($data, JSON_UNESCAPED_UNICODE)); //кодируем в json и записываем в файл
        fflush($file); //очищение файлового буфера и запись в файл
        flock($file, LOCK_UN); //снятие блокировки
        return;
    }

    private static function parseTableData($filename) {
        $file_data = file_get_contents($filename); //чтение файла
        return mb_strlen($file_data) ? json_decode($file_data, TRUE) : array(); //кодируем базу в массив либо создаем новую
    }

    private static function changeTable($table_name, $action, $data, $conditions) {
        $filename = self::route($table_name);
        $file = fopen($filename, "r+"); //открытие
        $records_old = self::parseTableData($filename, TRUE);
        $records = self::modifyData($records_old, $action, $data, $conditions);
        
        self::modifyTableFile($file, $records);
        fclose($file);

        return;
    }

    public function insert($table_name, $data) {
        self::changeTable($table_name, self::$actions['insert'], $data, NULL);
        return TRUE;
    }

    public function update($table_name, $data, $conditions) {
        self::changeTable($table_name, self::$actions['update'], $data, $conditions);
        return TRUE;
    }

    public function delete($table_name, $conditions) {
        self::changeTable($table_name, self::$actions['delete'], NULL, $conditions);
        return TRUE;
    }

    public function get($table_name, $conditions) {
        $filename = self::route($table_name);
        $file = fopen($filename, "r+"); //открытие
        $data = self::parseTableData($filename, TRUE);
        fclose($file);
        
        return self::getDataByConditions($data, $conditions);
    }

}
