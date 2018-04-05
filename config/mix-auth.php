<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Guards
    |--------------------------------------------------------------------------
    |
    | set expiration time for each guard that you want to use,
    | expires_after : set the time in seconds that you want to expire
    | the token, regardless of token usage. 0 to disable.
    | check_after : set the time in seconds that you want to expire the
    | token if the token isn't used during this duration. 0 to disable
    | check_every : this the time in seconds to update last_request time
    | to prevent update in every request, set it to 0 to update in every request
    |
    | Re-migrate your database if you add new guards, or manually update the
    | database to add guard value to `tokens`.`guard` enum
    |
    */
    'guards'         => [
        'web' => [
            'expires_after'     => 31536000, // year,
            'last_request_step' => [
                'check_after' => 1209600, // two weeks
                'check_every' => 86400 // daily
            ],
        ],

        /*
         *
         * Example
         *

        'user' => [
            'expires_after' => 31536000,
            'last_request_step' => [
                'check_after' => 1209600,
                'check_every' => 86400
            ]
        ]

         * Re-migrate if update
         *
         */


    ],

    /*
    |--------------------------------------------------------------------------
    | General Settings
    |--------------------------------------------------------------------------
    |
    | prefix_length : prefix enhance performance of search, it stored in database
    | to reduce retrieved hashes that you intended to check it
    |
    | token_length : the token that you need to generate (main token and not the
    | api token that encoded to base 64)
    |
    | hash_cost : hash cost, it's used in password_hash , 4 is good since data
    | is not very secure, and the higher cost means more time to verify hash
    |
    | token_sessions : generate session when use tokens, if you enable it an
    | session will be created after you use first token, so the next requests
    | will not require a token if you haven't register the auth
    |
    | delete_expired: delete expired tokens, this works when the user attempt to
    | access service using old token.
    |
    */
    'prefix_length'  => 5,
    'token_length'   => 127,
    'hash_cost'      => 4,
    'token_sessions' => false,
    'delete_expired' => false,

    /*
    |--------------------------------------------------------------------------
    | Api token key
    |--------------------------------------------------------------------------
    |
    | the keys that you can pass token through it, so you can set it in the header
    | , url query or even through the request body. you can disable the key by using
    | null in the value of the key
    |
    */
    'keys'           => [
        'header' => 'Authorization',
        'query'  => 'api_token',
        'body'   => 'api_token',
    ],

];