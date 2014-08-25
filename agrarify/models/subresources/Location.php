<?php

namespace Agrarify\Models\Subresources;

use Agrarify\Models\BaseModel;

class Location extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'nick_name'   => 'max:100',
        'number'      => 'max:20',
        'street'      => 'max:50',
        'state'       => 'max:50',
        'postal_code' => 'max:15',
        'latitude'    => 'max:12',
        'longitude'   => 'max:12',
        'geohash'     => 'max:12',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'nick_name',
        'number',
        'street',
        'state',
        'postal_code',
        'latitude',
        'longitude',
    ];

}