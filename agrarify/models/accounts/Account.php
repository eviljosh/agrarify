<?php

namespace Agrarify\Models\Accounts;

use Agrarify\Models\BaseModel;
use Illuminate\Support\Facades\Hash;

class Account extends BaseModel {

    const CREATE_CODE_MOBILE_APP = 'M';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'given_name'             => 'max:50',
        'surname'                => 'max:50',
        'email_address'          => 'max:100|email|unique:accounts,email_address',
        'create_code'            => 'max:1',
        'verification_code'      => 'max:1',
        'verification_timestamp' => '',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'given_name',
        'surname',
        'email_address',
    ];

    /**
     * @return mixed The database id for the record
     */
    public function getId()
    {
        return $this->getParamOrDefault('id');
    }

    /**
     * @return string Created at date
     */
    public function getCreatedAt()
    {
        return $this->getParamOrDefault('created_at');
    }

    /**
     * @return string Email address
     */
    public function getEmailAddress()
    {
        return $this->getParamOrDefault('email_address');
    }

    /**
     * @param string $email_address
     */
    public function setEmailAddress($email_address)
    {
        $this->email_address = $email_address;
    }

    /**
     * @return string Given name
     */
    public function getGivenName()
    {
        return $this->getParamOrDefault('given_name');
    }

    /**
     * @param string $code The create type code
     */
    public function setCreateCode($code)
    {
        $this->create_code = $code;
    }

    /**
     * @return \Agrarify\Models\Accounts\AccountProfile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param int $id Id of the location
     * @return \Agrarify\Models\Subresources\Location
     */
    public function getLocationById($id)
    {
        return $this->locations()->where('id', '=', $id)->first();
    }

    /**
     * Defines the one-to-many relationship with oauth access tokens
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthAccessTokens()
    {
        return $this->hasMany('Agrarify\Models\Oauth2\OauthAccessToken');
    }

    /**
     * Defines the one-to-many relationship with locations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany('Agrarify\Models\Subresources\Location');
    }

    /**
     * Defines the one-to-one relationship with account profiles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne('Agrarify\Models\Accounts\AccountProfile');
    }

    /**
     * @param string $password_text
     * @return bool
     */
    public function isPasswordValid($password_text)
    {
        return Hash::check($password_text, $this->password);
    }

    /**
     * @param string $password_text
     */
    public function hashAndSetPassword($password_text)
    {
        $this->password = Hash::make($password_text, ['rounds' => 13]);
    }

    /**
     * @return string Date account was created
     */
    public function getMemberSince()
    {
        return date('Y-m-d', $this->getCreatedAt()->getTimeStamp());
    }

    /**
     * Returns primary location or, if none exists, some location associated with the account.
     *
     * @return \Agrarify\Models\Subresources\Location
     */
    public function getPrimaryLocation()
    {
        $primary_location = null;
        foreach ($this->locations as $location) {
            $primary_location = $location;
            if ($location->isPrimary())
            {
                break;
            }
        }
        return $primary_location;
    }

    /**
     * @param $email_address
     * @return Account
     */
    public static function fetchByEmail($email_address)
    {
        return self::firstByAttributes(['email_address' => $email_address]);
    }

}