<?php

namespace Tests\Feature;

use App\Models\ExchangeRequest;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_all_panel_pages(): void
    {
        $admin = $this->admin();

        foreach (['admin.dashboard'] as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_non_admin_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    /*
    public function test_admin_can_verify_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['is_verified' => false]);

        $this->actingAs($admin)
            ->post(route('admin.users.verify', $target))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'is_verified' => true]);
    }

    public function test_admin_can_hide_and_unhide_review(): void
    {
        $admin = $this->admin();
        $reviewer = User::factory()->create();
        $reviewed = User::factory()->create();

        $exchange = ExchangeRequest::create([
            'from_user_id' => $reviewer->id,
            'to_user_id'   => $reviewed->id,
            'message'      => 'test',
            'status'       => 'accepted',
        ]);

        $review = Review::create([
            'exchange_request_id' => $exchange->id,
            'reviewer_id'         => $reviewer->id,
            'reviewed_user_id'    => $reviewed->id,
            'rating'              => 5,
            'comment'             => 'Mantap',
            'is_hidden'           => false,
        ]);

        $this->actingAs($admin)->post(route('admin.reviews.hide', $review))->assertRedirect();
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_hidden' => true]);

        $this->actingAs($admin)->post(route('admin.reviews.unhide', $review))->assertRedirect();
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_hidden' => false]);
    }
    */
}
