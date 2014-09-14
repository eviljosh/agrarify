<?php

namespace Agrarify\Models\Subresources;

use Agrarify\Models\BaseModel;

class ConfirmationToken extends BaseModel {

    const TYPE_EMAIL_VERIFICATION = 'E';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'confirmation_tokens';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'token' => 'required|max:40',
        'type'  => 'required|max:1',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Create a new ConfirmationToken model instance.
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
     * @return int Id
     */
    public function getId()
    {
        return $this->getParamOrDefault('id');
    }

    /**
     * @param \Agrarify\Models\Accounts\Account $account
     */
    public function setAccount($account)
    {
        $this->account_id = $account->getId();
    }

    /**
     * @return \Agrarify\Models\Accounts\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return string Token string
     */
    public function getToken()
    {
        return $this->getParamOrDefault('token');
    }

    /**
     * @return string Type char
     */
    public function getType()
    {
        return $this->getParamOrDefault('type');
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param $token_string
     * @return ConfirmationToken
     */
    public static function fetchByToken($token_string)
    {
        return self::firstByAttributes([
            'token' => $token_string,
        ]);
    }

}