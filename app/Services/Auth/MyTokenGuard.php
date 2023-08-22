<?php

namespace App\Services\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\Auth\Exceptions\MyJWTUserHasTokenException;
use App\Services\Auth\Exceptions\MYJWTTokenExpired;
use Illuminate\Support\Str;
use App\Services\Auth\Exceptions\MyJWTTokenMissing;

class MyTokenGuard implements Guard
{
    use GuardHelpers;

    /**
     * The user provider implementation.
     *
     * @var \App\Services\Auth\MyJWT
     */
    protected $jwt;
    
    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $provider;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(MyJWT $jwt, UserProvider $provider, Request $request)
    {
        $this->jwt = $jwt;
        $this->provider = $provider;
        $this->request = $request;
    }

    public function getToken(): ?string
    {
        return $this->request->bearerToken();
    }

    public function validate(array $credentials = [])
    {
        return (bool) $this->attempt($credentials, false);
    }

    public function attempt(array $credentials = [], $remember = true): bool|string
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user && $this->provider->validateCredentials($user, $credentials)) {
            return $remember ? $this->issueToken($user) : true;
        }

        return false;
    }

    protected function checkUserHasToken($user): bool
    {
        $user->jwtTokens()->where('expires_at', '<', now())->delete();

        return (bool) $user->jwtTokens()->count();
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return string $token
     */
    public function issueToken($user): string
    {
        // if ($this->checkUserHasToken($user)){
        //     throw new MyJWTUserHasTokenException("User has token");
        // }

        $payload = $this->getPayload($user);

        $token = $this->jwt->encode($payload);

        $user->jwtTokens()->create([
            'expires_at'   => $payload['exp'],
            'last_used_at' => now(),
            'unique_id' => $payload['jti'],
            'token_title' => 'API token'
        ]);

        return $token;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User  $user
     * @return array $payload
     */
    protected function getPayload($user): array
    {
        $now  = new \DateTimeImmutable();

        return [
            'sub' => $user->getAuthIdentifier(),
            'uid' => $user->uuid, /** @phpstan-ignore-line */
            'jti' => Str::random(),
            'iat' => $now,
            'nbf' => $now->modify('+1 minute'),
            'exp' => $now->modify('+1 day'),

        ];
    }

    public function logout(): void
    {
        $user = $this->user();

        if (empty($user)) {
            throw new MyJWTTokenMissing('Token missing');
        }

        $payload = $this->getPayloadFromToken();

        $this->invalidate($user, $payload['jti']);
    }

    public function refresh()
    {
    }

    protected function invalidate(User $user, string $jti): void
    {
        $user->jwtTokens()->where('unique_id', $jti)->delete();
    }

    public function setRequest(Request $request): MyTokenGuard
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @return  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     */
    public function user()
    {
        if (!empty($this->user)) {
            return $this->user;
        }

        $token = $this->getToken();

        if ($token && $payload = $this->jwt->decode($token)) {
            $user = $this->provider->retrieveById($payload['sub']);

            $tokenModel = $user->jwtTokens()->where('unique_id', $payload['jti'])->first();
            if (empty($tokenModel)) {
                return null;
            }
            /** @phpstan-ignore-next-line */
            if ($tokenModel && $tokenModel->expires_at->isPast()) {
                throw new MYJWTTokenExpired('Token expired');
            }

            /** @phpstan-ignore-next-line */
            $tokenModel->last_used_at = now();
            $tokenModel->save();
            return $this->user = $user;

        }
    }

    protected function getPayloadFromToken(): array
    {
        $token = $this->getToken();
        return $this->jwt->decode($token);
    }

    // authenticate
    // hasUser
    // check
    // guest
    // id
    // setUser
    // forgetUser
    // getProvider
    // setProvider

}
