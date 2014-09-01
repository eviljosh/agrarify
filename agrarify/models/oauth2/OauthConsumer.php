<?php

namespace Agrarify\Models\Oauth2;

use Agrarify\Models\BaseModel;

class OauthConsumer extends BaseModel {

    const TYPE_MOBILE = 'M';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_consumers';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'name'            => 'required|min:3',
        'description'     => 'required|min:5',
        'consumer_id'     => 'required|min:40|max:40',
        'consumer_secret' => 'required|min:42|max:42',
        'type'            => 'max:1',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'name',
        'type'
    ];

    /**
     * Create a new OauthConsumer model instance.
     *
     * @param array $attributes
     */
    function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->consumer_id = str_random(40);
        $this->consumer_secret = str_random(42);
        $this->type = self::TYPE_MOBILE; // default to mobile consumer
    }

    /**
     * Defines the one-to-many relationship with oauth access tokens
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauth_access_tokens()
    {
        return $this->hasMany('Agrarify\Models\Oauth2\OauthAccessToken');
    }

    /**
     * @return mixed The database id for the record
     */
    public function getId()
    {
        return $this->getParamOrDefault('id', -1);
    }

    /**
     * @return string Name
     */
    public function getName()
    {
        return $this->getParamOrDefault('name');
    }

    /**
     * @return string Description
     */
    public function getDescription()
    {
        return $this->getParamOrDefault('description');
    }

    /**
     * @return string Type
     */
    public function getType()
    {
        return $this->getParamOrDefault('type');
    }

    /**
     * @return string Consumer id
     */
    public function getConsumerId()
    {
        return $this->getParamOrDefault('consumer_id');
    }

    /**
     * @return string Consumer secret
     */
    public function getConsumerSecret()
    {
        return $this->getParamOrDefault('consumer_secret');
    }

    /**
     * @param string $consumer_id
     * @return OauthConsumer
     */
    public static function fetchByConsumerId($consumer_id)
    {
        return self::firstByAttributes(['consumer_id' => $consumer_id]);
    }

}