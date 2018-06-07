<?php

namespace Bnabriss\MixAuth\Middleware;

use Bnabriss\MixAuth\Exceptions\InvalidTokenException;
use Bnabriss\MixAuth\Exceptions\InvalidTokenGuardException;
use Bnabriss\MixAuth\Token;
use Bnabriss\MixAuth\TokenSplitter;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle($request, $next, $guard = null)
    {

        $inToken = $this->getToken();
        if (empty($inToken) || Auth::guard($guard)->check()) // if no token or session exist
        {
            return $next($request);
        }

        // token exist and no session
        TokenSplitter::split($inToken); // trows InvalidTokenException if not valid syntax

        $guard = $guard ?: config('auth.defaults.guard');
        if (TokenSplitter::$guard !== $guard) // if the used token is not for this guard
        {
            throw new InvalidTokenGuardException();
        }

        // check relative tokens hash in database
        $dbTokens = Token::take(5)->get();
        $token = TokenSplitter::checkTokens($dbTokens);

        if (is_null($token) || ! $token->user) // no token exist in data base or no user exist
        {
            throw new InvalidTokenException();
        }

        if (config('token_sessions')) {
            Auth::guard(TokenSplitter::$guard)->login($token->user);
        } else {
            Auth::guard(TokenSplitter::$guard)->setUser($token->user);
        }

        return $next($request);
    }

    /**
     * Get request token
     *
     * @return string
     */
    private function getToken()
    {
        $key = config('mix-auth.keys.header');
        if ($key && $token = request()->headers->get($key)) {
            return $token;
        }
        $key = config('mix-auth.keys.body');
        if ($key && $token = request()->input($key)) {
            return $token;
        }
        $key = config('mix-auth.keys.query');
        if ($key && $token = request()->query($key)) {
            return $token;
        }

        return null;
    }


}
