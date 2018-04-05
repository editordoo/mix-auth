<?php

namespace Bnabriss\MixAuth;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Token extends Model
{
    /**
     * @static scope to use current data of input token
     */
    const SPLITTER_GLOBAL_SCOPE = 'splitter_data';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mix_auth_tokens';
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'token';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['token', 'prefix'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates
        = [
            'expires_at',
            'last_request',
        ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(self::SPLITTER_GLOBAL_SCOPE, function (Builder $builder) {
            $builder->where([
                'guard'   => TokenSplitter::$guard,
                'user_id' => TokenSplitter::$user_id,
            ])->whereRaw("BINARY `prefix`= ?", [TokenSplitter::$prefix])->orderBy('expires_at', 'desc');
        });
    }

    /**
     * remove global scope data
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithoutTokenData(Builder $query)
    {
        return $query->withoutGlobalScope(self::SPLITTER_GLOBAL_SCOPE);
    }

    /**
     * Get user of giving token.
     *
     * @return BelongsTo
     */
    public function user()
    {
        $provider = config('auth.guards.'.$this->guard.'.provider');

        return $this->belongsTo(config('auth.providers.'.$provider.'.model'));
    }

    /**
     * validate token expires_at and last_request time if enabled
     *
     * @return bool
     */
    public function validateNotExpire()
    {
        $lastRequestStep = config('mix-auth.guards.'.$this->guard.'.last_request_step.check_after');
        if ($lastRequestStep > 0 && Carbon::now()->subSecond($lastRequestStep)->greaterThan($this->last_request)) {
            return false; //expired
        }
        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) {
            return false; // not used for long time
        }
        $checkEvery = config('mix-auth.guards.'.$this->guard.'.last_request_step.check_every');
        if ($lastRequestStep > 0 && Carbon::now()->subSecond($checkEvery)->greaterThan($this->last_request)) {
            $this->last_request = Carbon::now();
            $this->save();
        }

        return true;
    }


}