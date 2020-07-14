<?php

namespace Kits;

use JsonException;

abstract class Toolbox {

    final public static function ArrayDepth (array $array): int {
        $depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $newDepth = self::ArrayDepth($value) + 1;
                $depth = $newDepth > $depth ? $newDepth : $depth;
            }
        }
        return $depth;
    }

    final public static function ArrayToJson (array $array) : string {
        try {
            return json_encode($array, JSON_THROW_ON_ERROR, 512);
        } catch (JsonException $e) {
            return '';
        }
    }

    final public static function JsonToArray (string $jsonString) : array {
        try {
            return json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR) ?? [];
        } catch (JsonException $e) {
            return [];
        }
    }

}
