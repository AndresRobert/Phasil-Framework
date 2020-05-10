<?php

namespace Core;

session_save_path("core/tmp");
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

abstract class Session {

    /**
     * @param string $name
     *
     * @return mixed
     */
    final public static function Read (string $name) {
        return $_SESSION[APPNAME][$name]??null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    final public static function Create (string $name, $value): void {
        if (isset($value)) {
            $_SESSION[APPNAME][$name] = $value;
        }
        else {
            self::Delete($name);
        }
    }

    /**
     * @param string $name
     */
    final public static function Delete (string $name): void {
        unset($_SESSION[APPNAME][$name]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    final public static function Exists (string $name): bool {
        return isset($_SESSION[APPNAME][$name]);
    }

}
