<?php

namespace Agrarify\Transformers;

class VeggieImageTransformer extends AgrarifyTransformer
{
    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Veggies\VeggieImage $veggie_image
     * @param array $options
     * @return array
     */
    public function transform($veggie_image, $options = [])
    {
        return [
            'id'         => $veggie_image->getId(),
            'is_primary' => $veggie_image->isPrimary(),
            'url'        => $veggie_image->getDownloadUrl(),
            'created_at' => $veggie_image->getCreatedAt()->toDateTimeString(),
        ];
    }

}