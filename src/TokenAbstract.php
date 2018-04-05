<?php
/**
 * Created by PhpStorm.
 * User: Bassam
 * Date: 2018-04-01
 * Time: 11:50 AM
 */

namespace Bnabriss\MixAuth;

use Exception;
use Illuminate\Support\Facades\Auth;

abstract class TokenAbstract
{

    /**
     * the random generated text to be hashed
     *
     * @static string
     */
    static $token;         // abcde
    /**
     * concat random string with some data about user
     *
     * @static string
     */
    static $user_token;    // abcde.admin.5
    /**
     * convert concatenated string to base64 so it contains only alpha-number characters
     *
     * @static string
     */
    static $token_64;      // Ab5Cd6
    /**
     * the has of the token, to be saved in the database
     *
     * @static string
     */
    static $hashed_token;  // $Oymde
    /**
     * the hash user id
     *
     * @static int
     */
    static $user_id;       // 5
    /**
     * the token prefix to enhance db search performance
     *
     * @static string
     */
    static $prefix;        // ab
    /**
     * the auth guard
     *
     * @static string
     */
    static $guard;


    /**
     * get prefix of the token
     *
     * @return string
     */
    protected static function prefix()
    {

        return substr(self::$token, 0, config('mix-auth.prefix_length'));
    }


}