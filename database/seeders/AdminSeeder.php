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
        $email = trim((string) config('seeding.admin_email'));
        if (! $email) {
            $this->command?->warn('Admin tidak dibuat: isi SEED_ADMIN_EMAIL, SEED_ADMIN_NAME, dan SEED_ADMIN_PASSWORD di .env server, lalu jalankan php artisan config:clear.');

            return;
        }
        $email = mb_strtolower($email);
        $existing = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($existing) {
            $this->command?->warn($existing->is_admin ? 'Admin sudah tersedia. Password akun tidak diubah.' : 'Email sudah digunakan user non-admin. Seeder tidak mengubah hak akses akun yang sudah ada.');

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
        $this->command?->info('Akun admin berhasil dibuat. Login menggunakan kredensial SEED_ADMIN yang Anda tetapkan.');
    }
}
