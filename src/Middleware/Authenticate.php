<?php

namespace Bnabriss\MixAuth\Middleware;

use Bnabriss\MixAuth\Token;
use Bnabriss\MixAuth\TokenSplitter;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        if (empty($inToken) && Auth::guard($guard)->check()) // if no token and session exist
        {
            return $next($request);
        }

        // token exist and no session
        TokenSplitter::split($inToken = $this->getToken()); // trows UnauthorizedHttpException if not valid syntax

        $guard = $guard ?: config('auth.defaults.guard');
        if (TokenSplitter::$guard !== $guard) // if the used token is not for this guard
        {
            throw new UnauthorizedHttpException('');
        }

        // check relative tokens hash in database
        $dbTokens = Token::take(5)->get();
        $token = TokenSplitter::checkTokens($dbTokens);

        if (is_null($token) || ! $token->user) // no token exist in data base or no user exist
        {
            throw new UnauthorizedHttpException('');
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
        if ($c = config('mix-auth.keys.query')) {
            return request()->headers->get($c);
        }
        if ($c = config('mix-auth.keys.body')) {
            return request()->input($c);
        }
        if ($c = config('mix-auth.keys.query')) {
            return request()->query($c);
        }

        return null;
    }


}