<?php
/**
 * Created by PhpStorm.
 * User: Bassam
 * Date: 2018-06-07
 * Time: 1:31 AM
 */

namespace Bnabriss\MixAuth\Exceptions;


class InvalidTokenSyntaxException extends InvalidTokenException
{
    public function __construct($message = 'Invalid token syntax.')
    {
        parent::__construct($message);
    }
}