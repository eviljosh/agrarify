<?php

namespace Agrarify\Transformers;

class AccountProfileTransformer extends AgrarifyTransformer
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->plural_name = 'account_profiles';
    }

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\Accounts\AccountProfile $profile
     * @param array $options
     * @return array
     */
    public function transform($profile, $options = [])
    {
        $json_array = [
            'profile_slug'    => $this->getValueOrDefault($profile, 'profile_slug'),
            'display_name'    => $profile->getDisplayName(),
            'image_url'       => 'not yet implemented',
            'bio'             => $this->getValueOrDefault($profile, 'bio'),
            'favorite_veggie' => $this->getValueOrDefault($profile, 'favorite_veggie'),
            'home_location'   => $profile->getHomeLocationString(),
            'member_since'    => $profile->getAccount()->getMemberSince(),
        ];

        if (isset($options['resource_owner']) and $options['resource_owner'])
        {
            $json_array = array_merge($json_array, [
                'is_interested_in_getting_veggies'   => (boolean) $profile->is_interested_in_getting_veggies,
                'is_interested_in_giving_veggies'    => (boolean) $profile->is_interested_in_giving_veggies,
                'is_interested_in_gardening'         => (boolean) $profile->is_interested_in_gardening,
                'is_interested_in_providing_gardens' => (boolean) $profile->is_interested_in_providing_gardens,
            ]);
        }

        return $json_array;
    }

}