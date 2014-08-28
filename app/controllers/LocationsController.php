<?php

use Agrarify\Models\Accounts\Account;
use Agrarify\Models\Subresources\Location;
use Agrarify\Transformers\LocationTransformer;
use Illuminate\Support\Facades\Response;

class LocationsController extends ApiController {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transformer = new LocationTransformer();
    }

	/**
     * Display all locations associated with this account.
	 *
	 * @return Response
	 */
	public function listLocations()
	{
        return $this->sendSuccessResponse($this->getAccount()->getLocations());
	}

    /**
     * Display a location by id.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $location = $this->getAccount()->getLocationById($id);
        if ($location)
        {
            return $this->sendSuccessResponse($location);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Create a location associated with the authenticated account.
     *
     * @return Response
     */
    public function create()
    {
        $payload = $this->assertRequestPayloadItem();
        $location = new Location($payload);
        $location->setAccount($this->getAccount());
        $location->calculateGeohash();
        $this->assertValid($location);
        $location->save();
        return $this->sendSuccessResponse($location);
    }

    /**
     * Update a location by id.
     *
     * @param $id
     * @return Response
     */
    public function update($id)
    {
        $payload = $this->assertRequestPayloadItem();

        $location = $this->getAccount()->getLocationById($id);
        if ($location)
        {
            $location->fill($payload);
            $location->calculateGeohash();
            $this->assertValid($location);
            return $this->sendSuccessResponse($location);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Delete a location by id.
     *
     * @param $id
     * @return Response
     */
    public function deleteLocation($id)
    {
        $location = $this->getAccount()->getLocationById($id);
        if ($location)
        {
            $location->delete();
            return $this->sendSuccessNoContentResponse();
        }
        return $this->sendErrorNotFoundResponse();
    }

}
