<?php

namespace Agrarify\Transformers;

class PushRegistrationTransformer extends AgrarifyTransformer
{

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Accounts\PushRegistration $push_registration
     * @param array $options
     * @return array
     */
    public function transform($push_registration, $options = [])
    {
        return [
            'id'          => $push_registration->getId(),
            'type'        => $push_registration->getType(),
            'token'       => $push_registration->getToken(),
            'device_name' => $push_registration->getDeviceName(),
        ];
    }

}