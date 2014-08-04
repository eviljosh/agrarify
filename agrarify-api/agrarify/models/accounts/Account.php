<?php

namespace Agrarify\Models\Accounts;

class Account extends Eloquent {

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

}