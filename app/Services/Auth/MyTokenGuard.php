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

class MyTokenGuard implements Guard
{
    use GuardHelpers;

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

    public function userOrFail()
    {
        if (!$user = $this->user()) {
            throw new UserNotDefinedException;
        }

        return $user;
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

    public function issueToken(User $user)
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

    protected function getPayload(User $user): array
    {
        $now  = new \DateTimeImmutable();

        return [
            'sub' => $user->id,
            'uid' => $user->uuid,
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


    /**
     * Alias for getPayload().
     *
     * @return \Tymon\JWTAuth\Payload
     */
    public function payload()
    {
        return $this->getPayload();
    }

    public function setRequest(Request $request): MyTokenGuard
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set the token.
     *
     * @param  \Tymon\JWTAuth\Token|string  $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->jwt->setToken($token);

        return $this;
    }

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
                return;
            }
            if ($tokenModel && $tokenModel->expires_at->isPast()) {
                throw new MYJWTTokenExpired('Token expired');
            }

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
