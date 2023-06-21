<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToursListRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;

class TourController extends Controller
{
    //
    public function index(Travel $travel, ToursListRequest $request)
    {

        $tours = $travel->tours()
            ->when($request->priceFrom, function ($query) use ($request) {
                $query->where('price', '>=', $request->priceFrom * 100);
            })
            ->when($request->priceTo, function ($query) use ($request) {
                $query->where('price', '<=', $request->priceTo * 100);
            })
            ->when($request->dateFrom, function ($query) use ($request) {
                $query->where('start_date', '>=', $request->dateFrom);
            })
            ->when($request->dateTo, function ($query) use ($request) {
                $query->where('start_date', '<=', $request->dateTo);
            })
            ->when($request->sortBy && $request->sortOrder, function ($query) use ($request) {
                $query->orderBy($request->sortBy,
                    $request->sortOrder);
            })
            ->orderBy('start_date')->paginate(10);

        // or

        // return Tour::where('travel_id',$travel->id)->orderBy('start_date')->get();

        return TourResource::collection($tours);
    }
}
