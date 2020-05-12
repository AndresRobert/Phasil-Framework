<?php

namespace Kits;

abstract class Client {

    /**
     * @param string $method
     * @param string $url
     * @param array  $payload
     * @param array  $headers
     *
     * @return mixed
     */
    final public static function Request (string $method, string $url, array $payload, array $headers) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (isset($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = ['status' => 'success', 'response' => [], 'error' => curl_error($ch)];
        }
        else {
            $result = ['status' => 'success', 'response' => json_decode($data), 'error' => 'no error'];
            curl_close($ch);
        }
        return $result;
    }

}
