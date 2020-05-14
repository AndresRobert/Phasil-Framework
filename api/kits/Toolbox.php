<?php

namespace Kits;

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

}
