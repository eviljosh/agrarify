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
        $this->plural_name = 'veggies';

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
            'id'    => $veggie->getId(),
            'status'    => $veggie->getStatus(),
            'type'       => $veggie->getType(),
            'freshness'  => $veggie->getFreshness(),
            'quantity' => $veggie->getQuantity(),
            'notes'   => $veggie->getNotes(),
            'images'    => 'not yet implemented',
            'owner_profile' => $this->profile_transformer->transform($veggie->getAccount()->getProfile()),
            'location' => $location_array,
            'availability' => $availability ? $this->availability_transformer->transform($availability) : null,
        ];

        return $json_array;
    }

}