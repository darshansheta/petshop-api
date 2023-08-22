<?php

namespace App\Services\Auth;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Illuminate\Support\Facades\Config;

class MyJWT
{
    protected $jwtConfig;

    public function __construct()
    {
        $this->jwtConfig = $this->createConfigure();
    }

    protected function createConfigure(): Configuration
    {
        // return Configuration::forSymmetricSigner(
        // 	new Signer\Hmac\Sha256(),
        // 	InMemory::plainText(Config::get('my-jwt.secret')),
        // );
        return Configuration::forAsymmetricSigner(
            new Signer\Rsa\Sha256(),
            InMemory::file(Config::get('my-jwt.private')),
            InMemory::file(Config::get('my-jwt.public'))
            // InMemory::plainText(Config::get('my-jwt.private'))
        );
    }

    public function encode($payload): string
    {

        try {
            return $this->jwtConfig->builder()
                    // Configures the issuer (iss claim)
                    ->issuedBy(Config::get('app.url'))
                    // Configures the id (jti claim)
                    ->identifiedBy($payload['jti'])
                    // Configures the time that the token was issue (iat claim)
                    ->issuedAt($payload['iat'])
                    // Configures the time that the token can be used (nbf claim)
                    ->canOnlyBeUsedAfter($payload['nbf'])
                    // Configures the expiration time of the token (exp claim)
                    ->expiresAt($payload['exp'])
                    // Configures the sub
                    ->relatedTo($payload['sub'])
                    // Configures custom claim uuid of user
                    ->withClaim('uid', $payload['uid'])
                    // Builds a new token
                    ->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey())
                    ->toString();
        } catch (\Exception $e) {
            throw new \Exception('Exception while creating token: ', $e->getCode(), $e);
        }
    }

    public function decode($token)
    {
        try {
            $token = $this->jwtConfig->parser()->parse($token);
        } catch (\Exception $e) {
            throw new \Exception('Invalid token', $e->getCode(), $e);
        }

        return $token->claims()->all();
    }
}
