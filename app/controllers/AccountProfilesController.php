<?php

use Agrarify\Models\Accounts\AccountProfile;
use Agrarify\Transformers\AccountProfileTransformer;
use Illuminate\Support\Facades\Response;

class AccountProfilesController extends ApiController {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transformer = new AccountProfileTransformer();
    }

	/**
     * Display the profile for the authenticated account.
	 *
	 * @return Response
	 */
	public function showForAccount()
	{
        return $this->sendSuccessResponse($this->getAccount()->getProfile(), ['resource_owner' => true]);
	}

	/**
     * Update the profile for the authenticated account.
	 *
	 * @return Response
	 */
	public function updateForAccount()
    {

        $payload = $this->assertRequestPayloadItem();
        $profile = $this->getAccount()->getProfile();

        // We validate slugs as unique, so we need to do a little dance to avoid trying to revalidate the existing one.
        $should_validate_slug = true;
        if (!isset($payload['profile_slug']) or strtolower($payload['profile_slug']) == strtolower($profile->getSlug()))
        {
            $should_validate_slug = false;
        }

        // Update the profile instance.
        $profile->fill($payload);

        // Validate the profile instance
        if ($should_validate_slug)
        {
            $this->assertValid($profile);
        }
        else
        {
            $current_slug = $profile->getSlug();
            $profile->setSlug(null);
            $this->assertValid($profile);
            $profile->setSlug($current_slug);
        }

        // Save and return the updated profile.
        $profile->save();
        return $this->sendSuccessResponse($profile, ['resource_owner' => true]);
	}

    /**
     * Display the public profile for the slug.
     *
     * @param string $slug
     * @return Response
     */
    public function show($slug)
    {
        $profile = AccountProfile::fetchBySlug($slug);
        if ($profile)
        {
            return $this->sendSuccessResponse($profile);
        }
        return $this->sendErrorNotFoundResponse();
    }

}
