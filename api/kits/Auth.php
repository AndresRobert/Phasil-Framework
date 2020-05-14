<?php

namespace Kits;

use DomainException;
use Exception;
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use User;

abstract class Auth {

    /**
     * Hashes a pascode
     *
     * @param string $passcode
     *
     * @return string
     */
    final public static function Hash (string $passcode): string {
        return password_hash($passcode, PASSWORD_BCRYPT);
    }

    /**
     * Validate a passcode matching its hashed version
     *
     * @param string $passcode
     * @param string $hash
     *
     * @return bool
     */
    final public static function Match (string $passcode, string $hash): bool {
        return password_verify($passcode, $hash);
    }

    /**
     * Generates a JSON Web Token
     *
     * @param User $user
     *
     * @return array
     */
    final public static function JWToken (User $user) {
        $issue_date = time();
        $token = [
            "iss" => JWT_ISSUER,
            "aud" => JWT_AUDIENCE,
            "iat" => $issue_date,
            "nbf" => $issue_date + JWT_NOT_BEFORE,
            "exp" => $issue_date + JWT_EXPIRE,
            "data" => [
                "id" => $user->get('id'),
                "user_name" => $user->get('user_name'),
                "email" => $user->get('email')
            ]
        ];
        return [
            'token' => JWT::encode($token, JWT_SECRET, 'HS256'),
            'expire_at' => $issue_date + JWT_EXPIRE
        ];
    }

    /**
     * Validates JWT on Bearer authorization
     *
     * @return array
     */
    final public static function JWTValidate (): array {
        if (isset(HEADERS['Authorization'])) {
            $token = HEADERS['Authorization'];
            $jwt = Text::StartsWith('Bearer', $token) ? substr($token, 7) : $token;
            try {
                $body = JWT::decode($jwt, JWT_SECRET, ['HS256']);
                if (is_null($body)) {
                    return ['status' => 'fail', 'id' => '-1'];
                }
                return ['status' => 'success', 'id' => $body->data->id, 'message' => 'Authorized'];
            }
            catch (DomainException $e) {
                return ['status' => 'fail', 'id' => '-1', 'message' => 'Invalid token'];
            }
            catch (ExpiredException $e) {
                return ['status' => 'fail', 'id' => '-1', 'message' => 'Expired token'];
            }
            catch (Exception $e) {
                return ['status' => 'fail', 'id' => '-1', 'message' => 'Signature verification failed'];
            }
        }
        else {
            return ['status' => 'fail', 'id' => '-1', 'message' => 'Not Authorized'];
        }
    }

}