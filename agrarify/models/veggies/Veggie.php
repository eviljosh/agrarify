<?php

namespace Agrarify\Models\Veggies;

use Agrarify\Models\BaseModel;
use Agrarify\Models\Subresources\Message;
use Agrarify\Models\Veggies\VeggieAvailability;
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
        'status'          => 'required|numeric',
        'type'            => 'required|numeric',
        'freshness'       => 'required|numeric',
        'quantity'        => 'required|numeric',
        'notes'           => ''
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
     * Create a new Veggie model instance.
     *
     * @param array $attributes
     */
    function __construct(array $attributes = array())
    {
        $this->setStatus(self::STATUS_AVAILABLE);
        parent::__construct($attributes);
    }

    /**
     * Re-index for searching after saving.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array())
    {
        $v = parent::save($options);

        // TODO: ASYNC -- spin off an async reindexing task for this id

        return $v;
    }

    /**
     * Delete message records prior to deleting the model from the database.
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $messages = $this->getMessages();
        foreach ($messages as $message)
        {
            $message->delete();  // TODO: adjust to be a single query  // TODO: ASYNC
        }

        return parent::delete();
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
     * Defines the many-to-one relationship with locations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('Agrarify\Models\Subresources\Location');
    }

    /**
     * Defines the one-to-many relationship with veggie availabilities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function availabilities()
    {
        return $this->hasMany('Agrarify\Models\Veggies\VeggieAvailability')->orderBy('availability_date', 'asc');
    }

    /**
     * Defines the one-to-many relationship with veggie images
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('Agrarify\Models\Veggies\VeggieImage');
    }

    /**
     * @return int Id
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
        return $this->location;
    }

    /**
     * @param \Agrarify\Models\Subresources\Location $location
     */
    public function setLocation($location)
    {
        $this->location_id = $location->getId();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailabilities()
    {
        return $this->availabilities;
    }

    /**
     * Deletes existing availabilities associated with this veggie
     */
    public function deleteAvailabilities()
    {
        VeggieAvailability::where('veggie_id', '=', $this->getId())->delete();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMessages()
    {
        return Message::where('other_id', '=', $this->getId())
            ->whereIn('type', Message::getVeggieMessageTypes())
            ->get();
    }

    /**
     * @return \Agrarify\Models\Subresources\Message
     */
    public function getAcceptanceMessage()
    {
        return Message::where('other_id', '=', $this->getId())
            ->where('type', '=', Message::TYPE_VEGGIE_OFFER_ACCEPTANCE)
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getImages()
    {
        return $this->images()->orderBy('is_primary', 'DESC')->orderBy('created_at', 'ASC')->get();
    }

    /**
     * @param $id
     * @return \Agrarify\Models\Veggies\VeggieImage
     */
    public function getImageById($id)
    {
        return $this->images()->where('id', '=', $id)->first();
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
        if ($this->account_id == $account->getId())
        {
            return true;
        }

        // disabling full location details for veggies that you do not own until we figure out security implications
//        $acceptance = $this->getAcceptanceMessage();
//        if ($acceptance and $acceptance->getRecipientAccount()->getId() == $account->getId())
//        {
//            return true;
//        }

        return false;
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