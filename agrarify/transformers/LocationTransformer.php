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
            'nickname'    => $this->getValueOrDefault($location, 'nickname'),
            'number'      => $this->getValueOrDefault($location, 'number'),
            'street'      => $this->getValueOrDefault($location, 'street'),
            'city'        => $this->getValueOrDefault($location, 'city'),
            'state'       => $this->getValueOrDefault($location, 'state'),
            'postal_code' => $this->getValueOrDefault($location, 'postal_code'),
            'latitude'    => $this->getValueOrDefault($location, 'latitude'),
            'longitude'   => $this->getValueOrDefault($location, 'longitude'),
            'is_primary'  => $location->isPrimary(),
        ];
    }

}