<?php

namespace Agrarify\Transformers;

class VeggieTransformer extends AgrarifyTransformer
{
    const OPTIONS_SHOULD_SEE_DETAILS = 'full_details';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->availability_transformer = new VeggieAvailabilityTransformer();
        $this->image_transformer = new VeggieImageTransformer();
        $this->location_transformer = new LocationTransformer();
        $this->profile_transformer = new AccountProfileTransformer();
    }

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Veggies\Veggie $veggie
     * @param array $options
     * @return array
     */
    public function transform($veggie, $options = [])
    {
        $location_array = [];
        if ($this->getOption($options, self::OPTIONS_SHOULD_SEE_DETAILS))
        {
            $location_array = $this->location_transformer->transform($veggie->getLocation());
        }
        else
        {
            $location_array = $this->location_transformer->transform(
                $veggie->getLocation(),
                [LocationTransformer::OPTIONS_ROUGH_ONLY => true]
            );
        }

        $availabilities = $veggie->getAvailabilities();

        $json_array = [
            'id'              => $veggie->getId(),
            'status'          => $veggie->getStatus(),
            'type'            => $veggie->getType(),
            'freshness'       => $veggie->getFreshness(),
            'quantity'        => $veggie->getQuantity(),
            'notes'           => $veggie->getNotes(),
            'created_at'      => $veggie->getCreatedAt()->toDateTimeString(),
            'images'          => $this->image_transformer->transformCollection($veggie->getImages()),
            'owner_profile'   => $this->profile_transformer->transform($veggie->getAccount()->getProfile()),
            'location'        => $location_array,
            'availabilities'  => $availabilities ? $this->availability_transformer->transformCollection($availabilities) : null,
        ];

        // TODO: remove this once temporary test search functionality is no longer in use
        if (isset($veggie->distance) and isset($veggie->direction))
        {
            $distance_params = [
                'distance'  => $veggie->distance,
                'direction' => $veggie->direction
            ];

            $json_array = array_merge($json_array, $distance_params);
        }

        return $json_array;
    }

}