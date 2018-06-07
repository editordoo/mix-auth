<?php
/**
 * Created by PhpStorm.
 * User: Bassam
 * Date: 2018-04-01
 * Time: 11:50 AM
 */

namespace Bnabriss\MixAuth;

use Bnabriss\MixAuth\Exceptions\InvalidTokenException;
use Bnabriss\MixAuth\Exceptions\InvalidTokenGuardException;
use Bnabriss\MixAuth\Exceptions\InvalidTokenSyntaxException;
use Bnabriss\MixAuth\Exceptions\TokenExpiredException;
use Illuminate\Database\Eloquent\Collection;

class TokenSplitter extends TokenAbstract
{


    /**
     * split token to get original data
     *
     * @param string $token64
     *
     * @throws InvalidTokenException
     */
    public static function split($token64)
    {
        self::$token_64 = $token64;
        self::$user_token = self::base64_decode();
        list(self::$token, self::$guard, self::$user_id) = static::extractData();
        self::validateSyntax();
        self::$prefix = self::prefix();
    }

    /**
     * check database tokens' hashes with splitted one
     *
     * @param Collection|Token[] $tokens the database tokens
     *
     * @return Token|null the verified token
     * @throws InvalidTokenSyntaxException|TokenExpiredException
     * @throws \Exception
     */
    public static function checkTokens($tokens)
    {

        foreach ($tokens as $token) {
            if (password_verify(self::$token, $token->token)) {
                if ( ! $token->validateNotExpire()) {
                    if (config('mix-auth.delete_expired')) {
                        $token->delete();
                    }

                    throw new InvalidTokenSyntaxException();
                }

                return $token;
            }
        }

        return null;
    }

    /**
     * validate splitted token data to match expected data types
     *
     * @throws InvalidTokenGuardException|InvalidTokenSyntaxException
     */
    protected static function validateSyntax()
    {
        if (strlen(self::$token) !== config('mix-auth.token_length')) {
            throw new InvalidTokenSyntaxException('');
        }
        if ( ! in_array(self::$guard, array_keys(config('mix-auth.guards')), true)) {
            throw new InvalidTokenGuardException('');
        }
        if ( ! ctype_digit(self::$user_id) && ! self::$user_id) {
            throw new InvalidTokenSyntaxException('');
        }
        self::$user_id = (int)self::$user_id;

    }

    /**
     * get concatenated string from the base 64 text
     *
     * @return string
     *
     * @throws InvalidTokenException
     */
    protected static function base64_decode()
    {
        $userToken = base64_decode(self::$token_64, true);
        if($userToken === false)
            throw new InvalidTokenException();
        return $userToken;
    }

    /**
     * extract data from decoded string
     *
     * @return array
     * @throws InvalidTokenSyntaxException
     */
    protected static function extractData()
    {
        $arr = explode('.', self::$user_token);
        if (count(array_filter($arr)) !== 3) {
            throw new InvalidTokenSyntaxException();
        }

        return $arr;
    }

}