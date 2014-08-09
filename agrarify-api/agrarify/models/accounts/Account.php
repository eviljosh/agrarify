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
        // 'title' => 'required'
    ];

    // Don't forget to fill this array
    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * @param string $password_text
     * @return bool
     */
    public function isPasswordValid($password_text)
    {
        return Hash::check($password_text, $this->password);
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