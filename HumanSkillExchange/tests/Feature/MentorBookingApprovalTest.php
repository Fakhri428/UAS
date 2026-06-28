<?php

namespace Tests\Feature;

use App\Models\MentoringBooking;
use App\Models\MentoringRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorBookingApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_can_approve_booking_for_their_room(): void
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

        $booking = MentoringBooking::create([
            'mentoring_room_id' => $room->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'scheduled_at' => now()->addDays(2),
            'duration_minutes' => 60,
        ]);

        $this->actingAs($mentor)
            ->post(route('mentoring-bookings.mentor.approve', $booking))
            ->assertRedirect();

        $this->assertDatabaseHas('mentoring_bookings', [
            'id' => $booking->id,
            'status' => 'approved',
        ]);
    }

    public function test_non_mentor_cannot_approve_booking(): void
    {
        $mentor = User::factory()->create();
        $other = User::factory()->create();
        $user = User::factory()->create();

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

        $this->actingAs($other)
            ->post(route('mentoring-bookings.mentor.approve', $booking))
            ->assertStatus(403);
    }
}
