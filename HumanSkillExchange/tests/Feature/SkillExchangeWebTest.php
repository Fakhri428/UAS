<?php

namespace Tests\Feature;

use App\Models\ExchangeRequest;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillExchangeWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_dashboard_renders_management_forms(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Profil exchange')
            ->assertSee('Tambah skill')
            ->assertSee('Buat Koukan Offer')
            ->assertSee('Buat Koukan Need');
    }

    public function test_authenticated_user_can_create_skill_offer_need_and_exchange_request(): void
    {
        $user = User::factory()->create();
        $partner = User::factory()->create();

        $myNeed = Need::create([
            'user_id' => $user->id,
            'title' => 'Butuh desain dashboard',
            'category' => 'Design',
            'description' => 'Saya butuh bantuan UI dashboard.',
            'exchange_offer' => 'Saya bisa bantu REST API Laravel.',
        ]);

        $partnerOffer = Offer::create([
            'user_id' => $partner->id,
            'title' => 'Saya bisa bantu UI Figma',
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Saya bisa bantu wireframe dan prototype.',
            'exchange_expectation' => 'Saya butuh bantuan backend.',
            'available_duration' => '3 jam per minggu',
        ]);

        $this->actingAs($user)
            ->post(route('skills.store'), [
                'name' => 'Laravel REST API',
                'category' => 'Programming',
                'level' => 'intermediate',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('skills', [
            'user_id' => $user->id,
            'name' => 'Laravel REST API',
        ]);

        $this->actingAs($user)
            ->post(route('offers.store'), [
                'title' => 'Saya bisa bantu REST API Laravel',
                'type' => 'skill',
                'category' => 'Programming',
                'description' => 'Saya bisa bantu login, CRUD, validasi, dan dokumentasi.',
                'exchange_expectation' => 'Saya butuh bantuan desain dashboard.',
                'available_duration' => '4 jam per minggu',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('offers', [
            'user_id' => $user->id,
            'title' => 'Saya bisa bantu REST API Laravel',
        ]);

        $this->actingAs($user)
            ->post(route('needs.store'), [
                'title' => 'Butuh review UI portfolio',
                'category' => 'Design',
                'description' => 'Saya ingin UI portfolio diperiksa.',
                'exchange_offer' => 'Saya bisa bantu endpoint API.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('needs', [
            'user_id' => $user->id,
            'title' => 'Butuh review UI portfolio',
        ]);

        $this->actingAs($user)
            ->post(route('offers.request', $partnerOffer), [
                'need_id' => $myNeed->id,
                'message' => 'Halo, saya ingin exchange UI dengan backend.',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('exchange_requests', [
            'from_user_id' => $user->id,
            'to_user_id' => $partner->id,
            'offer_id' => $partnerOffer->id,
            'need_id' => $myNeed->id,
            'status' => 'pending',
        ]);
    }

    public function test_exchange_recipient_can_accept_and_participants_can_complete(): void
    {
        $requester = User::factory()->create();
        $recipient = User::factory()->create();
        $offer = Offer::create([
            'user_id' => $recipient->id,
            'title' => 'Mentoring Laravel',
            'type' => 'mentoring',
            'category' => 'Programming',
            'description' => 'Sesi mentoring Laravel.',
            'exchange_expectation' => 'Butuh desain slide.',
        ]);
        $need = Need::create([
            'user_id' => $requester->id,
            'title' => 'Butuh mentoring API',
            'category' => 'Programming',
            'description' => 'Butuh arahan membuat REST API.',
            'exchange_offer' => 'Saya bisa bantu desain slide.',
        ]);
        $exchange = ExchangeRequest::create([
            'from_user_id' => $requester->id,
            'to_user_id' => $recipient->id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => 'Mari exchange.',
            'status' => 'pending',
        ]);

        $this->actingAs($recipient)
            ->patch(route('exchange-requests.update', $exchange), ['action' => 'accept'])
            ->assertRedirect();

        $this->assertDatabaseHas('exchange_requests', [
            'id' => $exchange->id,
            'status' => 'accepted',
        ]);

        $this->actingAs($requester)
            ->patch(route('exchange-requests.update', $exchange), ['action' => 'complete'])
            ->assertRedirect();

        $this->assertDatabaseHas('exchange_requests', [
            'id' => $exchange->id,
            'status' => 'in_progress',
            'completed_by_from_user' => true,
        ]);

        $this->actingAs($recipient)
            ->patch(route('exchange-requests.update', $exchange), ['action' => 'complete'])
            ->assertRedirect();

        $this->assertDatabaseHas('exchange_requests', [
            'id' => $exchange->id,
            'status' => 'completed',
            'completed_by_to_user' => true,
        ]);
    }
}
