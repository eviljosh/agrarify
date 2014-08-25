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
     * @return array
     */
    public function transform($profile)
    {
        return [
            'profile_slug'        => $this->getValueOrDefault($profile, 'profile_slug'),
            'display_name'        => $profile->getDisplayName(),
            'image_url' => 'not yet implemented',
            'bio'     => $this->getValueOrDefault($profile, 'bio'),
            'favorite_crop' => $this->getValueOrDefault($profile, 'favorite_crop'),
            'home_location' => $profile->getHomeLocationString(),
            'member_since' => $profile->getAccount()->created_at,
        ];
    }

}