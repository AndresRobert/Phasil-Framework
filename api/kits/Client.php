<?php

namespace Kits;

abstract class Client {

    /**
     * @param string $method
     * @param string $url
     * @param array  $payload
     * @param array  $headers
     *
     * @return array
     */
    final public static function Request (string $method, string $url, array $payload, array $headers): array {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, Toolbox::ArrayToJson($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (isset($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = ['status' => 'fail', 'response' => [], 'error' => curl_error($ch)];
        }
        else {
            $result = ['status' => 'success', 'response' => Toolbox::JsonToArray($data), 'error' => 'no error'];
        }
        curl_close($ch);
        return $result;
    }

}
