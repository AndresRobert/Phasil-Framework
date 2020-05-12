<?php

namespace Kits;

abstract class Auth {

    final public static function Hash (string $passcode): string {
        return password_hash($passcode, PASSWORD_BCRYPT);
    }

    final public static function Match (string $passcode, string $hash): bool {
        return password_verify($passcode, $hash);
    }

}