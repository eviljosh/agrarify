<?php

namespace Agrarify\Models\Oauth2;

class OauthAccessToken extends Eloquent {

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
        // 'title' => 'required'
    ];

    // Don't forget to fill this array
    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [];

}