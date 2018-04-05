## Mix Auth 
This auth used to mix laravel sessions with api tokens, so you can use the same middleware to authenticate request if it has sessions or it support api token.

#### Features
* More secure than default token auth in laravel since it using hashes for tokens.
* Support many tokens for the same device, so the devices will not share same token.
* Support sessions if it exist in the request and there is no token entered.
* Support many ways to provide token throw url query, request body and request header.
* Support many guards.
* More customizable since you can specify expire time, max step time between requests.


#### Requirements
* PHP >=7.1.3
* Laravel ^5.6

## Installation  
Require this package with composer. 
```
composer require bnabriss/mix-auth
```

Laravel >5.5 uses Package Auto-Discovery, so does'nt require you to manually add the ServiceProvider. If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php` 

```php
Bnabriss\MixAuth\MixAuthProvider::class,
```

You can customize some settings of the vendor using `config/mix-auth.php` that you can publish it using the publish command
```
php artisan vendor:publish --provider="Bnabriss\MixAuth\MixAuthProvider"
```
so we encourage you to read [config file](https://github.com/bnabriss/mix-auth/blob/master/config/debugbar.php) and learn more about customization
After you customize you config you should migrate your database to add tokens table to your database using migration command
```
php artisan migrate
```
> Note that migration depends on some configuration in config file, so we encourage you to re-migrate database after you make changes in the config file.

The models that use this auth should use the `HasMixAuth.php` trait in your model to add some helpful methods in your class
```
namespace App;

use Bnabriss\MixAuth\HasMixAuth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasMixAuth, Notifiable;
}
``` 
## Usage
#### Generate token
You can generate token for the user simply generate token of custom user or for authenticated user with specific guard.
```php
// for custom user 
\App\User::first()->generateToken('guard-name');
// for authenticated user 
\App\User::generateTokenForAuth('guard-name');
```
> Note that to use the guard you must specify it the config file so you can use it, by default the file contains only web guard, see user example in the file itself.

> Note that the generate token methods will not generate sessions if you disable the key `token_sessions` in the config 
#### Auth middleware 
You can simply set the middleware for the route using the route-middleware   
```php
Route::get('/secure-page', function () {

})->middleware('mix.auth:guard-name');
```
#### Delete token
You can delete user token by using the defined relations token in the trait
```php
$user->tokens()->delete();
```
or you can even delete all tokens for that user (other device tokens) by 
```php
$user->allTokens()->delete();
```

> You may need to disable api middleware `auth:api` if you need to use this vendor to api route 