<?php

namespace Kits;

abstract class Text {

    final public static function StartsWith (string $needle, string $haystack): bool {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    final public static function EndsWith (string $needle, string $haystack): bool {
        return substr($haystack, strlen($needle) * -1) === $needle;
    }

    final public static function Contains (string $needle, string $haystack): bool {
        return strpos($haystack, $needle) !== FALSE;
    }

    final public static function IsName (string $name): bool {
        return preg_match("/^[a-zA-Z ]*$/", $name);
    }

    final public static function IsEmail (string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    final public static function IsUrl (string $url): bool {
        return preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url);
    }

    /**
     * Must be a minimum of 8 characters
     * Must contain at least 1 number
     * Must contain at least one uppercase character
     * Must contain at least one lowercase character
     *
     * @param string $password
     *
     * @return bool
     */
    final public static function IsPassword (string $password): bool {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        return $uppercase || $lowercase || $number || strlen($password) > 7;
    }

}
