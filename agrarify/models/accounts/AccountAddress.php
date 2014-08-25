<?php

namespace Agrarify\Models\Accounts;

use Agrarify\Models\BaseModel;

class AccountAddress extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'account_addresses';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'is_primary' => 'boolean',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'is_primary',
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
     * Defines the many-to-one relationship with locations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('Agrarify\Models\Subresources\Location');
    }

}