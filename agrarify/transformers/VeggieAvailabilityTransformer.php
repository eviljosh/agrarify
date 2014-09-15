<?php

namespace Agrarify\Transformers;

class VeggieAvailabilityTransformer extends AgrarifyTransformer
{

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Veggies\VeggieAvailability $availability
     * @param array $options
     * @return array
     */
    public function transform($availability, $options = [])
    {
        return [
            'type'              => $availability->getType(),
            'availability_date' => $availability->getAvailabilityDate(),
            'start_hour'        => $availability->getStartHour(),
            'end_hour'          => $availability->getEndHour(),
        ];
    }

}