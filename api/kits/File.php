<?php

namespace Kits;

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

    final public static function SaveImage ($temp_path, $sub_folder = NULL, $name = NULL): bool {
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

    final public static function Write (string $path, string $content) {
        $fileHandle = fopen($path, self::IsFile($path) ? 'a+' : 'w+');
        fwrite($fileHandle, $content);
        fclose($fileHandle);
    }

    final public static function Read (string $path) {
        $contents = '';
        if (self::IsFile($path)) {
            $fileHandle = fopen($path, "r");
            $contents = fread($fileHandle, filesize($path));
            fclose($fileHandle);
        }
        return $contents ?: '';
    }

}
