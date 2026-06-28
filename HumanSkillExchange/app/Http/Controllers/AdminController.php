<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRequest;
use App\Models\MentoringRoom;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use App\Models\MentoringBooking;
use App\Notifications\BookingApprovedNotification;
use App\Notifications\BookingDeclinedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::orderBy('created_at', 'desc')->limit(50)->get();
        $rooms = MentoringRoom::with(['user', 'mentor'])->latest()->limit(50)->get();
        $transactions = Transaction::with('user')->latest()->limit(50)->get();
        $bookings = MentoringBooking::with(['room', 'user'])->latest()->limit(50)->get();
        return view('admin.dashboard', compact('users', 'rooms', 'transactions', 'bookings'));
    }

    public function users()
    {
        $users = User::with('plan')->latest()->paginate(30);
        return view('admin.users', compact('users'));
    }

    public function exchanges()
    {
        $exchanges = ExchangeRequest::with(['fromUser', 'toUser', 'offer', 'need'])->latest()->paginate(30);
        return view('admin.exchanges', compact('exchanges'));
    }

    public function reviews()
    {
        $reviews = Review::with(['reviewer', 'reviewedUser'])->latest()->paginate(30);
        return view('admin.reviews', compact('reviews'));
    }

    public function transactions()
    {
        $transactions = Transaction::with('user')->latest()->paginate(30);
        return view('admin.transactions', compact('transactions'));
    }

    public function verifyUser(Request $request, User $user): RedirectResponse
    {
        $user->update(['is_verified' => true]);
        return back()->with('status', "User {$user->name} berhasil diverifikasi.");
    }

    public function hideReview(Request $request, Review $review): RedirectResponse
    {
        $review->update(['is_hidden' => true]);
        return back()->with('status', 'Review berhasil disembunyikan.');
    }

    public function unhideReview(Request $request, Review $review): RedirectResponse
    {
        $review->update(['is_hidden' => false]);
        return back()->with('status', 'Review berhasil ditampilkan kembali.');
    }

    public function approveBooking(Request $request, $booking): RedirectResponse
    {
        $bookingRecord = MentoringBooking::findOrFail($booking);
        $bookingRecord->update(['status' => 'approved']);

        // Send notification to user
        $bookingRecord->user->notify(new BookingApprovedNotification($bookingRecord));

        return back()->with('status', 'Booking approved dan notifikasi dikirim.');
    }

    public function declineBooking(Request $request, $booking): RedirectResponse
    {
        $bookingRecord = MentoringBooking::findOrFail($booking);
        $bookingRecord->update(['status' => 'declined']);

        // Send notification to user
        $bookingRecord->user->notify(new BookingDeclinedNotification($bookingRecord));

        return back()->with('status', 'Booking declined dan notifikasi dikirim.');
    }

    public function completeTransaction(Request $request, $transaction): RedirectResponse
    {
        Transaction::where('id', $transaction)->update(['status' => 'completed']);
        return back()->with('status', 'Transaction marked completed.');
    }
}

