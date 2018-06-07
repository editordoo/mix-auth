<?php
/**
 * Created by PhpStorm.
 * User: Bassam
 * Date: 2018-06-07
 * Time: 1:31 AM
 */

namespace Bnabriss\MixAuth\Exceptions;


class InvalidTokenException extends \Exception
{

    protected $message;
    public function __construct($message = 'Invalid token.')
    {
        parent::__construct($message);
    }


}