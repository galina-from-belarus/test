<?php

final class Crypto {

    public static function salt() {
        return bin2hex(random_bytes(22));
    }
    
    public static function crypt($salt, $password) {
        return sha1($salt . $password);
    }
}