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
        'city'        => 'max:50',
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
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'is_primary',
    ];

    /**
     * Set geohash before saving.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array())
    {
        // TODO: use eloquent's ->getDirty() and ->getOriginal to only calculate if necessary.  See http://stackoverflow.com/questions/21266030/laravel4-track-changes-to-be-saved-by-eloquent
        $this->calculateGeohash();   // TODO: ASYNC -- make this an async task!
        return parent::save($options);
    }

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
     * @return string Nickname
     */
    public function getNickname()
    {
        return $this->getParamOrDefault('nickname');
    }

    /**
     * @return string Number
     */
    public function getNumber()
    {
        return $this->getParamOrDefault('number');
    }

    /**
     * @return string City
     */
    public function getCity()
    {
        return $this->getParamOrDefault('city');
    }

    /**
     * @return string Street
     */
    public function getStreet()
    {
        return $this->getParamOrDefault('street');
    }

    /**
     * @return string State
     */
    public function getState()
    {
        return $this->getParamOrDefault('state');
    }

    /**
     * @return string Postal code
     */
    public function getPostalCode()
    {
        return $this->getParamOrDefault('postal_code');
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
     * @return \League\Geotools\Coordinate\Coordinate|null
     */
    public function getCoordinate()
    {
        if ($this->getLatitude() and $this->getLongitude())
        {
            return new Coordinate($this->getLatitude() . ', ' . $this->getLongitude());
        }
        return null;
    }

    /**
     * Sets geohash if longitude and latitude are present
     */
    public function calculateGeohash()
    {
        $coord = $this->getCoordinate();
        if ($coord)
        {
            $geotool = new Geotools();
            $encoded = $geotool->geohash()->encode($coord);
            $hash = $encoded->getGeohash();
            $this->geohash = $hash;
        }
    }

}