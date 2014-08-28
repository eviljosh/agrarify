<?php

namespace Agrarify\Models\Accounts;

use Agrarify\Models\BaseModel;

class AccountProfile extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'account_profiles';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'profile_slug'                       => 'max:100',
        'display_name'                       => 'max:100',
        'favorite_veggie'                    => 'max:50',
        'is_interested_in_getting_veggies'   => 'boolean',
        'is_interested_in_giving_veggies'    => 'boolean',
        'is_interested_in_gardening'         => 'boolean',
        'is_interested_in_providing_gardens' => 'boolean',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'profile_slug',
        'display_name',
        'favorite_veggie',
        'bio',
        'is_interested_in_getting_veggies',
        'is_interested_in_giving_veggies',
        'is_interested_in_gardening',
        'is_interested_in_providing_gardens',
    ];

    /**
     * Create a new AccountProfile model instance.
     *
     * @param array $attributes
     */
    function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        if (empty($this->profile_slug))
        {
            $this->profile_slug = str_random(45);  // default the slug to a random string
        }
    }

    /**
     * Defines the one-to-one relationship with accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Agrarify\Models\Accounts\Account');
    }

    /**
     * @return \Agrarify\Models\Accounts\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param \Agrarify\Models\Accounts\Account $account
     */
    public function setAccount($account)
    {
        $this->account_id = $account->getId();
    }

    /**
     * @return string The profile url slug
     */
    public function getSlug()
    {
        return $this->getParamOrDefault('profile_slug');
    }

    /**
     * @param string $slug The new profile url slug
     */
    public function setSlug($slug)
    {
        $this->profile_slug = $slug;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (isset($this->display_name))
        {
            return $this->display_name;
        }
        elseif ($this->getAccount()->getGivenName())
        {
            return $this->getAccount()->getGivenName();
        }
        else
        {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getHomeLocationString()
    {
        if ($location = $this->getAccount()->getPrimaryLocation())
        {
            return $location->getCity() . ', ' . $location->getState();
        }
        return '';
    }

    /**
     * @param $slug
     * @return AccountProfile
     */
    public static function fetchBySlug($slug)
    {
        return self::firstByAttributes(['profile_slug' => $slug]);
    }

}