<?php

require_once MDL.'UserModel.php';

use Base\Response;
use Kits\Auth;
use Kits\Text;

class Users extends Response {


    public function register (array $userPayload): array {
        $return = [
            'status' => 'fail',
            'message' => 'Password must be a min of 8 chars, at least 1 number, 1 lowercase char and 1 uppercase char',
            'id' => '',
        ];
        if (isset($userPayload['password']) &&
            Text::IsPassword($userPayload['password'])) {
            $userPayload['password'] = Auth::Hash($userPayload['password']);
            $User = new User();
            if (isset($userPayload['email']) &&
                Text::IsEmail($userPayload['email']) &&
                !$User->exists('email', $userPayload['email'])) {
                if (isset($userPayload['user_name']) &&
                    !$User->exists('user_name', $userPayload['user_name'])) {
                    $User->set($userPayload);
                    if ($User->create()) {
                        $return = [
                            'status' => 'success',
                            'message' => 'User successfully registered',
                            'id' => $User->get('id'),
                        ];
                    }
                    else {
                        $return = [
                            'status' => 'fail',
                            'message' => 'User could not be registered',
                            'id' => '',
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'fail',
                        'message' => 'User name is already taken',
                        'id' => '',
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'fail',
                    'message' => 'Email is already taken',
                    'id' => '',
                ];
            }
        }
        return $return;
    }

    public function login (array $userPayload): array {
        $response = [
            'response_code' => 400,
            'status' => 'fail',
            'message' => 'Invalid data',
            'email' => NULL,
            'token' => NULL,
            'expiration' => NULL
        ];
        if (isset($userPayload['password']) && isset($userPayload['user_name'])) {
            $User = new User();
            if ($User->readBy(['user_name' => $userPayload['user_name']])) {
                if (Auth::Match($userPayload['password'], $User->get('password'))) {
                    $tokenData = Auth::JWToken($User);
                    $response = [
                        'response_code' => 200,
                        'status' => 'success',
                        'message' => 'Successful login',
                        'email' => $User->get('email'),
                        'token' => $tokenData['token'],
                        'expiration' => $tokenData['expire_at']
                    ];
                }
                else {
                    $response['response_code'] = 401;
                    $response['message'] = 'Wrong password';
                }
            }
            else {
                $response['response_code'] = 401;
                $response['message'] = 'User not found';
            }
        }
        return $response;
    }

    function getByFilter ($filter): array {
        return self::RequiresAuthorization(
            function () use ($filter) {
                return (new User())->filter(['*'], $filter);
            }
        );
    }

}