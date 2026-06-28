<?php

namespace Tests\Feature;

use App\Models\ExchangeProgress;
use App\Models\ExchangeRequest;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_progress(): void
    {
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        $offer = Offer::create([
            'user_id' => $toUser->id,
            'title' => 'Test offer',
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_expectation' => 'Test',
        ]);
        $need = Need::create([
            'user_id' => $fromUser->id,
            'title' => 'Test need',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_offer' => 'Test',
        ]);

        $exchange = ExchangeRequest::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => 'Test exchange',
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($fromUser)
            ->post(route('exchange-requests.progress.store', $exchange), [
                'progress_note' => 'Selesai 50% dari pekerjaan',
                'file_url' => 'https://example.com/proof.pdf',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('exchange_progress', [
            'exchange_request_id' => $exchange->id,
            'user_id' => $fromUser->id,
            'progress_note' => 'Selesai 50% dari pekerjaan',
            'file_url' => 'https://example.com/proof.pdf',
        ]);
    }

    public function test_user_cannot_upload_progress_for_others_exchange(): void
    {
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        $other = User::factory()->create();
        $offer = Offer::create([
            'user_id' => $toUser->id,
            'title' => 'Test offer',
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_expectation' => 'Test',
        ]);
        $need = Need::create([
            'user_id' => $fromUser->id,
            'title' => 'Test need',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_offer' => 'Test',
        ]);

        $exchange = ExchangeRequest::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => 'Test exchange',
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($other)
            ->post(route('exchange-requests.progress.store', $exchange), [
                'progress_note' => 'Selesai 50% dari pekerjaan',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_progress(): void
    {
        $user = User::factory()->create();
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        $offer = Offer::create([
            'user_id' => $toUser->id,
            'title' => 'Test',
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_expectation' => 'Test',
        ]);
        $need = Need::create([
            'user_id' => $fromUser->id,
            'title' => 'Test',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_offer' => 'Test',
        ]);

        $exchange = ExchangeRequest::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => 'Test',
            'status' => 'in_progress',
        ]);

        $progress = ExchangeProgress::create([
            'exchange_request_id' => $exchange->id,
            'user_id' => $user->id,
            'progress_note' => 'Test progress',
        ]);

        $response = $this->actingAs($user)
            ->delete(route('exchange-progress.destroy', $progress));

        $response->assertRedirect();

        $this->assertDatabaseMissing('exchange_progress', [
            'id' => $progress->id,
        ]);
    }

    public function test_user_cannot_delete_others_progress(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        $offer = Offer::create([
            'user_id' => $toUser->id,
            'title' => 'Test',
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_expectation' => 'Test',
        ]);
        $need = Need::create([
            'user_id' => $fromUser->id,
            'title' => 'Test',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_offer' => 'Test',
        ]);

        $exchange = ExchangeRequest::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => 'Test',
            'status' => 'in_progress',
        ]);

        $progress = ExchangeProgress::create([
            'exchange_request_id' => $exchange->id,
            'user_id' => $user->id,
            'progress_note' => 'Test progress',
        ]);

        $response = $this->actingAs($other)
            ->delete(route('exchange-progress.destroy', $progress));

        $response->assertStatus(403);
    }

    public function test_progress_upload_requires_valid_status(): void
    {
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        $offer = Offer::create([
            'user_id' => $toUser->id,
            'title' => 'Test',
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_expectation' => 'Test',
        ]);
        $need = Need::create([
            'user_id' => $fromUser->id,
            'title' => 'Test',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_offer' => 'Test',
        ]);

        $exchange = ExchangeRequest::create([
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => 'Test exchange',
            'status' => 'pending', // Invalid for progress upload
        ]);

        $response = $this->actingAs($fromUser)
            ->post(route('exchange-requests.progress.store', $exchange), [
                'progress_note' => 'Test',
            ]);

        $response->assertStatus(422);
    }
}

