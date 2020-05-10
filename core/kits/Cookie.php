<?php

namespace Core;

abstract class Cookie {

    /**
     * @param string $name
     *
     * @return mixed
     */
    final public static function Read (string $name) {
        return $_COOKIE[APPNAME.$name] ?? NULL;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param int    $days
     */
    final public static function Create (string $name, $value, int $days = 30): void {
        if (isset($value)) {
            setcookie(APPNAME.$name, $value, time() + (86400 * $days), "/");
        }
        else {
            self::Delete(APPNAME.$name);
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param int    $days
     */
    final public static function Update (string $name, $value, int $days = 30): void {
        if (isset($value)) {
            setcookie(APPNAME.$name, $value, time() + (86400 * $days), "/");
        }
        else {
            self::Delete(APPNAME.$name);
        }
    }

    /**
     * @param string $name
     */
    final public static function Delete (string $name): void {
        setcookie(APPNAME.$name, "", time() - 3600);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    final public static function Exists (string $name): bool {
        return isset($_COOKIE[APPNAME.$name]);
    }

}
