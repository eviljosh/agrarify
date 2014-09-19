<?php

namespace Agrarify\Models\Accounts;

use Agrarify\Models\BaseModel;
use Agrarify\Models\Subresources\ConfirmationToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class Account extends BaseModel {

    const CREATE_CODE_MOBILE_APP = 'M';

    const VERIFICATION_CODE_EMAIL = 'E';

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
     * Defines the one-to-many relationship with confirmation tokens
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function confirmationTokens()
    {
        return $this->hasMany('Agrarify\Models\Subresources\ConfirmationToken');
    }

    /**
     * Defines the one-to-many relationship with veggies
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function veggies()
    {
        return $this->hasMany('Agrarify\Models\Veggies\Veggie');
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
     * Defines the one-to-many relationship with push registrations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pushRegistrations()
    {
        return $this->hasMany('Agrarify\Models\Accounts\PushRegistration');
    }

    /**
     * @return mixed The database id for the record
     */
    public function getId()
    {
        return $this->getParamOrDefault('id');
    }

    /**
     * @return \Carbon\Carbon Created at date
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
     * @return string Surname
     */
    public function getSurname()
    {
        return $this->getParamOrDefault('surname');
    }

    /**
     * @param string $code The create type code
     */
    public function setCreateCode($code)
    {
        $this->create_code = $code;
    }

    /**
     * @return string Verification code
     */
    public function getVerificationCode()
    {
        $this->getParamOrDefault('verification_code');
    }

    /**
     * Sets verification code and updates verification timestamp appropriately
     *
     * @param string $code
     */
    public function setVerificationCode($code)
    {
        $this->verification_code = $code;

        if (!empty($code))
        {
            $this->setVerificationTimestampToNow();
        }
        else
        {
            $this->verification_timestamp = null;
        }
    }

    /**
     * Updates verification timestamp to present
     */
    private function setVerificationTimestampToNow()
    {
        $this->verification_timestamp = new \DateTime;
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
     * @param int $id Id of the veggie
     * @return \Agrarify\Models\Veggies\Veggie
     */
    public function getVeggieById($id)
    {
        return $this->veggies()->where('id', '=', $id)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPushRegistrations()
    {
        return $this->pushRegistrations;
    }

    /**
     * @param int $id Id of the push registration
     * @return \Agrarify\Models\Accounts\PushRegistration
     */
    public function getPushRegistrationById($id)
    {
        return $this->pushRegistrations()->where('id', '=', $id)->first();
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
     * @return bool Indication of whether account password is set or not
     */
    public function hasPassword()
    {
        return !empty($this->getParamOrDefault('password'));
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

    public function deleteOutstandingConfirmationTokensOfType($type)
    {
        ConfirmationToken::where('account_id', '=', $this->getId())
            ->where('type', '=', $type)
            ->delete();
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