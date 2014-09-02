<?php

namespace Agrarify\Models\Subresources;

use Agrarify\Models\BaseModel;
use Carbon\Carbon;

class Message extends BaseModel {

    const TYPE_VEGGIE_MESSAGE = 1;
    const TYPE_VEGGIE_OFFER = 2;
    const TYPE_VEGGIE_OFFER_ACCEPTANCE = 3;

    /**
     * @return array Of veggie message type codes.
     */
    public static function getVeggieMessageTypes()
    {
        return [
            self::TYPE_VEGGIE_MESSAGE,
            self::TYPE_VEGGIE_OFFER,
            self::TYPE_VEGGIE_OFFER_ACCEPTANCE,
        ];
    }

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
        'message',
    ];

    /**
     * Defines the many-to-one relationship with sender accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Agrarify\Models\Accounts\Account');
    }

    /**
     * Defines the many-to-one relationship with recipient accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo('Agrarify\Models\Accounts\Account', 'recipient_id');
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
        return $this->recipient;
    }

    /**
     * @param \Agrarify\Models\Accounts\Account $account
     */
    public function setRecipientAccount($account)
    {
        $this->recipient_id = $account->getId();
    }

    /**
     * @return int Type code
     */
    public function getType()
    {
        return $this->getParamOrDefault('type');
    }

    /**
     * @return mixed The id of the associated other resource (e.g. veggie, garden, etc.)
     */
    public function getOtherId()
    {
        return $this->getParamOrDefault('other_id');
    }

    /**
     * @param mixed $id The id of the associated other resource (e.g. veggie, garden, etc.)
     */
    public function setOtherId($id)
    {
        $this->other_id = $id;
    }

    /**
     * @return string Message text
     */
    public function getMessage()
    {
        return $this->getParamOrDefault('message');
    }

    /**
     * @return \Carbon\Carbon Created at date
     */
    public function getCreatedAt()
    {
        return $this->getParamOrDefault('created_at');
    }

    /**
     * @return bool Indication of whether this message relates to a veggie or not.
     */
    public function isVeggieMessage()
    {
        return in_array($this->getType(), self::getVeggieMessageTypes());
    }

    /**
     * Fetches a collection of all veggie Messages for the given account created within the past x days.
     *
     * @param \Agrarify\Models\Accounts\Account $account
     * @param int $days_past
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function fetchVeggieMessagesByAccountForDaysPast($account, $days_past = 30)
    {
        $account_id = $account->getId();
        return self::where(function ($query) use ($account_id) {
                $query->where('account_id', '=', $account_id)
                    ->orWhere('recipient_id', '=', $account_id);
            })
            ->whereIn('type', Message::getVeggieMessageTypes())
            ->where('created_at', '>=', Carbon::now()->subDays($days_past)->toDateString())
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Fetches a collection of all Messages for the given account and the given Veggie.
     *
     * @param \Agrarify\Models\Accounts\Account $account
     * @param \Agrarify\Models\Veggies\Veggie $veggie
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function fetchMessagesForVeggieAndAccount($account, $veggie)
    {
        $account_id = $account->getId();
        return self::where(function ($query) use ($account_id) {
                $query->where('account_id', '=', $account_id)
                    ->orWhere('recipient_id', '=', $account_id);
            })
            ->whereIn('type', Message::getVeggieMessageTypes())
            ->where('other_id', '=', $veggie->getId())
            ->orderBy('created_at', 'DESC')
            ->get();
    }

}