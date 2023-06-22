<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;

class TravelController extends Controller
{
    /**
     * @group Public endpoints
     *
     * GET Travels
     *
     * Returns paginated list of travels.
     *
     * @queryParam page integer Page number. Example: 1
     *
     * @response {"data":[{"id":"9958e389-5edf-48eb-8ecd-e058985cf3ce","name":"First travel", ...}}
     */
    public function index()
    {
        //
        $travels = Travel::where('is_public', true)->paginate(10);

        return TravelResource::collection($travels);
    }

    /**
     * @group Admin endpoints
     *
     * POST Travel
     *
     * Creates a new Travel record.
     *
     * @authenticated
     *
     * @response {"data":{"id":"996a36ca-2693-4901-9c55-7136e68d81d5","name":"My new travel 234","slug":"my-new-travel-234", ...}
     * @response 422 {"message":"The name has already been taken.","errors":{"name":["The name has already been taken."]}}
     */
    public function store(TravelRequest $request)
    {
        //
        $data = $request->validated();

        return new TravelResource(Travel::create($data));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * @group Admin endpoints
     *
     * PUT Travel
     *
     * Update specified travel resource in storage.
     *
     * @authenticated
     *
     * @response {"data":{"id":"996a36ca-2693-4901-9c55-7136e68d81d5","name":"My new travel 234", ...}
     * @response 422 {"message":"The name has already been taken.","errors":{"name":["The name has already been taken."]}}
     */
    public function update(Travel $travel, TravelRequest $request)
    {
        $travel->update($request->validated());

        return new TravelResource($travel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
