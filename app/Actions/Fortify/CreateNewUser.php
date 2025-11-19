<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

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
            'namapanggilan' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'notelp' => ['nullable', 'string', 'max:20'],
            'negara' => ['nullable', 'string', 'max:255'],
            'kota' => ['nullable', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            // Note: superadmin should typically not be created via public registration
            // Include it here for admin-initiated user creation flows
            'user_type' => ['nullable', 'in:personal,admin,instansi,gift,superadmin'],
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'namapanggilan' => $input['namapanggilan'] ?? null,
            'email' => $input['email'],
            'notelp' => $input['notelp'] ?? null,
            'negara' => $input['negara'] ?? null,
            'kota' => $input['kota'] ?? null,
            'password' => $input['password'],
            'user_type' => $input['user_type'] ?? 'personal',
        ]);
    }
}
