<?php

namespace Base;

use Api\Status;
use Kits\Auth;
use Kits\File;

/**
 * The force is strong with this Response class and you need to extend your response classes from this one.
 *
 * Class Response
 */
class Response {

    protected array $require_authorization = [];

    /**
     * Nothing special yet
     *
     * Response constructor.
     */
    public function __construct () { }

    protected static function RequiresAuthorization ($function): array {
        $validate = Auth::JWTValidate();
        if ($validate['status'] === 'success') return $function();
        $validate['response_code'] = 401;
        return $validate;
    }

    /**
     * @param string $response
     * @param array  $payload
     *
     * @return array|null
     */
    final public static function Get (string $response, array $payload = []) {
        [$class, $method] = explode('/', $response) ?? ['', ''];
        if ($class !== '') {
            $class = ucfirst($class);
        }
        else {
            $method = ucfirst($method);
        }
        $file = $class.'Response.php';
        if ($file === 'Response.php') {
            return self::$method($payload) ?? [];
        }
        elseif (File::IsFile(RSP.$file)) {
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
        if ($class !== '') {
            $class = ucfirst($class);
        }
        else {
            $method = ucfirst($method);
        }
        $file = $class.'Response.php';
        if ($file === 'Response.php') {
            return true;
        }
        if (File::IsFile(RSP.$file)) {
            require_once RSP.$file;
            return method_exists($class, $method);
        }
        return FALSE;
    }

    final public static function Status (): array {
        return Status::All();
    }

}