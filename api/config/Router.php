<?php

namespace Api;

use Base\Response;
use Kits\Session;
use Kits\Text;

abstract class Route {

    private static array $responseCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    final private static function is_method_allowed (string $request_method) {
        return in_array($request_method, ALLOWED_METHODS);
    }

    /**
     *
     * @param string $request_method
     * @param string $endpoint
     *
     * @return int
     */
    final private static function get_response_code (string $request_method, string $endpoint): int {
        $routes = Session::Read('Routes');
        if (!self::is_method_allowed($request_method)) {
            return 405;
        }
        if (!isset($routes[$request_method])) {
            return 406;
        }
        if (!isset($routes[$request_method][$endpoint])) {
            return 404;
        }
        if (!Response::Exists($routes[$request_method][$endpoint])) {
            return 400;
        }
        return 200;
    }

    /**
     * Clear routes
     *
     */
    final public static function Clear (): void {
        Session::Delete('Routes');
    }

    /**
     * Adds routing to your services
     *
     * @param string $request_method
     * @param string $endpoint
     * @param string $response
     */
    final public static function Create (string $request_method, string $endpoint, string $response): void {
        if (self::is_method_allowed($request_method)) {
            $endpoint = Text::StartsWith(HTACCESS_FOLDER, $endpoint) ? $endpoint : HTACCESS_FOLDER.$endpoint;
            $endpoint = in_array($endpoint, ['', '/']) ? '/' : $endpoint;
            $routes = Session::Read('Routes');
            $routes[$request_method][$endpoint] = $response;
            Session::Create('Routes', $routes);
        }
    }

    /**
     * Renders the response for the API call
     *
     * @param string $request_method
     * @param string $endpoint
     * @param array  $payload
     *
     * @return string
     */
    final public static function Read (string $request_method, string $endpoint, array $payload = []): string {
        $routes = Session::Read('Routes');
        $responseCode = self::get_response_code($request_method, $endpoint);
        $response = $responseCode === 200 ? Response::Get($routes[$request_method][$endpoint], $payload) : [];
        $responseCode = isset($response['response_code']) ? $response['response_code'] : $responseCode;
        http_response_code($responseCode);
        return json_encode(['status' => self::$responseCodes[$responseCode], 'response' => $response] + ABOUT);
    }

}
