<?php

namespace Database\Seeders;

use App\Models\Need;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\User;
use App\Models\Profile;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ExchangeProgress;
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
        
        // 5 Akun Baru untuk Testing
        $siti = $this->seedUser('Siti Nurhaliza', 'siti@example.com', 'user', $gratis->id, 'siti-token-123');
        $budi = $this->seedUser('Budi Santoso', 'budi@example.com', 'user', $pro->id, 'budi-token-123');
        $dewi = $this->seedUser('Dewi Lestari', 'dewi@example.com', 'user', $gratis->id, 'dewi-token-123');
        $andi = $this->seedUser('Andi Pratama', 'andi@example.com', 'user', $proMax->id, 'andi-token-123');
        $rina = $this->seedUser('Rina Putri', 'rina@example.com', 'user', $pro->id, 'rina-token-123');

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
                'instagram_url' => 'https://instagram.com/raka.design',
                'linkedin_url' => 'https://linkedin.com/in/raka',
                'website_url' => 'https://portfolio.example.com/raka',
            ]
        );
        
        // Profil untuk 5 akun baru
        Profile::updateOrCreate(
            ['user_id' => $siti->id],
            [
                'bio' => 'Content writer dan copywriter dengan pengalaman 2 tahun. Bisa membantu tulisan untuk website dan sosial media.',
                'location' => 'Jakarta',
                'work_mode' => 'online',
                'available_time' => 'Setiap hari 09.00 - 17.00',
                'portfolio_url' => 'https://portfolio.example.com/siti',
                'instagram_url' => 'https://instagram.com/siti.writes',
                'linkedin_url' => 'https://linkedin.com/in/siti',
            ]
        );
        
        Profile::updateOrCreate(
            ['user_id' => $budi->id],
            [
                'bio' => 'Fullstack web developer dengan keahlian di React dan Node.js. Suka bikin project open source.',
                'location' => 'Bandung',
                'work_mode' => 'hybrid',
                'available_time' => 'Malam dan weekend',
                'portfolio_url' => 'https://portfolio.example.com/budi',
                'github_url' => 'https://github.com/budi',
                'linkedin_url' => 'https://linkedin.com/in/budi',
                'website_url' => 'https://budi.dev',
            ]
        );
        
        Profile::updateOrCreate(
            ['user_id' => $dewi->id],
            [
                'bio' => 'Digital marketer spesialis SEO dan social media management. Bisa membantu optimasi website dan konten.',
                'location' => 'Surabaya',
                'work_mode' => 'online',
                'available_time' => 'Setiap hari 10.00 - 18.00',
                'portfolio_url' => 'https://portfolio.example.com/dewi',
                'linkedin_url' => 'https://linkedin.com/in/dewi',
                'instagram_url' => 'https://instagram.com/dewi.marketing',
            ]
        );
        
        Profile::updateOrCreate(
            ['user_id' => $andi->id],
            [
                'bio' => 'UI/UX designer dan mentor desain. Sudah berpengalaman 5 tahun di industri tech.',
                'location' => 'Yogyakarta',
                'work_mode' => 'hybrid',
                'available_time' => 'Setiap hari, bisa jadwal fleksibel',
                'portfolio_url' => 'https://portfolio.example.com/andi',
                'linkedin_url' => 'https://linkedin.com/in/andi',
                'website_url' => 'https://andi.design',
            ]
        );
        
        Profile::updateOrCreate(
            ['user_id' => $rina->id],
            [
                'bio' => 'Mobile developer (iOS & Android) dengan Flutter. Bisa membantu bikin aplikasi mobile sederhana.',
                'location' => 'Semarang',
                'work_mode' => 'online',
                'available_time' => 'Weekday sore dan weekend',
                'portfolio_url' => 'https://portfolio.example.com/rina',
                'github_url' => 'https://github.com/rina',
                'linkedin_url' => 'https://linkedin.com/in/rina',
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
        
        // Skill untuk 5 akun baru
        Skill::updateOrCreate(['user_id' => $siti->id, 'name' => 'Content Writing'], [
            'category' => 'Writing',
            'level' => 'advanced',
        ]);
        Skill::updateOrCreate(['user_id' => $siti->id, 'name' => 'Copywriting'], [
            'category' => 'Marketing',
            'level' => 'intermediate',
        ]);
        
        Skill::updateOrCreate(['user_id' => $budi->id, 'name' => 'React.js'], [
            'category' => 'Programming',
            'level' => 'advanced',
        ]);
        Skill::updateOrCreate(['user_id' => $budi->id, 'name' => 'Node.js'], [
            'category' => 'Programming',
            'level' => 'advanced',
        ]);
        Skill::updateOrCreate(['user_id' => $budi->id, 'name' => 'MongoDB'], [
            'category' => 'Database',
            'level' => 'intermediate',
        ]);
        
        Skill::updateOrCreate(['user_id' => $dewi->id, 'name' => 'SEO Optimization'], [
            'category' => 'Marketing',
            'level' => 'advanced',
        ]);
        Skill::updateOrCreate(['user_id' => $dewi->id, 'name' => 'Social Media Management'], [
            'category' => 'Marketing',
            'level' => 'intermediate',
        ]);
        
        Skill::updateOrCreate(['user_id' => $andi->id, 'name' => 'UI Design'], [
            'category' => 'Design',
            'level' => 'advanced',
        ]);
        Skill::updateOrCreate(['user_id' => $andi->id, 'name' => 'UX Research'], [
            'category' => 'Design',
            'level' => 'advanced',
        ]);
        Skill::updateOrCreate(['user_id' => $andi->id, 'name' => 'Design System'], [
            'category' => 'Design',
            'level' => 'intermediate',
        ]);
        
        Skill::updateOrCreate(['user_id' => $rina->id, 'name' => 'Flutter'], [
            'category' => 'Mobile Development',
            'level' => 'intermediate',
        ]);
        Skill::updateOrCreate(['user_id' => $rina->id, 'name' => 'Dart'], [
            'category' => 'Programming',
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

        // Exchange request ketiga: pending (untuk testing accept)
        $offer3 = Offer::where('user_id', $siti->id)->first();
        if (!$offer3) {
            $offer3 = Offer::create([
                'user_id' => $siti->id,
                'title' => 'Saya bisa bantu content writing',
                'type' => 'skill',
                'category' => 'Writing',
                'description' => 'Saya bisa membantu menulis artikel dan konten sosial media.',
                'exchange_expectation' => 'Saya membutuhkan bantuan desain UI untuk portfolio.',
                'available_duration' => '5 jam per minggu',
            ]);
        }

        $need3 = Need::where('user_id', $andi->id)->first();
        if (!$need3) {
            $need3 = Need::create([
                'user_id' => $andi->id,
                'title' => 'Butuh content writer untuk portfolio',
                'category' => 'Writing',
                'description' => 'Saya membutuhkan tulisan untuk case study portfolio desain.',
                'exchange_offer' => 'Saya bisa membantu desain UI/UX.',
            ]);
        }

        $pendingExchange = \App\Models\ExchangeRequest::updateOrCreate(
            ['from_user_id' => $siti->id, 'to_user_id' => $andi->id],
            [
                'offer_id' => $offer3->id,
                'need_id' => $need3->id,
                'message' => 'Halo Andi, saya tertarik dengan offer Anda. Saya bisa bantu content writing!',
                'status' => 'pending',
                'completed_by_from_user' => false,
                'completed_by_to_user' => false,
            ]
        );

        // Exchange request keempat: accepted (dengan conversation dan messages)
        $offer4 = Offer::where('user_id', $budi->id)->first();
        if (!$offer4) {
            $offer4 = Offer::create([
                'user_id' => $budi->id,
                'title' => 'Saya bisa bantu React & Node.js',
                'type' => 'skill',
                'category' => 'Programming',
                'description' => 'Saya bisa membantu fullstack development dengan React dan Node.',
                'exchange_expectation' => 'Saya membutuhkan bantuan SEO untuk blog pribadi.',
                'available_duration' => '6 jam per minggu',
            ]);
        }

        $need4 = Need::where('user_id', $dewi->id)->first();
        if (!$need4) {
            $need4 = Need::create([
                'user_id' => $dewi->id,
                'title' => 'Butuh fullstack developer untuk blog',
                'category' => 'Programming',
                'description' => 'Saya membutuhkan bantuan untuk membuat blog pribadi.',
                'exchange_offer' => 'Saya bisa membantu SEO dan manajemen sosial media.',
            ]);
        }

        $acceptedExchange = \App\Models\ExchangeRequest::updateOrCreate(
            ['from_user_id' => $budi->id, 'to_user_id' => $dewi->id],
            [
                'offer_id' => $offer4->id,
                'need_id' => $need4->id,
                'message' => 'Halo Dewi, saya bisa membantu buat blog Anda!',
                'status' => 'accepted',
                'completed_by_from_user' => false,
                'completed_by_to_user' => false,
            ]
        );

        // Buat conversation untuk accepted exchange
        $conversation1 = Conversation::updateOrCreate(
            [
                'user1_id' => min($budi->id, $dewi->id),
                'user2_id' => max($budi->id, $dewi->id),
            ],
            [
                'exchange_request_id' => $acceptedExchange->id,
                'last_message_at' => now(),
            ]
        );

        // Tambahkan messages untuk conversation
        Message::updateOrCreate(
            ['conversation_id' => $conversation1->id, 'sender_id' => $dewi->id, 'content' => 'Halo Budi! Exchange kita sudah diterima. Yuk mulai diskusi tentang blognya!'],
            [
                'is_read' => false,
                'type' => 'text',
                'metadata' => null,
                'created_at' => now()->subMinutes(30),
            ]
        );

        Message::updateOrCreate(
            ['conversation_id' => $conversation1->id, 'sender_id' => $budi->id, 'content' => 'Baik, terima kasih Dewi! Apa framework yang ingin kita gunakan untuk blognya?'],
            [
                'is_read' => true,
                'type' => 'text',
                'metadata' => null,
                'created_at' => now()->subMinutes(25),
            ]
        );

        Message::updateOrCreate(
            ['conversation_id' => $conversation1->id, 'sender_id' => $dewi->id, 'content' => 'Ayo kita gunakan Laravel saja, biar saya juga bisa belajar tentang struktur backendnya!'],
            [
                'is_read' => true,
                'type' => 'text',
                'metadata' => null,
                'created_at' => now()->subMinutes(20),
            ]
        );

        // Exchange request kelima: in_progress dengan progress di chat
        $offer5 = Offer::where('user_id', $rina->id)->first();
        if (!$offer5) {
            $offer5 = Offer::create([
                'user_id' => $rina->id,
                'title' => 'Saya bisa bantu Flutter',
                'type' => 'skill',
                'category' => 'Mobile Development',
                'description' => 'Saya bisa membantu membuat aplikasi mobile sederhana dengan Flutter.',
                'exchange_expectation' => 'Saya membutuhkan latihan desain UI untuk portofolio.',
                'available_duration' => '4 jam per minggu',
            ]);
        }

        $need5 = Need::where('user_id', $raka->id)->first();
        if (!$need5) {
            $need5 = Need::create([
                'user_id' => $raka->id,
                'title' => 'Butuh bantuan buat mobile app',
                'category' => 'Mobile Development',
                'description' => 'Saya ingin belajar membuat aplikasi mobile sederhana.',
                'exchange_offer' => 'Saya bisa bantu desain UI dan Figma prototype.',
            ]);
        }

        $inProgressExchange2 = \App\Models\ExchangeRequest::updateOrCreate(
            ['from_user_id' => $raka->id, 'to_user_id' => $rina->id],
            [
                'offer_id' => $offer5->id,
                'need_id' => $need5->id,
                'message' => 'Halo Rina, saya mau belajar Flutter sama kamu!',
                'status' => 'in_progress',
                'completed_by_from_user' => false,
                'completed_by_to_user' => false,
            ]
        );

        $conversation2 = Conversation::updateOrCreate(
            [
                'user1_id' => min($raka->id, $rina->id),
                'user2_id' => max($raka->id, $rina->id),
            ],
            [
                'exchange_request_id' => $inProgressExchange2->id,
                'last_message_at' => now(),
            ]
        );

        // Tambahkan messages dan progress
        Message::updateOrCreate(
            ['conversation_id' => $conversation2->id, 'sender_id' => $rina->id, 'content' => 'Baik Raka! Kita mulai dari setup environment dulu ya.'],
            [
                'is_read' => true,
                'type' => 'text',
                'metadata' => null,
                'created_at' => now()->subHours(2),
            ]
        );

        $progress1 = ExchangeProgress::updateOrCreate(
            ['exchange_request_id' => $inProgressExchange2->id, 'user_id' => $rina->id],
            [
                'progress_note' => 'Setup Flutter SDK dan Android Studio sudah selesai. Berikut link panduan instalasi!',
                'file_url' => 'https://docs.flutter.dev/get-started/install',
            ]
        );

        Message::updateOrCreate(
            ['conversation_id' => $conversation2->id, 'sender_id' => $rina->id, 'content' => '📊 Update Progress: Setup Flutter SDK dan Android Studio sudah selesai. Berikut link panduan instalasi!'],
            [
                'is_read' => false,
                'type' => 'progress',
                'metadata' => [
                    'progress_id' => $progress1->id,
                    'progress_note' => $progress1->progress_note,
                    'file_url' => $progress1->file_url,
                ],
                'created_at' => now()->subHour(),
            ]
        );

        Message::updateOrCreate(
            ['conversation_id' => $conversation2->id, 'sender_id' => $raka->id, 'content' => 'Wow, terima kasih Rina! Saya akan coba install dulu!'],
            [
                'is_read' => false,
                'type' => 'text',
                'metadata' => null,
                'created_at' => now()->subMinutes(30),
            ]
        );
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
