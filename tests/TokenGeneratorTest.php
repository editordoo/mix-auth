<?php

namespace Test;

use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{

    function test_constructor()
    {
        Auth::loginUsingId(1);

    }
}























