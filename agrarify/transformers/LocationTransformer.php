<?php

namespace Agrarify\Transformers;

class LocationTransformer extends AgrarifyTransformer
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plural_name = 'locations';
    }

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Subresources\Location $location
     * @param array $options
     * @return array
     */
    public function transform($location, $options = [])
    {
        return [
            'id'          => $location->getId(),
            'nickname'    => $location->getNickname(),
            'number'      => $location->getNumber(),
            'street'      => $location->getStreet(),
            'city'        => $location->getCity(),
            'state'       => $location->getState(),
            'postal_code' => $location->getPostalCode(),
            'latitude'    => $location->getLatitude(),
            'longitude'   => $location->getLongitude(),
            'is_primary'  => $location->isPrimary(),
        ];
    }

}