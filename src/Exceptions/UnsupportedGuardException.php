<?php
/**
 * Created by PhpStorm.
 * User: Bassam
 * Date: 2018-06-07
 * Time: 1:31 AM
 */

namespace Bnabriss\MixAuth\Exceptions;


class UnsupportedGuardException extends TokenGenerationException
{

    protected $message;
    public function __construct($message = 'Unsupported guard in config.')
    {
        parent::__construct($message);
    }


}