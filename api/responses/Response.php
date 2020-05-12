<?php

namespace Base;

use Kits\File;

/**
 * The force is strong with this Response class and you need to extend your response classes from this one.
 *
 * Class Response
 */
class Response {

    /**
     * Nothing special yet
     *
     * Response constructor.
     */
    public function __construct () { }

    /**
     * Seems like a good idea to implement an authorization system
     *
     * @return bool
     */
    public function authorized (): bool { return TRUE; }

    /**
     * @param string $response
     * @param array  $payload
     *
     * @return array|null
     */
    final public static function Get (string $response, array $payload) {
        [$class, $method] = explode('/', $response) ?? ['', ''];
        $class = ucfirst($class);
        $file = $class.'Response.php';
        if (File::IsFile(RSP.$file)) {
            require_once RSP.$file;
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
        if (File::IsFile(RSP.$file)) {
            require_once RSP.$file;
            return method_exists($class, $method);
        }
        return FALSE;
    }

}