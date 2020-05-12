<?php

use Base\Response;

class Dom extends Response {

    public function info (): array {
        return [
            'body' => [
                'header' => [
                    'h1' => 'Welcome to Phasil Framework',
                    'p' => 'Easy PHP ERA (Endpoint Response API) facilitator'
                ],
                'main' => [
                    'p' => [
                        'Create your routes, call endpoints, get responses. Need more than that?',
                        'Plain PHP code ready to be tuned! Any other implementations has no limits.'
                    ]
                ],
                'footer' => [
                    'h4' => 'Find us',
                    'img' => 'https://phasil.acode.cl/src/img/phasil.png',
                    'a' => [
                        'https://phasil.acode.cl',
                        'https://github.com/AndresRobert/Phasil-Framework',
                    ]
                ]
            ]
        ];
    }

}