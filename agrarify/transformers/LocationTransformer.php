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
        $json_array = [
            'city'        => $location->getCity(),
            'state'       => $location->getState(),
        ];

        if (!isset($options['rough_only']) or !$options['rough_only'])
        {
            $json_array = array_merge($json_array, [
                'number'      => $location->getNumber(),
                'street'      => $location->getStreet(),
                'postal_code' => $location->getPostalCode(),
                'latitude'    => $location->getLatitude(),
                'longitude'   => $location->getLongitude(),
            ]);
        }

        if (isset($options['resource_owner']) and $options['resource_owner'])
        {
            $json_array = array_merge($json_array, [
                'id'          => $location->getId(),
                'nickname'    => $location->getNickname(),
                'is_primary'  => $location->isPrimary(),
            ]);
        }

        return $json_array;
    }

}