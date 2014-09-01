<?php

namespace Agrarify\Models\Veggies;

use Agrarify\Models\BaseModel;
use Agrarify\Models\Accounts\Account;
use Agrarify\Models\Subresources\Location;

class Veggie extends BaseModel {

    const STATUS_AVAILABLE = 1;
    const STATUS_CLAIMED = 2;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'veggies';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'account_id'      => 'required|numeric',
        'location_id'     => 'required|numeric',
        'availability_id' => 'numeric',
        'status'          => 'required|numeric',
        'type'            => 'required|numeric',
        'freshness'       => 'required|numeric',
        'quantity'        => 'required|numeric',
        'notes'           => 'required'
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'type',
        'freshness',
        'quantity',
        'notes',
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
     * @return \Agrarify\Models\Subresources\Location
     */
    public function getLocation()
    {
        return Location::find($this->location_id);
    }

    /**
     * @return \Agrarify\Models\Subresources\Availability
     */
    public function getAvailability()
    {
        if (isset($this->availability_id))
        {
            return Location::find($this->availability_id);
        }
        return null;
    }

    /**
     * @return int Status code
     */
    public function getStatus()
    {
        return $this->getParamOrDefault('status');
    }

    /**
     * @return int Type code
     */
    public function getType()
    {
        return $this->getParamOrDefault('type');
    }

    /**
     * @return int Freshness metric
     */
    public function getFreshness()
    {
        return $this->getParamOrDefault('freshness');
    }

    /**
     * @return int Quantity
     */
    public function getQuantity()
    {
        return $this->getParamOrDefault('quantity');
    }

    /**
     * @return string Notes
     */
    public function getNotes()
    {
        return $this->getParamOrDefault('notes');
    }

}