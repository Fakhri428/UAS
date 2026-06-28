<?php

namespace Tests\Feature;

use App\Models\MentoringBooking;
use App\Models\MentoringRoom;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentoringBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_mentoring_booking(): void
    {
        $mentor = User::factory()->create();
        $user = User::factory()->create();

        $room = MentoringRoom::create([
            'mentor_id' => $mentor->id,
            'title' => 'Mentoring Laravel',
            'description' => 'Sesi mentoring dasar',
            'duration_minutes' => 60,
            'price' => 0,
            'schedule' => now()->addWeek(),
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('mentoring-bookings.store'), [
                'mentoring_room_id' => $room->id,
                'scheduled_at' => now()->addDays(3)->toDateTimeString(),
                'duration_minutes' => 60,
            ]);

        $response->assertStatus(201)->assertJsonStructure(['data' => ['id', 'status']]);

        $this->assertDatabaseHas('mentoring_bookings', [
            'mentoring_room_id' => $room->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_and_decline_booking(): void
    {
        $mentor = User::factory()->create();
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $room = MentoringRoom::create([
            'mentor_id' => $mentor->id,
            'title' => 'Mentoring Laravel',
            'description' => 'Sesi mentoring dasar',
            'duration_minutes' => 60,
            'price' => 0,
            'schedule' => now()->addWeek(),
        ]);

        $booking = MentoringBooking::create([
            'mentoring_room_id' => $room->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'scheduled_at' => now()->addDays(2),
            'duration_minutes' => 60,
        ]);

        $this->withoutMiddleware();
        $this->actingAs($admin)
            ->post(route('admin.bookings.approve', $booking))
            ->assertRedirect();

        $this->assertDatabaseHas('mentoring_bookings', [
            'id' => $booking->id,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.bookings.decline', $booking))
            ->assertRedirect();

        $this->assertDatabaseHas('mentoring_bookings', [
            'id' => $booking->id,
            'status' => 'declined',
        ]);
    }

    public function test_admin_can_mark_transaction_completed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $tx = Transaction::create([
            'user_id' => $user->id,
            'amount' => 10000,
            'type' => 'subscription',
            'status' => 'pending',
            'platform_fee' => 0,
        ]);

        $this->withoutMiddleware();
        $this->actingAs($admin)
            ->post(route('admin.transactions.complete', $tx))
            ->assertRedirect();

        $this->assertDatabaseHas('transactions', [
            'id' => $tx->id,
            'status' => 'completed',
        ]);
    }
}
