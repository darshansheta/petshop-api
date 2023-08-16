<?php

namespace App\Services\Auth;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Illuminate\Support\Facades\Config;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;

class MyJWT
{
	protected $jwtConfig;

	public function __construct()
    {
        $this->jwtConfig = $this->createConfigure();
    }

    protected function createConfigure(): Configuration
    {
    	return Configuration::forSymmetricSigner(
    		new Signer\Hmac\Sha256(),
    		InMemory::plainText(Config::get('my-jwt.secret')),
    	);
    	return Configuration::forAsymmetricSigner(
    	    new Signer\Rsa\Sha256(),
    	    InMemory::file(Config::get('my-jwt.public')),
    	    // InMemory::plainText(Config::get('my-jwt.private'))
    	    InMemory::file(Config::get('my-jwt.private'))
    	);
    }

    public function encode($payload): string
    {
    	$now  = new \DateTimeImmutable();

    	try {
	    	return $this->jwtConfig->builder()
		    		// Configures the issuer (iss claim)
				    ->issuedBy(Config::get('app.url'))
				    // Configures the id (jti claim)
				    ->identifiedBy($payload['identifier'])
				    // Configures the time that the token was issue (iat claim)
				    ->issuedAt($now)
				    // Configures the time that the token can be used (nbf claim)
				    ->canOnlyBeUsedAfter($now->modify('+1 minute'))
				    // Configures the expiration time of the token (exp claim)
				    ->expiresAt($now->modify('+1 day'))
				    // Configures a new claim, called "uid"
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
            throw new Exception('Invalid token', $e->getCode(), $e);
        }

        // if (
        // 	! $this->jwtConfig->validator()->validate(
	    //     	$token, 
	    //     	// ...$this->jwtConfig->validationConstraints()
	    //     	new RelatedTo('1234567890')
	    //     )
    	// ) {
        //     throw new \Exception('Invalid token: unable to validate');
        // }

        return $token->claims()->all();
    }
    }
