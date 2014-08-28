<?php

namespace Agrarify\Models\Subresources;

use Agrarify\Models\BaseModel;
use League\Geotools\Geotools;
use League\Geotools\Coordinate\Coordinate;

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
        'is_primary'  => 'boolean',
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
        'is_primary',
    ];

    /**
     * @return int Id
     */
    public function getId()
    {
        return $this->getParamOrDefault('id');
    }

    /**
     * @param \Agrarify\Models\Accounts\Account $account
     */
    public function setAccount($account)
    {
        $this->account_id = $account->getId();
    }

    /**
     * @return string City
     */
    public function getCity()
    {
        return $this->getParamOrDefault('city');
    }

    /**
     * @return string State
     */
    public function getState()
    {
        return $this->getParamOrDefault('state');
    }

    /**
     * @return string Longitude
     */
    public function getLongitude()
    {
        return $this->getParamOrDefault('longitude');
    }

    /**
     * @return string Latitude
     */
    public function getLatitude()
    {
        return $this->getParamOrDefault('latitude');
    }

    /**
     * @return boolean Indication of whether this is the primary location
     */
    public function isPrimary()
    {
        return (boolean) $this->getParamOrDefault('is_primary');
    }

    /**
     * Sets geohash if longitude and latitude are present
     */
    public function calculateGeohash()
    {
        if ($this->getLatitude() and $this->getLongitude())
        {
            $coord = new Coordinate($this->getLatitude() . ', ' . $this->getLongitude());
            $geotool = new Geotools();
            $encoded = $geotool->geohash()->encode($coord);
            $hash = $encoded->getGeohash();
            $this->geohash = $hash;
        }
    }

}