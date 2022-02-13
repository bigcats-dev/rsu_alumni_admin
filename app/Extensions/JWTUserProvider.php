<?php

namespace App\Extensions;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class JWTUserProvider implements UserProvider
{
    /**
     * The Mongo User Model
     */
    private $model;

    /**
     * Create a new mongo user provider.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @return void
     */
    public function __construct(User $userModel)
    {
        $this->model = $userModel;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return;
        }

        $user = $this->model->fetchUserByCredentials($credentials);

        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials  Request credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // return $credentials['U_KEYCLOAK_ID'] == $user->U_KEYCLOAK_ID;
    }

    public function retrieveById($identifier)
    {
        return $this->model->find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        // decode JWT
        list($header, $payload, $signature) = explode(".", $token);
        // decode payload and json_decode to array
        $objUser = json_decode($this->base64url_decode($payload), true);
        if (!$objUser) return null;
        // validate attribute
        if (
            !isset($objUser['sub'])
            || !isset($objUser['preferred_username'])
            || !isset($objUser['email'])
            || !isset($objUser['given_name'])
            || !isset($objUser['family_name'])
        ) return null;

        // find user by keycloak id
        $user = $this->retrieveByCredentials(['keycloak_id' => $objUser['sub']]);
        // if check table user not exists insert user
        if (!isset($user)) {
            $user = $this->model->create([
                'keycloak_id' => $objUser['sub'],
                'username' => $objUser['preferred_username'],
                'password' => bcrypt(Helper::generateRandomString(8)),
                'email' => $objUser['email'],
                'fullname' => $objUser['given_name'] . " " . $objUser['family_name'],
            ]);
        }
        return $user;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
    }

    private function base64url_decode($data, $strict = false)
    {
        // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
        $b64 = strtr($data, '-_', '+/');
        // Decode Base64 string and return the original data
        return base64_decode($b64, $strict);
    }
}
