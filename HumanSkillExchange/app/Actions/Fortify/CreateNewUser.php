<?php

namespace App\Actions\Fortify;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $plan = Plan::firstOrCreate(
            ['name' => 'Gratis'],
            [
                'price' => 0,
                'max_skills' => 3,
                'max_needs' => 3,
                'max_offers' => 2,
                'max_exchange_requests' => 5,
            ]
        );

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role' => 'user',
            'plan_id' => $plan->id,
            'password' => Hash::make($input['password']),
        ]);
    }
}
