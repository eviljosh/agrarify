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
     * Defines the one-to-many relationship with oauth access tokens
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauth_access_tokens()
    {
        return $this->hasMany('Agrarify\Models\Oauth2\OauthAccessToken');
    }

    /**
     * Defines the one-to-one relationship with account profiles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account_profile()
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
     * @param $email_address
     * @return Account
     */
    public static function fetchByEmail($email_address)
    {
        return self::firstByAttributes(['email_address' => $email_address]);
    }

}