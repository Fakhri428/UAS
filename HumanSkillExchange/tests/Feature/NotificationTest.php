<?php

namespace Tests\Feature;

use App\Models\ExchangeRequest;
use App\Models\MentoringBooking;
use App\Models\MentoringRoom;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\BookingApprovedNotification;
use App\Notifications\BookingDeclinedNotification;
use App\Notifications\ExchangeAcceptedNotification;
use App\Notifications\ExchangeCompletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_receives_notification_when_booking_approved(): void
    {
        Notification::fake();

        $mentor = User::factory()->create();
        $user = User::factory()->create();

        $room = MentoringRoom::create([
            'mentor_id' => $mentor->id,
            'title' => 'Test mentoring',
            'description' => 'Test',
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
            ->post(route('mentoring-bookings.mentor.approve', $booking));

        Notification::assertSentTo($user, BookingApprovedNotification::class);
    }

    public function test_user_receives_notification_when_booking_declined(): void
    {
        Notification::fake();

        $mentor = User::factory()->create();
        $user = User::factory()->create();

        $room = MentoringRoom::create([
            'mentor_id' => $mentor->id,
            'title' => 'Test mentoring',
            'description' => 'Test',
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
            ->post(route('mentoring-bookings.mentor.decline', $booking));

        Notification::assertSentTo($user, BookingDeclinedNotification::class);
    }

    public function test_user_receives_notification_when_exchange_accepted(): void
    {
        Notification::fake();

        $requester = User::factory()->create();
        $recipient = User::factory()->create();
        $offer = Offer::create([
            'user_id' => $recipient->id,
            'title' => 'Test offer',
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_expectation' => 'Test',
        ]);
        $need = Need::create([
            'user_id' => $requester->id,
            'title' => 'Test need',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_offer' => 'Test',
        ]);

        $exchange = ExchangeRequest::create([
            'from_user_id' => $requester->id,
            'to_user_id' => $recipient->id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => 'Test',
            'status' => 'pending',
        ]);

        $this->actingAs($recipient)
            ->patch(route('exchange-requests.update', $exchange), ['action' => 'accept']);

        Notification::assertSentTo($requester, ExchangeAcceptedNotification::class);
    }

    public function test_users_receive_notification_when_exchange_completed(): void
    {
        Notification::fake();

        $requester = User::factory()->create();
        $recipient = User::factory()->create();
        $offer = Offer::create([
            'user_id' => $recipient->id,
            'title' => 'Test offer',
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_expectation' => 'Test',
        ]);
        $need = Need::create([
            'user_id' => $requester->id,
            'title' => 'Test need',
            'category' => 'Design',
            'description' => 'Test',
            'exchange_offer' => 'Test',
        ]);

        $exchange = ExchangeRequest::create([
            'from_user_id' => $requester->id,
            'to_user_id' => $recipient->id,
            'offer_id' => $offer->id,
            'need_id' => $need->id,
            'message' => 'Test',
            'status' => 'in_progress',
        ]);

        $this->actingAs($requester)
            ->patch(route('exchange-requests.update', $exchange), ['action' => 'complete']);

        $this->actingAs($recipient)
            ->patch(route('exchange-requests.update', $exchange->fresh()), ['action' => 'complete']);

        Notification::assertSentTo($requester, ExchangeCompletedNotification::class);
        Notification::assertSentTo($recipient, ExchangeCompletedNotification::class);
    }
}
