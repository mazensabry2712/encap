<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Command\Command as CommandCode;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--name= : Administrator display name} {--email= : Administrator email address}';

    protected $description = 'Create the first ENCAP admin user with an interactive password prompt.';

    public function handle(): int
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Administrator name')));
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Administrator email'))));

        $validator = Validator::make(
            ['name' => $name, 'email' => $email],
            ['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email:rfc', 'max:190']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return CommandCode::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('An account with that email already exists. No account was changed.');

            return CommandCode::FAILURE;
        }

        $password = $this->secret('Administrator password (at least 12 characters)');
        $confirmation = $this->secret('Confirm password');

        if (!is_string($password) || strlen($password) < 12 || !is_string($confirmation) || !hash_equals($password, $confirmation)) {
            $this->error('Password confirmation failed or the password is shorter than 12 characters. No account was created.');

            return CommandCode::FAILURE;
        }

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->info('Administrator account created successfully.');

        return CommandCode::SUCCESS;
    }
}
