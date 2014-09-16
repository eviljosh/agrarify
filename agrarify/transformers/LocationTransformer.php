<?php

namespace Agrarify\Transformers;

class LocationTransformer extends AgrarifyTransformer
{
    const OPTIONS_IS_RESOURCE_OWNER = 'resource_owner';
    const OPTIONS_ROUGH_ONLY = 'rough_only';

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

        if (!$this->getOption($options, self::OPTIONS_ROUGH_ONLY))
        {
            $json_array = array_merge($json_array, [
                'number'      => $location->getNumber(),
                'street'      => $location->getStreet(),
                'postal_code' => $location->getPostalCode(),
                'latitude'    => $location->getLatitude(),
                'longitude'   => $location->getLongitude(),
                'geohash'     => $location->getGeohash(),
            ]);
        }

        if ($this->getOption($options, self::OPTIONS_IS_RESOURCE_OWNER))
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