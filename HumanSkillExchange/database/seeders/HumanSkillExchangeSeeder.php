<?php

namespace Database\Seeders;

use App\Models\Need;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class HumanSkillExchangeSeeder extends Seeder
{
    public function run(): void
    {
        $gratis = Plan::updateOrCreate(
            ['name' => 'Gratis'],
            ['price' => 0, 'max_skills' => 3, 'max_needs' => 3, 'max_offers' => 2, 'max_exchange_requests' => 5]
        );

        $pro = Plan::updateOrCreate(
            ['name' => 'Pro'],
            ['price' => 19000, 'max_skills' => 10, 'max_needs' => 10, 'max_offers' => 10, 'max_exchange_requests' => 30]
        );

        $proMax = Plan::updateOrCreate(
            ['name' => 'Pro Max'],
            ['price' => 59000, 'max_skills' => null, 'max_needs' => null, 'max_offers' => null, 'max_exchange_requests' => null]
        );

        $fakhri = $this->seedUser('Fakhri', 'fakhri@example.com', 'user', $gratis->id, 'fakhri-token-123');
        $raka = $this->seedUser('Raka', 'raka@example.com', 'user', $gratis->id, 'raka-token-123');
        $this->seedUser('Admin Human Skill', 'admin@hse.test', 'admin', $proMax->id, 'admin-token-123');

        Profile::updateOrCreate(
            ['user_id' => $fakhri->id],
            [
                'bio' => 'Backend learner yang bisa membantu membuat REST API Laravel dan dokumentasi Postman.',
                'location' => 'Purwokerto',
                'work_mode' => 'online',
                'available_time' => 'Malam dan akhir pekan',
                'portfolio_url' => 'https://portfolio.example.com/fakhri',
                'social_url' => 'https://linkedin.com/in/fakhri',
                'github_url' => 'https://github.com/fakhri',
                'linkedin_url' => 'https://linkedin.com/in/fakhri',
                'website_url' => 'https://portfolio.example.com/fakhri',
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $raka->id],
            [
                'bio' => 'UI designer pemula yang ingin membangun portofolio aplikasi web dan mobile.',
                'location' => 'Purwokerto',
                'work_mode' => 'hybrid',
                'available_time' => 'Sore hari',
                'portfolio_url' => 'https://portfolio.example.com/raka',
                'social_url' => 'https://dribbble.com/raka',
                'instagram_url' => 'https://instagram.com/raka.design',
                'linkedin_url' => 'https://linkedin.com/in/raka',
                'website_url' => 'https://portfolio.example.com/raka',
            ]
        );

        Skill::updateOrCreate(['user_id' => $fakhri->id, 'name' => 'Laravel REST API'], [
            'category' => 'Programming',
            'level' => 'intermediate',
        ]);
        Skill::updateOrCreate(['user_id' => $fakhri->id, 'name' => 'Postman Documentation'], [
            'category' => 'Documentation',
            'level' => 'intermediate',
        ]);
        Skill::updateOrCreate(['user_id' => $raka->id, 'name' => 'UI Design'], [
            'category' => 'Design',
            'level' => 'intermediate',
        ]);
        Skill::updateOrCreate(['user_id' => $raka->id, 'name' => 'Figma Prototype'], [
            'category' => 'Design',
            'level' => 'intermediate',
        ]);

        Need::updateOrCreate(['user_id' => $fakhri->id, 'title' => 'Butuh bantuan desain UI dashboard'], [
            'category' => 'Design',
            'description' => 'Saya membutuhkan desain dashboard untuk aplikasi REST API.',
            'exchange_offer' => 'Saya bisa membantu membuat endpoint CRUD dan dokumentasi API.',
        ]);

        Need::updateOrCreate(['user_id' => $raka->id, 'title' => 'Butuh bantuan Laravel REST API'], [
            'category' => 'Programming',
            'description' => 'Saya membutuhkan backend API untuk project portofolio UI saya.',
            'exchange_offer' => 'Saya bisa membuat desain UI dan prototype Figma.',
        ]);

        Offer::updateOrCreate(['user_id' => $fakhri->id, 'title' => 'Saya bisa bantu membuat REST API Laravel'], [
            'type' => 'skill',
            'category' => 'Programming',
            'description' => 'Saya bisa membantu API login, CRUD, validasi, dan dokumentasi Postman.',
            'exchange_expectation' => 'Saya membutuhkan bantuan desain UI dashboard.',
            'available_duration' => '4 jam per minggu',
        ]);

        Offer::updateOrCreate(['user_id' => $raka->id, 'title' => 'Saya bisa bantu desain UI di Figma'], [
            'type' => 'skill',
            'category' => 'Design',
            'description' => 'Saya bisa membuat wireframe, UI dashboard, dan prototype sederhana.',
            'exchange_expectation' => 'Saya membutuhkan bantuan backend REST API.',
            'available_duration' => '3 jam per minggu',
        ]);

        // Exchange request: Fakhri → Raka
        $offer = Offer::where('user_id', $raka->id)->first();
        $need  = Need::where('user_id', $fakhri->id)->first();

        $exchange = \App\Models\ExchangeRequest::updateOrCreate(
            ['from_user_id' => $fakhri->id, 'to_user_id' => $raka->id],
            [
                'offer_id'                 => $offer?->id,
                'need_id'                  => $need?->id,
                'message'                  => 'Halo Raka, saya tertarik exchange. Saya bisa bantu API, kamu bantu desain ya.',
                'status'                   => 'completed',
                'completed_by_from_user'   => true,
                'completed_by_to_user'     => true,
            ]
        );

        // Progress entries
        \App\Models\ExchangeProgress::updateOrCreate(
            ['exchange_request_id' => $exchange->id, 'user_id' => $fakhri->id],
            [
                'progress_note' => 'Sudah selesai membuat endpoint login dan register. Dokumentasi Postman sudah dikirim via Drive.',
                'file_url'      => 'https://drive.google.com/sample-api-docs',
            ]
        );

        \App\Models\ExchangeProgress::updateOrCreate(
            ['exchange_request_id' => $exchange->id, 'user_id' => $raka->id],
            [
                'progress_note' => 'Wireframe dashboard sudah dibuat, tinggal review dari Fakhri.',
                'file_url'      => 'https://figma.com/sample-design',
            ]
        );

        // Reviews setelah exchange selesai
        \App\Models\Review::updateOrCreate(
            ['exchange_request_id' => $exchange->id, 'reviewer_id' => $fakhri->id],
            [
                'reviewed_user_id' => $raka->id,
                'rating'           => 5,
                'comment'          => 'Raka sangat responsif dan desainnya bersih. Highly recommended!',
            ]
        );

        \App\Models\Review::updateOrCreate(
            ['exchange_request_id' => $exchange->id, 'reviewer_id' => $raka->id],
            [
                'reviewed_user_id' => $fakhri->id,
                'rating'           => 5,
                'comment'          => 'Fakhri cepat dalam membuat API dan dokumentasinya sangat lengkap.',
            ]
        );

        // Update exchange status ke reviewed karena dua-duanya sudah review
        $exchange->update(['status' => 'reviewed']);

        // Exchange ke-2: in_progress (untuk testing form review & progress)
        $offer2 = Offer::where('user_id', $fakhri->id)->first();
        $need2  = Need::where('user_id', $raka->id)->first();

        \App\Models\ExchangeRequest::updateOrCreate(
            ['from_user_id' => $raka->id, 'to_user_id' => $fakhri->id],
            [
                'offer_id'               => $offer2?->id,
                'need_id'                => $need2?->id,
                'message'                => 'Halo Fakhri, mari kita exchange lagi untuk project portfolio saya.',
                'status'                 => 'in_progress',
                'completed_by_from_user' => false,
                'completed_by_to_user'   => false,
            ]
        );

        // Mentoring room dari Fakhri (Pro Max tidak wajib, tapi test pakai admin)
        $adminUser = User::where('email', 'admin@hse.test')->first();
        if ($adminUser) {
            \App\Models\MentoringRoom::updateOrCreate(
                ['mentor_id' => $adminUser->id, 'title' => 'Dasar Laravel REST API'],
                [
                    'description'      => 'Belajar membuat REST API dari nol dengan Laravel 11, Sanctum, dan best practices.',
                    'schedule'         => now()->addDays(7),
                    'duration_minutes' => 90,
                    'price'            => 0,
                ]
            );
        }
    }

    private function seedUser(string $name, string $email, string $role, int $planId, string $plainToken): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password123'),
                'role' => $role,
                'plan_id' => $planId,
            ]
        );

        PersonalAccessToken::updateOrCreate(
            [
                'tokenable_type' => User::class,
                'tokenable_id' => $user->id,
                'name' => 'seed-token',
            ],
            [
                'token' => hash('sha256', $plainToken),
                'abilities' => ['*'],
            ]
        );

        return $user;
    }
}
