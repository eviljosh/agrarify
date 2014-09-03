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
        $this->availability_transformer = new AvailabilityTransformer();
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

        $availability = $veggie->getAvailability();

        $json_array = [
            'id'            => $veggie->getId(),
            'status'        => $veggie->getStatus(),
            'type'          => $veggie->getType(),
            'freshness'     => $veggie->getFreshness(),
            'quantity'      => $veggie->getQuantity(),
            'notes'         => $veggie->getNotes(),
            'created_at'    => $veggie->getCreatedAt()->toDateTimeString(),
            'images'        => 'not yet implemented',
            'owner_profile' => $this->profile_transformer->transform($veggie->getAccount()->getProfile()),
            'location'      => $location_array,
            'availability'  => $availability ? $this->availability_transformer->transform($availability) : null,
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