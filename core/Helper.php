<?php

namespace Core\Helpers;

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

abstract class File {

    /**
     * @param string $path
     *
     * @return bool
     */
    final public static function IsFile (string $path) : bool {
        return file_exists($path) && is_file($path);
    }

    final public static function IsFolder (string $path): string {
        return file_exists($path) && is_dir($path);
    }

    final public static function GetExtension (string $path): string {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    static function SaveImage ($temp_path, $sub_folder = NULL, $name = NULL): bool {
        $_path = is_null($sub_folder) ? IMG : IMG.$sub_folder.DS;
        $_name = is_null($name) ? uniqid() : $name;
        $_file = $_path.basename($_name);
        $i = 1;
        while (self::IsFile($_file)) {
            $_name = "(".$i++.")".$_name;
            $_file = $_path.basename($_name);
        }
        if (getimagesize($temp_path) !== FALSE) {
            return '';
        }
        if (!self::IsFolder($_path)) {
            return '';
        }
        if (!in_array(self::GetExtension($_file), ALLOWED_IMG_EXTENSIONS)) {
            return '';
        }
        if (move_uploaded_file($temp_path, $_file)) {
            return $_name;
        }
        return '';
    }

}

abstract class Api {

    /**
     * @param string $response
     * @param array  $payload
     *
     * @return array|null
     */
    final public static function Response (string $response, array $payload) {
        [$class, $method] = explode('/', $response) ?? ['', ''];
        $class = ucfirst($class);
        $file = $class.'Response.php';
        if (File::IsFile(RESPONSES.$file)) {
            require_once RESPONSES.$file;
            return (new $class())->$method($payload) ?? [];
        }
        return NULL;
    }

    /**
     * @param string $response
     *
     * @return bool
     */
    final public static function Exists (string $response): bool {
        [$class, $method] = explode('/', $response) ?? ['', ''];
        $class = ucfirst($class);
        $file = $class.'Response.php';
        if (File::IsFile(RESPONSES.$file)) {
            require_once RESPONSES.$file;
            return method_exists($class, $method);
        }
        return FALSE;
    }

}

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

}

abstract class Password {

    final public static function Hash (string $passcode): string {
        return password_hash($passcode, PASSWORD_BCRYPT);
    }

    final public static function Match (string $passcode, string $hash): bool {
        return password_verify($passcode, $hash);
    }

}