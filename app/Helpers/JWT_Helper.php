<?php

use Config\Services;
use App\Models\UserModel;
use Firebase\JWT\JWT;

/**
 * @throws Exception
 */
function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

/**
 * @throws Exception
 */
function validateJWTFromRequest(string $encodedToken)
{
    $key = Services::getSecretKey();
    $decodedToken = JWT::decode($encodedToken, $key, ['HS256']);
    $userModel = new UserModel();

    $user = $userModel->where('email', $decodedToken->email)->first();
    if (!$user)
        throw new Exception("Invalid account");

    return $user;
}

function getSignedJWTForUser(string $email): string
{
    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ];

    return JWT::encode($payload, Services::getSecretKey());
}
