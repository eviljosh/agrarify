<?php

namespace Agrarify\Models\Veggies;

use Agrarify\Models\BaseModel;

class VeggieAvailability extends BaseModel {

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
    protected $table = 'veggie_availabilities';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'type'              => 'required|numeric|between:1,5',
        'availability_date' => 'required|date',
        'start_hour'        => 'numeric|between:0,23',
        'end_hour'          => 'numeric|between:0,23',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'availability_date',
        'start_hour',
        'end_hour',
    ];

    /**
     * Defines the many-to-one relationship with veggies
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function veggie()
    {
        return $this->belongsTo('Agrarify\Models\Veggies\Veggie');
    }

    /**
     * @return \Agrarify\Models\Veggies\Veggie
     */
    public function getVeggie()
    {
        return $this->veggie;
    }

    /**
     * @param \Agrarify\Models\Veggies\Veggie $veggie
     */
    public function setVeggie($veggie)
    {
        $this->veggie_id = $veggie->getId();
    }

    /**
     * @return mixed The database id for the record
     */
    public function getId()
    {
        return $this->getParamOrDefault('id');
    }

    /**
     * @return int Type code
     */
    public function getType()
    {
        return $this->getParamOrDefault('type');
    }

    /**
     * @return string Availability date
     */
    public function getAvailabilityDate()
    {
        return $this->getParamOrDefault('availability_date');
    }

    /**
     * @return int Start hour
     */
    public function getStartHour()
    {
        return $this->getParamOrDefault('start_hour');
    }

    /**
     * @return int End hour
     */
    public function getEndHour()
    {
        return $this->getParamOrDefault('end_hour');
    }

}