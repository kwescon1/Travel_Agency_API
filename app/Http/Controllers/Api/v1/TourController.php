<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourRequest;
use App\Http\Requests\ToursListRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;

class TourController extends Controller
{
    /**
     * @group Public endpoints
     *
     * GET Travel Tours
     *
     * Returns paginated list of tours by travel slug.
     *
     * @urlParam travel_slug string Travel slug. Example: "first-travel"
     *
     * @bodyParam priceFrom number. Example: "123.45"
     * @bodyParam priceTo number. Example: "234.56"
     * @bodyParam dateFrom date. Example: "2023-06-01"
     * @bodyParam dateTo date. Example: "2023-07-01"
     * @bodyParam sortBy string. Example: "price"
     * @bodyParam sortOrder string. Example: "asc" or "desc"
     *
     * @response {"data":[{"id":"9958e389-5edf-48eb-8ecd-e058985cf3ce","name":"Tour on Sunday","starting_date":"2023-06-11","ending_date":"2023-06-16", ...}
     */
    public function index(Travel $travel, ToursListRequest $request)
    {

        $tours = $travel->tours()
            ->when($request->priceFrom, function ($query, $priceFrom) {
                $query->where('price', '>=', $priceFrom * 100);
            })
            ->when($request->priceTo, function ($query, $priceTo) {
                $query->where('price', '<=', $priceTo * 100);
            })
            ->when($request->dateFrom, function ($query, $dateFrom) {
                $query->where('start_date', '>=', $dateFrom);
            })
            ->when($request->dateTo, function ($query, $dateTo) {
                $query->where('start_date', '<=', $dateTo);
            })
            ->when($request->sortBy && $request->sortOrder, function ($query, $sortBy, $sortOrder) {
                $query->orderBy($sortBy,
                    $sortOrder);
            })
            ->orderBy('start_date')->paginate(10);

        // or

        // return Tour::where('travel_id',$travel->id)->orderBy('start_date')->get();

        return TourResource::collection($tours);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Travel $travel, TourRequest $request)
    {
        $tour = $travel->tours()->create($request->validated());

        return new TourResource($tour);
    }
}
