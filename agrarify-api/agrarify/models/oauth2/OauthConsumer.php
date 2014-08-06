<?php

namespace Agrarify\Models\Oauth2;

use Illuminate\Database\Eloquent\Model;

class OauthConsumer extends Model {

    function __construct() {
        parent::__construct();
        $this->consumer_id = str_random(40);
        $this->consumer_secret = str_random(42);
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_consumer';

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