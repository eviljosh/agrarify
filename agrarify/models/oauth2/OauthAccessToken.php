<?php

namespace Agrarify\Models\Oauth2;

use Agrarify\Models\Accounts\Account;
use Agrarify\Models\BaseModel;

class OauthAccessToken extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_access_tokens';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'token'  => 'required|min:40|max:40',
        'oauth_consumer_id' => 'required|numeric',
        'account_id' => 'required|numeric',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Create a new OauthAccessToken model instance.
     *
     * @param array $attributes
     */
    function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->token = str_random(40);
    }

    /**
     * Defines the many-to-one relationship with accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Agrarify\Models\Accounts\Account');
    }

    /**
     * Defines the many-to-one relationship with oauth consumers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oauth_consumer()
    {
        return $this->belongsTo('Agrarify\Models\Oauth2\OauthConsumer');
    }

    /**
     * @param Account $account
     * @param OauthConsumer $consumer
     * @return OauthAccessToken
     */
    public static function fetchByAccountAndConsumer($account, $consumer)
    {
        return self::firstByAttributes([
            'account_id' => $account->id,
            'oauth_consumer_id' => $consumer->id,
        ]);
    }

    /**
     * @param $token_string
     * @return OauthAccessToken
     */
    public static function fetchByToken($token_string)
    {
        return self::firstByAttributes(['token' => $token_string]);
    }

}