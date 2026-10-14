<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email} {--name=Administrator} {--generate : Generate a secure initial password}';

    protected $description = 'Create an administrator without public registration';

    public function handle(): int
    {
        $email = Str::lower($this->argument('email'));
        $password = $this->option('generate') ? Str::password(20) : $this->secret('Kata sandi (minimal 12 karakter)');
        $data = ['email' => $email, 'password' => $password, 'name' => $this->option('name')];
        $validator = Validator::make($data, ['email' => 'required|email|unique:users,email', 'password' => 'required|string|min:12', 'name' => 'required|string|max:100']);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        $user = new User($data);
        $user->is_admin = true;
        $user->save();
        $this->info('Administrator dibuat: '.$email);
        if ($this->option('generate')) {
            $this->line('Kata sandi awal: '.$password);
        }

        return self::SUCCESS;
    }
}
