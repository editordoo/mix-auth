<?php
/**
 * Created by PhpStorm.
 * User: Bassam
 * Date: 2018-04-01
 * Time: 11:50 AM
 */

namespace Bnabriss\MixAuth;

use Bnabriss\MixAuth\Exceptions\TokenGenerationException;
use Bnabriss\MixAuth\Exceptions\UnsupportedGuardException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class TokenGenerator extends TokenAbstract
{

    /**
     * Generate token if there is no token generated yet, else keep the last token
     *
     * @param string          $guard the guard
     * @param Authenticatable $user
     *
     * @return array
     * @throws TokenGenerationException
     * @throws \ReflectionException
     */
    public static function generate($guard = null, $user = null)
    {
        if (empty(self::$token)) {
            self::update($guard, $user);
        }
        return  (new \ReflectionClass(self::class))->getStaticProperties();

    }

    /**
     * Generate new token, even if there is one before
     *
     * @param string          $guard the guard
     * @param Authenticatable $user
     *
     * @return array
     * @throws TokenGenerationException
     * @throws \ReflectionException
     */
    public static function update($guard = null, $user = null)
    {
        $guard = $guard ?: config('auth.defaults.guard');
        if ( ! in_array($guard, array_keys(config('mix-auth.guards')), true)){
            throw new UnsupportedGuardException();
        }
        if (Auth::guard($guard)->guest() && is_null($user)) {
            throw new TokenGenerationException('It\'s not allowed for guest user to generate token');
        }

        $user = $user ?: Auth::guard(self::$guard)->user();
        self::$token = self::generateToken();
        self::$guard = $guard;
        self::$user_id = $user->id;
        self::$user_token = self::userToken();
        self::$token_64 = self::base64_encode();
        self::$hashed_token = self::hash();
        self::$prefix = self::prefix();
        return  (new \ReflectionClass(self::class))->getStaticProperties();
    }

    /**
     * get some data to send it to send it via response
     *
     * @return array
     */
    public static function responseData()
    {
        return [
            'api_token' => self::$token_64,
            'guard'     => self::$guard,
            'user_id'   => self::$user_id,
        ];

    }

    /**
     * Generate random text
     *
     * @return string
     */
    protected static function generateToken()
    {

        return str_random(config('mix-auth.token_length'));
    }

    /**
     * Concatenate some info with the token
     *
     * @return string
     */
    protected static function userToken()
    {

        return self::$token.'.'.self::$guard.'.'.self::$user_id;
    }

    /**
     * Hashing the token
     *
     * @return string
     */
    protected static function hash()
    {

        return password_hash(self::$token, PASSWORD_DEFAULT, ['cost' => config('mix-auth.hash_cost')]);
    }

    /**
     * encode concatenated string to base 64
     *
     * @return string
     */
    protected static function base64_encode()
    {
        $encoded = base64_encode(self::$user_token);

        return rtrim($encoded, '=');
    }


}