<?php

namespace Agrarify\Transformers;

class AvailabilityTransformer extends AgrarifyTransformer
{

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Subresources\Availability $availability
     * @param array $options
     * @return array
     */
    public function transform($availability, $options = [])
    {
        return [
            'type'       => $availability->getType(),
            'start_date' => $availability->getStartDate(),
            'end_date'   => $availability->getEndDate(),
            'start_hour' => $availability->getStartHour(),
            'end_hour'   => $availability->getEndHour(),
            'monday'     => $availability->isAvailableMonday(),
            'tuesday'    => $availability->isAvailableTuesday(),
            'wednesday'  => $availability->isAvailableWednesday(),
            'thursday'   => $availability->isAvailableThursday(),
            'friday'     => $availability->isAvailableFriday(),
            'saturday'   => $availability->isAvailableSaturday(),
            'sunday'     => $availability->isAvailableSunday(),
        ];
    }

}