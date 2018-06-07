<?php
/**
 * Created by PhpStorm.
 * User: Bassam
 * Date: 2018-06-07
 * Time: 1:31 AM
 */

namespace Bnabriss\MixAuth\Exceptions;


class TokenExpiredException extends InvalidTokenException
{
    public function __construct($message = 'Token Expired.')
    {
        parent::__construct($message);
    }
}