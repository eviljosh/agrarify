<?php

use Agrarify\Models\Subresources\Location;
use Agrarify\Models\Veggies\Veggie;
use Agrarify\Models\Veggies\VeggieAvailability;
use Agrarify\Models\Veggies\VeggieOptions;
use Agrarify\Transformers\VeggieTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

// TODO: remove once have search controller
use League\Geotools\Geotools;
use League\Geotools\Coordinate\Coordinate;

class VeggiesController extends ApiController {

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transformer = new VeggieTransformer();
    }

    /**
     * Display a list of available veggies for the app.
     *
     * @return Response
     */
    public function listOptions()
    {
        $options = VeggieOptions::getOptions();

        $veggie_options = [];
        foreach ($options as $option)
        {
            $veggie_options[] = [
                'id' => $option[0],
                'name' => $option[1],
                'singular_name' => $option[2],
                'collective_noun' => $option[3],
                'image_name' => $option[4],
            ];
        }

        return Response::json(['items' => $veggie_options, 'metadata' => VeggieOptions::getMetadata()]);
    }


    /**
     * Display all veggies created by this account in the recent past.
	 *
	 * @return Response
	 */
	public function index()
	{
        return $this->sendSuccessResponse(
            Veggie::fetchByAccountForDaysPast($this->getAccount(), 10),
            [VeggieTransformer::OPTIONS_SHOULD_SEE_DETAILS => true]
        );
	}

    /**
     * Display a veggie by id.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        /**
         * @var $veggie \Agrarify\Models\Veggies\Veggie
         */
        $veggie = Veggie::find($id);

        if ($veggie)
        {
            $options = [];
            if ($veggie->shouldAccountSeeDetails($this->getAccount()))
            {
                $options = [VeggieTransformer::OPTIONS_SHOULD_SEE_DETAILS => true];
            }

            return $this->sendSuccessResponse($veggie, $options);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Create a veggie associated with this account.
     *
     * @return Response
     */
    public function store()
    {
        $payload = $this->assertRequestPayloadItem();
        $veggie = new Veggie($payload);
        $veggie->setAccount($this->getAccount());

        // Handle the location
        if (isset($payload['location']))
        {
            $location_payload = $payload['location'];

            if (isset($location_payload['id']))
            {
                if ($location = $this->getAccount()->getLocationById($location_payload['id']))
                {
                    $veggie->setLocation($location);
                }
                else
                {
                    return $this->sendErrorResponse(['message' => 'Location with given id does not exist']);
                }
            }
            else
            {
                $location = new Location($location_payload);
                $location->setAccount($this->getAccount());
                $this->assertValid($location);
                $location->save();
                $veggie->setLocation($location);
            }
        }
        else
        {
            return $this->sendErrorResponse(['message' => 'Location is required']);
        }

        // Validate and save
        $this->assertValid($veggie);
        $veggie->save();

        // Handle availability
        if (isset($payload['availabilities']))
        {
            foreach ($payload['availabilities'] as $availability_payload)
            {
                $availability = new VeggieAvailability($availability_payload);
                $availability->setVeggie($veggie);
                $this->assertValid($availability);
                $availability->save();
            }
        }

        return $this->sendSuccessResponseCreated($veggie, [VeggieTransformer::OPTIONS_SHOULD_SEE_DETAILS => true]);
    }

    /**
     * Update a veggie by id.
     *
     * @param $id
     * @return Response
     */
    public function update($id)
    {
        $payload = $this->assertRequestPayloadItem();

        $veggie = $this->getAccount()->getVeggieById($id);
        if ($veggie)
        {
            $veggie->fill($payload);

            // Handle the location
            if (isset($payload['location']))
            {
                $location_payload = $payload['location'];

                if (isset($location_payload['id']))
                {
                    if ($location_payload['id'] != $veggie->getLocation()->getId())
                    {
                        if ($location = $this->getAccount()->getLocationById($location_payload['id']))
                        {
                            $veggie->setLocation($location);
                        }
                        else
                        {
                            return $this->sendErrorResponse(['message' => 'Location with given id does not exist']);
                        }
                    }
                    else
                    {
                        $location = $veggie->getLocation();
                        $location->fill($location_payload);
                        $this->assertValid($location);
                        $location->save();
                    }
                }
                else
                {
                    $location = new Location($location_payload);
                    $location->setAccount($this->getAccount());
                    $this->assertValid($location);
                    $location->save();
                    $veggie->setLocation($location);
                }
            }

            // Handle availability
            if (isset($payload['availabilities']) and !empty($payload['availabilities']))
            {
                $veggie->deleteAvailabilities();
                foreach ($payload['availabilities'] as $availability_payload)
                {
                    $availability = new VeggieAvailability($availability_payload);
                    $availability->setVeggie($veggie);
                    $this->assertValid($availability);
                    $availability->save();
                }
            }

            // Validate and save
            $this->assertValid($veggie);
            $veggie->save();
            return $this->sendSuccessResponse($veggie, [VeggieTransformer::OPTIONS_SHOULD_SEE_DETAILS => true]);
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Delete a veggie by id.
     *
     * @param $id
     * @return Response
     */
    public function destroy($id)
    {
        $veggie = $this->getAccount()->getVeggieById($id);
        if ($veggie)
        {
            $veggie->delete();
            return $this->sendSuccessNoContentResponse();
        }
        return $this->sendErrorNotFoundResponse();
    }

    /**
     * Temporary "fake" veggie search (pre-ElasticSearch)
     *
     * @return Response
     */
    public function testSearch()
    {
        $lat = Input::get('latitude', '37.77492950');
        $lon = Input::get('longitude', '-122.4194155');
        $type = Input::get('type', null);

        $coord = new Coordinate($lat . ', ' . $lon);
        $geotool = new Geotools();
        $encoded = $geotool->geohash()->encode($coord);
        $geohash = $encoded->getGeohash();

        $results = [];
        $ids_seen = [];
        for ($n = 11; $n > 3; $n--)
        {
            $geohash_substring = substr($geohash, 0, $n) . '%';

            $veggies_query = Veggie::whereHas('location', function($q) use ($geohash_substring)
                {
                    $q->where('geohash', 'like', $geohash_substring);
                })
                ->where('created_at', '>', Carbon::now()->subDays(7)->toDateTimeString())
                ->where('status', '=', Veggie::STATUS_AVAILABLE);
            if ($type)
            {
                if (is_array($type))
                {
                    $veggies_query = $veggies_query->whereIn('type', $type);
                }
                else
                {
                    $veggies_query = $veggies_query->where('type', '=', $type);
                }
            }
            $veggies = $veggies_query->get();

            foreach ($veggies as $veggie)
            {
                if (in_array($veggie->getId(), $ids_seen))
                {
                    continue;
                }

                $veggie_coord = $veggie->getLocation()->getCoordinate();
                $point = $geotool->point()->setFrom($coord)->setTo($veggie_coord);
                $veggie->direction = $point->initialCardinal();

                $distance = $geotool->distance()->setFrom($coord)->setTo($veggie_coord);
                $veggie->distance = $distance->in('mi')->haversine();

                $results[] = $veggie;
                $ids_seen[] = $veggie->getId();
            }

            if (count($results) > 10)
            {
                break;
            }
        }

        $metadata = [
            'search_latitude'  => $lat,
            'search_longitude' => $lon,
            'search_geohash'   => $geohash,
            'search_type'      => $type,
        ];

        return $this->sendSuccessResponse($results, [], $metadata);
    }

}
