<?php
// Restore User.php
$file = 'app/Models/User.php';
$content = file_get_contents($file);
if (!str_contains($content, 'koukan_score')) {
    $content = str_replace(
        "protected \$fillable = [\n        'name',\n        'email',\n        'password',\n        'role',\n        'plan_id',\n    ];", 
        "protected \$fillable = [\n        'name',\n        'email',\n        'password',\n        'role',\n        'plan_id',\n        'koukan_score',\n    ];",
        $content
    );
    $content = str_replace(
        "public function reviewsReceived()",
        "public function recalculateKoukanScore()\n    {\n        \$average = \$this->reviewsReceived()->avg('rating');\n        \$this->update(['koukan_score' => \$average ? round(\$average, 2) : null]);\n    }\n\n    public function reviewsReceived()",
        $content
    );
    file_put_contents($file, $content);
}

// Restore Review.php
$file = 'app/Models/Review.php';
$content = file_get_contents($file);
if (!str_contains($content, 'booted()')) {
    $content = str_replace(
        "return \$this->belongsTo(User::class, 'reviewed_user_id');\n    }",
        "return \$this->belongsTo(User::class, 'reviewed_user_id');\n    }\n\n    protected static function booted()\n    {\n        static::saved(function (\$review) {\n            if (\$review->reviewee) {\n                \$review->reviewee->recalculateKoukanScore();\n            }\n        });\n\n        static::deleted(function (\$review) {\n            if (\$review->reviewee) {\n                \$review->reviewee->recalculateKoukanScore();\n            }\n        });\n    }",
        $content
    );
    file_put_contents($file, $content);
}

// Restore SkillExchangeController.php
$file = 'app/Http/Controllers/SkillExchangeController.php';
$content = file_get_contents($file);
if (!str_contains($content, 'showKoukanId')) {
    $content = str_replace(
        "public function showProfile(User \$user)",
        "public function showKoukanId(\$username)\n    {\n        \$user = User::where('name', \$username)->first();\n\n        if (!\$user) {\n            \$user = User::findOrFail(\$username);\n        }\n\n        \$viewer = auth()->user();\n\n        \$user->load([\n            'profile',\n            'skills',\n            'offers',\n            'needs',\n            'portfolios',\n            'reviewsReceived.reviewer',\n        ]);\n\n        \$reputation = [\n            'score' => number_format(\$user->reviewsReceived->avg('rating') ?: 0, 1),\n            'reviews' => \$user->reviewsReceived->count(),\n            'completed' => ExchangeRequest::where(function (\$q) use (\$user) {\n                \$q->where('requester_id', \$user->id)->orWhere('receiver_id', \$user->id);\n            })->where('status', 'completed')->count(),\n        ];\n\n        return view('skill-exchange.profile.show', compact('user', 'viewer', 'reputation'));\n    }\n\n    public function showProfile(User \$user)",
        $content
    );
    $content = str_replace(
        "public function showProfile(User \$user)\n    {",
        "public function showProfile(User \$user)\n    {\n        return redirect()->route('user.show', \$user->name);",
        $content
    );
    file_put_contents($file, $content);
}

// Restore web.php
$file = 'routes/web.php';
$content = file_get_contents($file);
if (!str_contains($content, '/user/{username}')) {
    $content = str_replace(
        "Route::get('/market', [SkillExchangeController::class, 'market'])->name('market');",
        "Route::get('/market', [SkillExchangeController::class, 'market'])->name('market');\nRoute::get('/explore', [SkillExchangeController::class, 'market']);\nRoute::get('/user/{username}', [SkillExchangeController::class, 'showKoukanId'])->name('user.show');",
        $content
    );
    file_put_contents($file, $content);
}
echo "PATCH SUCCESS\n";
