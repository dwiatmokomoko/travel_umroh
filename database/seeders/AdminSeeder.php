<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('seeding.admin_email');
        if (! $email) {
            return;
        }
        $email = mb_strtolower($email);
        if (User::where('email', $email)->exists()) {
            return;
        }
        $data = Validator::make([
            'email' => $email, 'name' => config('seeding.admin_name'), 'password' => config('seeding.admin_password'),
        ], [
            'email' => ['required', 'email', 'max:255'], 'name' => ['required', 'string', 'max:100'],
            'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()],
        ])->validate();
        $user = new User($data);
        $user->is_admin = true;
        $user->save();
    }
}
