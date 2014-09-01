<?php

namespace Agrarify\Models\Subresources;

use Agrarify\Models\BaseModel;
use Agrarify\Models\Accounts\Account;

class Message extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agrarify_messages';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'account_id'   => 'required|numeric',
        'recipient_id' => 'required|numeric',
        'type'         => 'required|numeric',
        'other_id'     => 'numeric',
        'message'      => '',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'other_id',
        'message',
    ];

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
     * @return \Agrarify\Models\Accounts\Account
     */
    public function getRecipientAccount()
    {
        return Account::find($this->recipient_id);
    }

    /**
     * @param \Agrarify\Models\Accounts\Account $account
     */
    public function setRecipientAccount($account)
    {
        $this->recipient_id = $account->getId();
    }



}