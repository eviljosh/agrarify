<?php

namespace Agrarify\Models\Subresources;

use Agrarify\Models\BaseModel;

class Availability extends BaseModel {

    const TYPE_ALL_DAY = 1;
    const TYPE_MORNING = 2;
    const TYPE_AFTERNOON = 3;
    const TYPE_EVENING = 4;
    const TYPE_SPECIFIC_HOURS = 5;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'availabilities';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'type'       => 'required|numeric|between:1,5',
        'start_date' => 'date',
        'end_date'   => 'date',
        'start_hour' => 'numeric|between:0,23',
        'end_hour'   => 'numeric|between:0,23',
        'monday'     => 'boolean',
        'tuesday'    => 'boolean',
        'wednesday'  => 'boolean',
        'thursday'   => 'boolean',
        'friday'     => 'boolean',
        'saturday'   => 'boolean',
        'sunday'     => 'boolean',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'start_date',
        'end_date',
        'start_hour',
        'end_hour',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];

    /**
     * Create a new Availability model instance.
     *
     * @param array $attributes
     */
    function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        if (empty($this->start_date))
        {
            $this->start_date = date('Y-m-d', time());  // default start date to today
        }
    }

}