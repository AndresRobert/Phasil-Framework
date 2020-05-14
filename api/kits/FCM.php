<?php

namespace Kits;

abstract class FCM {

    private const ENDPOINT = 'https://fcm.googleapis.com/fcm/send';

    /**
     *  $notification = [
     *      "title" => "Notification title",
     *      "body" => "Hello I am from Your php server",
     *      "icon" => "icon_launcher"
     *  ]
     *
     * @param array  $registrationId
     * @param array  $notification
     * @param string $priority
     *
     * @return bool
     */
    public static function pushMessage(array $registrationId, array $notification, string $priority = 'high'): bool {
        $postFields = [
            'to' => $registrationId,
            'notification' => $notification,
            'priority' => $priority,
        ];
        $send = Client::Request(
            'POST',
            self::ENDPOINT,
            $postFields,
            [
                'Content-Type: application/json',
                'Authorization: key='.FCM_ACCESS_KEY
            ]
        );
        return $send['status'] === 'success';
    }

}