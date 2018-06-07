<?php
/**
 * Created by PhpStorm.
 * User: Bassam
 * Date: 2018-06-07
 * Time: 1:31 AM
 */

namespace Bnabriss\MixAuth\Exceptions;


class TokenGenerationException extends \Exception
{

    protected $message;
    public function __construct($message = 'Unable to generate token.')
    {
        parent::__construct($message);
    }


}