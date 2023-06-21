<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToursListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_tours_list_by_travel_slug_returns_correct_tours(): void
    {

        $travel = Travel::factory()->create();

        $tour = Tour::factory()->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }

    public function test_tour_prices_is_shown_correctly(): void
    {
        $travel = Travel::factory()->create();

        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 123.45]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => '123.45']);
    }

    public function test_tour_lists_returns_paginations(): void
    {
        // $toursPerPage = config('app.paginationPerPage.tours');

        $travel = Travel::factory()->create();

        Tour::factory(16)->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_tours_list_sorts_by_start_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(4),
        ]);

        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'start_date' => now(),
            'end_date' => now()->addDays(1),
        ]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
    }

    public function test_tours_list_sorts_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $cheaperLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(13),
        ]);

        $cheaperEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'start_date' => now(),
            'end_date' => now()->addDay(),
        ]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?sortBy=price&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheaperEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheaperLaterTour->id);
        $response->assertJsonPath('data.2.id', $expensiveTour->id);
    }

    public function test_tours_list_filters_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
        ]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?priceFrom=100');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?priceFrom=150');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?priceFrom=250');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?priceTo=200');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?priceTo=150');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonMissing(['id' => $expensiveTour->id]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?priceTo=50');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?priceFrom=150&priceTo=200');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

    }

    public function test_tours_list_filters_by_start_date_correctly(): void
    {

        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(4),
        ]);

        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'start_date' => now(),
            'end_date' => now()->addDays(1),
        ]);

        $endpoint = '/api/v1/travels/'.$travel->slug.'/tours';

        $response = $this->get($endpoint.'?dateFrom='.now());

        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

        $response = $this->get($endpoint.'?dateFrom='.now()->addDay());

        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

        $response = $this->get($endpoint.'?dateFrom='.now()->addDays(6));

        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint.'?dateTo='.now()->addDays(6));

        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

        $response = $this->get($endpoint.'?dateTo='.now()->addDay());

        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonMissing(['id' => $laterTour->id]);

        $response = $this->get($endpoint.'?dateTo='.now()->subDay());

        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint.'?dateFrom='.now()->addDay().'&dateTo='.now()->addDays(6));

        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

    }

    public function test_tour_lists_returns_validation_errors(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->getJson('/api/v1/travels/'.$travel->slug.'/tours?dateFrom=abcd');

        $response->assertStatus(422);

        $response = $this->getJson('/api/v1/travels/'.$travel->slug.'/tours?priceFrom=abcd');

        $response->assertStatus(422);
    }
}
