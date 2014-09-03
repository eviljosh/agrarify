<?php

namespace Agrarify\Transformers;

class AccountProfileTransformer extends AgrarifyTransformer
{
    const OPTIONS_IS_RESOURCE_OWNER = 'resource_owner';
    const OPTIONS_SHOW_SHORT_PROFILE = 'show_short_profile';

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
            'profile_slug'    => $profile->getSlug(),
            'display_name'    => $profile->getDisplayName(),
        ];

        if (!$this->getOption($options, self::OPTIONS_SHOW_SHORT_PROFILE))
        {
            $json_array = array_merge($json_array, [
                'image_url'       => 'not yet implemented',
                'bio'             => $profile->getBio(),
                'favorite_veggie' => $profile->getFavoriteVeggie(),
                'home_location'   => $profile->getHomeLocationString(),
                'member_since'    => $profile->getAccount()->getMemberSince(),
            ]);
        }

        if ($this->getOption($options, self::OPTIONS_IS_RESOURCE_OWNER))
        {
            $json_array = array_merge($json_array, [
                'is_interested_in_getting_veggies'   => $profile->isInterestedInGettingVeggies(),
                'is_interested_in_giving_veggies'    => $profile->isInterestedInGivingVeggies(),
                'is_interested_in_gardening'         => $profile->isInterestedInGardening(),
                'is_interested_in_providing_gardens' => $profile->isInterestedInProvidingGardens(),
            ]);
        }

        return $json_array;
    }

}