<?php

namespace Agrarify\Models\Accounts;

class Email extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'emails';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        // 'title' => 'required'
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [];

}