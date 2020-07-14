<?php

namespace Kits;

abstract class Log {

    private const LOG_TYPES = [
        'ERROR',
        'WARNING',
        'ALERT',
        'NOTICE',
        'EXCEPTION',
        'INFO',
        'REPLACED'
    ];

    final public static function Add (array $data, string $type = 'INFO', array $labels = []): void
    {
        $TODAY = date("Ymd");
        $error_id = time();
        $latestLog = Session::Read('PhasilLatestLog');
        Session::Create('PhasilLatestLog', $data);
        $logInfo = [
            'time' => $error_id,
            'type' => $type,
            'data' => $data,
            'labels' => $labels,
            'request' => [
                'time' => $_SERVER['REQUEST_TIME'],
                'method' => $_SERVER['REQUEST_METHOD'],
                'query' => $_SERVER['QUERY_STRING'],
                'header' => getallheaders(),
            ],
            'server' => [
                'name' => $_SERVER['SERVER_NAME'],
                'address' => $_SERVER['SERVER_ADDR'],
            ],
            'client' => [
                'name' => $_SERVER['REMOTE_HOST'],
                'address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            ],
        ];
        if ($data === []) {
            File::Write(LOGS.'internal'.$TODAY.'.json', Toolbox::ArrayToJson([
                'time' => time(),
                'type' => 'WARNING',
                'data' => [
                    'message' => 'Empty log data',
                    'content' => [],
                ],
                'labels' => [$error_id, 'Auto-generated'],
            ]), 'append');
        }
        if (!in_array($type, self::LOG_TYPES)) {
            File::Write(LOGS.'internal'.$TODAY.'.json', Toolbox::ArrayToJson([
                'time' => time(),
                'type' => 'ERROR',
                'data' => [
                    'message' => 'Wrong log type',
                    'content' => $type
                ],
                'labels' => [$error_id, 'Auto-generated'],
            ]), 'append');
            $logInfo['type'] = 'REPLACED';
        }
        if ($labels === []) {
            File::Write(LOGS.'internal'.$TODAY.'.json', Toolbox::ArrayToJson([
                'time' => time(),
                'type' => 'NOTICE',
                'data' => [
                    'message' => 'No labels',
                    'content' => [],
                ],
                'labels' => [$error_id, 'Auto-generated'],
            ]), 'append');
        }
        if ($latestLog === $data) {
            File::Write(LOGS.'internal'.$TODAY.'.json', Toolbox::ArrayToJson([
                'time' => time(),
                'type' => 'ALERT',
                'data' => [
                    'message' => 'Repetitive log',
                    'content' => $data,
                ],
                'labels' => [$error_id, 'Auto-generated'],
            ]), 'append');
        }
        File::Write(LOGS.'log'.$TODAY.'.json', Toolbox::ArrayToJson($logInfo), 'append');
    }

}
