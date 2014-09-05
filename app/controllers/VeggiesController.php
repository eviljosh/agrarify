<?php

use Agrarify\Models\Subresources\Availability;
use Agrarify\Models\Subresources\Location;
use Agrarify\Models\Veggies\Veggie;
use Agrarify\Transformers\VeggieTransformer;
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
        $options = [
            [1, 'Apples', 'apple', 'apples'],
            [2, 'Asparagus', 'asparagus', 'bunches'],
            [3, 'Artichokes', 'artichoke', 'artichokes'],
            [4, 'Avocados', 'avocado', 'avocados'],
            [5, 'Broccoli', 'broccoli', 'heads'],
            [6,  'Beans (Green)', 'bean', 'handfulls'],
            [7, 'Beans (Dry)', 'bean', 'handfulls'],
            [8, 'Bok Choy', 'bok choy', 'heads'],
            [9, 'Basil', 'basil', 'bunches'],
            [10, 'Beets', 'beet', 'beets'],
            [11, 'Cilantro', 'cilantro', 'bunches'],
            [12, 'Cabbage', 'cabbage', 'heads'],
            [13, 'Cucumbers', 'cucumber', 'cucumbers'],
            [14, 'Carrots', 'carrot', 'bunches'],
            [15, 'Cauliflower', 'cauliflower', 'heads'],
            [16, 'Celery', 'celery', 'heads'],
            [17, 'Cherries', 'cherry', 'handfulls'],
            [18, 'Eggplant', 'eggplant', 'eggplants'],
            [19, 'Garlic', 'garlic', 'heads'],
            [20, 'Kale', 'kale', 'heads'],
            [21, 'Lettuce', 'lettuce', 'heads'],
            [22, 'Lemons', 'lemon', 'lemons'],
            [23, 'Limes', 'lime', 'limes'],
            [24, 'Oranges', 'orange', 'oranges'],
            [25, 'Radishes', 'radish', 'bunches'],
            [26, 'Spinach', 'spinach', 'bunches'],
            [27, 'Chard', 'chard', 'bunches'],
            [28, 'Turnips', 'turnip', 'bunches'],
            [29, 'Pumpkins', 'pumpkin', 'pumpkins'],
            [30, 'Squashes', 'squash', 'squashes'],
            [31, 'Peppers (Bell)', 'pepper', 'peppers'],
            [32, 'Peppers (Hot)', 'pepper', 'peppers'],
            [33, 'Tomatoes', 'tomato', 'tomatoes'],
            [34, 'Onions', 'onion', 'onions'],
            [35, 'Leeks', 'leek', 'bunches'],
            [36, 'Plums', 'plum', 'plums'],
            [37, 'Grapes', 'grape', 'bunches'],
            [38, 'Potatoes', 'potato', 'potatoes'],
            [39, 'Potatoes (Sweet)', 'potato', 'potatoes'],
        ];

        $veggie_options = [];
        foreach ($options as $option)
        {
            $veggie_options[] = [
                'id' => $option[0],
                'name' => $option[1],
                'singular_name' => $option[2],
                'collective_noun' => $option[3],
            ];
        }

        return Response::json(['items' => $veggie_options]);
    }


    /**
     * Display all veggies created by this account in the recent past.
	 *
	 * @return Response
	 */
	public function index()
	{
        return $this->sendSuccessResponse(
            Veggie::fetchByAccountForDaysPast($this->getAccount()),
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

        // Handle availability
        if (isset($payload['availability']))
        {
            $availability_payload = $payload['availability'];
            $availability = new Availability($availability_payload);
            $this->assertValid($availability);
            $availability->save();
            $veggie->setAvailability($availability);
        }

        // Set defaults
        if (!$veggie->getStatus())
        {
            $veggie->setStatus(Veggie::STATUS_AVAILABLE);
        }
        if (!$veggie->getNotes())
        {
            $veggie->setNotes('Looking for a good home!');
        }

        // Validate and save
        $this->assertValid($veggie);
        $veggie->save();
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
            if (isset($payload['availability']))
            {
                $availability_payload = $payload['availability'];

                $availability = $veggie->getAvailability();
                if ($availability)
                {
                    $availability->fill($availability_payload);
                }
                else
                {
                    $availability = new Availability($availability_payload);
                }

                $this->assertValid($availability);
                $availability->save();
                $veggie->setAvailability($availability);
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
        $geohash_substrings = [];
        for ($n = 12; $n > 3; $n--)
        {
            $geohash_substring = substr($geohash, 0, $n) . '%';
            $geohash_substrings[] = $geohash_substring;

            $veggies_query = Veggie::whereHas('location', function($q) use ($geohash_substring)
                {
                    $q->where('geohash', 'like', $geohash_substring);
                });
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
            'substrings'       => $geohash_substrings,
            'search_type'      => $type,
        ];

        return $this->sendSuccessResponse($results, [], $metadata);
    }

}
