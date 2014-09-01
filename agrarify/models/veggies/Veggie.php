<?php

namespace Agrarify\Models\Veggies;

use Agrarify\Models\BaseModel;
use Agrarify\Models\Accounts\Account;
use Agrarify\Models\Subresources\Availability;
use Agrarify\Models\Subresources\Location;
use Carbon\Carbon;

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
     * @param \Agrarify\Models\Subresources\Location $location
     */
    public function setLocation($location)
    {
        $this->location_id = $location->getId();
    }

    /**
     * @return \Agrarify\Models\Subresources\Availability
     */
    public function getAvailability()
    {
        if (isset($this->availability_id))
        {
            return Availability::find($this->availability_id);
        }
        return null;
    }

    /**
     * @param \Agrarify\Models\Subresources\Availability $availability
     */
    public function setAvailability($availability)
    {
        $this->availability_id = $availability->getId();
    }

    /**
     * @return int Status code
     */
    public function getStatus()
    {
        return $this->getParamOrDefault('status');
    }

    /**
     * @param int $status_code
     */
    public function setStatus($status_code)
    {
        $this->status = $status_code;
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

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * Determines whether account should have access to veggie details.  E.g. account is owner or account has been
     * granted pickup rights.
     *
     * @param \Agrarify\Models\Accounts\Account $account
     * @return bool Indication of whether account should have access to veggie details
     */
    public function shouldAccountSeeDetails($account)
    {
        return true; //TODO implement
    }

    /**
     * Fetches a collection of all Veggies for the given account created within the past x days.
     *
     * @param \Agrarify\Models\Accounts\Account $account
     * @param int $days_past
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function fetchByAccountForDaysPast($account, $days_past = 30)
    {
        return self::where('account_id', '=', $account->getId())
            ->where('created_at', '>=', Carbon::now()->subDays($days_past)->toDateString())
            ->orderBy('created_at', 'DESC')
            ->get();
    }

}