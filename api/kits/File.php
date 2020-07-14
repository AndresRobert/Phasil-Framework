<?php

namespace Kits;

abstract class File {

    private const FL_WRITE = 'wb+';
    private const FL_APPEND = 'ab+';
    private const FL_READ = 'rb+';
    private const FL_EMPTY = '';
    private const FL_CHMOD = 0655;
    private const FL_REPLACE = 'replace';

    /**
     * @param string $path
     *
     * @return bool
     */
    final public static function Create (string $path) : bool {
        if (!self::IsFile($path)) {
            touch($path);
            chmod($path, self::FL_CHMOD);
            $newFile= fopen($path, self::FL_WRITE);
            fwrite($newFile, self::FL_EMPTY);
            fclose($newFile);
        } elseif (!is_writable($path)) {
            chmod($path, self::FL_CHMOD);
        }
        return file_exists($path) && is_file($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    final public static function Delete (string $path) : bool {
        if (self::IsFile($path)) {
            return unlink($path);
        }
        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    final public static function Clear (string $path) : bool {
        if (self::IsFile($path)) {
            $bytes = file_put_contents($path, self::FL_EMPTY);
            return $bytes !== false;
        }
        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    final public static function IsFile (string $path) : bool {
        return file_exists($path) && is_file($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    final public static function Folder (string $path) : bool {
        if (!self::IsFolder($path)) {
            return mkdir($path, self::FL_CHMOD);
        }
        if (!is_writable($path)) {
            return chmod($path, self::FL_CHMOD);
        }
        return true;
    }

    /**
     * @param string $path
     * @return string
     */
    final public static function IsFolder (string $path) : string {
        return file_exists($path) && is_dir($path);
    }

    /**
     * @param string $path
     * @return string
     */
    final public static function GetExtension (string $path) : string {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * @param $temp_path
     * @param null $sub_folder
     * @param null $name
     * @return bool
     */
    final public static function SaveImage ($temp_path, $sub_folder = NULL, $name = NULL): bool {
        $_path = is_null($sub_folder) ? IMG : IMG.$sub_folder.DS;
        $_name = is_null($name) ? uniqid(APPNAME, true) : $name;
        $_file = $_path.basename($_name);
        $i = 1;
        while (self::IsFile($_file)) {
            $_name = "(".$i++.")".$_name;
            $_file = $_path.basename($_name);
        }
        if (getimagesize($temp_path) === FALSE) {
            return self::FL_EMPTY;
        }
        if (self::IsFolder($_path)) {
            return self::FL_EMPTY;
        }
        if (!in_array(self::GetExtension($_file), ALLOWED_IMG_EXTENSIONS)) {
            return self::FL_EMPTY;
        }
        if (move_uploaded_file($temp_path, $_file)) {
            return $_name;
        }
        return self::FL_EMPTY;
    }

    /**
     * @param string $path
     * @param string $content
     * @param string $mode
     * @return bool
     */
    final public static function Write (string $path, string $content, string $mode = null) : bool {
        $append = self::IsFile($path) ? self::FL_APPEND : self::FL_WRITE;
        $mode = $mode === self::FL_REPLACE ? self::FL_WRITE : $append;
        $fileHandle = fopen($path, $mode);
        $result = fwrite($fileHandle, $content);
        fclose($fileHandle);
        return $result;
    }

    /**
     * @param string $path
     * @return string
     */
    final public static function Read (string $path) : string {
        $contents = '';
        if (self::IsFile($path)) {
            $fileHandle = fopen($path, self::FL_READ);
            $contents = fread($fileHandle, filesize($path));
            fclose($fileHandle);
        }
        return $contents ?: '';
    }

}
