<?php

namespace Bnabriss\MixAuth;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

trait HasMixAuth
{

    /**
     * Generate token data for auth user
     *
     * @param $guard string
     *
     * @return array data about token
     */
    public static function generateTokenForAuth($guard = null)
    {
        $user = Auth::guard($guard)->user();

        return $user->generateToken($guard);


    }

    /**
     * Get all of the access tokens for the user. using Token Data scope
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tokens()
    {
        return $this->hasMany(Token::class, 'user_id')->orderBy('expires_at', 'desc');
    }

    /**
     * Get all of the access tokens for the user. without Token Data scope
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allTokens()
    {
        return $this->hasMany(Token::class, 'user_id')->withoutTokenData()->orderBy('expires_at', 'desc');
    }

    /**
     * Generate token data for costume user
     *
     * @param $guard string
     *
     * @return array data about token
     * @throws \Exception
     */
    public function generateToken($guard = null)
    {
        TokenGenerator::generate($guard, $this);
        $expireSec = config('mix-auth.guards.'.TokenGenerator::$guard.'.expires_after');
        $tokenObj = new Token([
            'guard'        => TokenGenerator::$guard,
            'token'        => TokenGenerator::$hashed_token,
            'prefix'       => TokenGenerator::$prefix,
            'expires_at'   => $expireSec ? Carbon::now()->addSeconds($expireSec) : null,
            'last_request' => Carbon::now(),
        ]);

        try {
            $this->tokens()->save($tokenObj);
        } catch (QueryException $ex) {
        }

        header("Authorization: ".TokenGenerator::$token_64);

        return TokenGenerator::responseData();
    }


}