<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'create-admin-account
                          {--name= : Full name of the admin user}
                          {--username= : Username for the admin user}
                          {--email= : Email address for the admin user}
                          {--password= : Password for the admin user}
                          {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new admin user with optional CLI arguments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating a new admin user for PornTk...');
        $this->newLine();

        try {
            $name = $this->option('name') ?: text(
                label: 'Full Name',
                placeholder: 'Enter the admin\'s full name',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 2 => 'Name must be at least 2 characters.',
                    strlen($value) > 255 => 'Name must not exceed 255 characters.',
                    default => null
                }
            );

            if ($this->option('name')) {
                $nameValidation = $this->validateNameForCli($name);
                if ($nameValidation !== true) {
                    $this->error($nameValidation);

                    return Command::FAILURE;
                }
            }

            $username = $this->option('username') ?: text(
                label: 'Username',
                placeholder: 'Enter a unique username',
                required: true,
                validate: function (string $value) {
                    return $this->validateUsernameForPrompt($value);
                }
            );

            if ($this->option('username')) {
                $usernameValidation = $this->validateUsernameForCli($username);
                if ($usernameValidation !== true) {
                    $this->error($usernameValidation);

                    return Command::FAILURE;
                }
            }

            $email = $this->option('email') ?: text(
                label: 'Email Address',
                placeholder: 'admin@example.com',
                required: true,
                validate: function (string $value) {
                    return $this->validateEmailForPrompt($value);
                }
            );

            if ($this->option('email')) {
                $emailValidation = $this->validateEmailForCli($email);
                if ($emailValidation !== true) {
                    $this->error($emailValidation);

                    return Command::FAILURE;
                }
            }

            $password = $this->option('password') ?: password(
                label: 'Password',
                placeholder: 'Enter a secure password',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 8 => 'Password must be at least 8 characters.',
                    default => null
                }
            );

            if ($this->option('password')) {
                $passwordValidation = $this->validatePasswordForCli($password);
                if ($passwordValidation !== true) {
                    $this->error($passwordValidation);

                    return Command::FAILURE;
                }
            }

            if (! $this->option('password')) {
                $confirmPassword = password(
                    label: 'Confirm Password',
                    placeholder: 'Re-enter the password',
                    required: true,
                    validate: fn (string $value) => $value !== $password
                        ? 'Password confirmation does not match.'
                        : null
                );
            }

            $this->newLine();
            $this->info('Admin User Summary:');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $name],
                    ['Username', $username],
                    ['Email', $email],
                    ['Password', str_repeat('*', strlen($password))],
                ]
            );

            if (! $this->option('force')) {
                $confirmed = confirm(
                    label: 'Create this admin user?',
                    default: true
                );

                if (! $confirmed) {
                    $this->warn('Admin user creation cancelled.');

                    return Command::FAILURE;
                }
            }

            DB::transaction(function () use ($name, $username, $email, $password) {
                $user = new User;
                $user->name = $name;
                $user->username = $username;
                $user->email = $email;
                $user->password = Hash::make($password);
                $user->email_verified_at = now();
                $user->is_admin = true;
                $user->save();

                $this->info('✅ Admin user created successfully!');
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID', $user->id],
                        ['Name', $user->name],
                        ['Username', $user->username],
                        ['Email', $user->email],
                        ['Created', $user->created_at->format('Y-m-d H:i:s')],
                    ]
                );
            });

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to create admin user: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Validate the name field for CLI arguments
     */
    private function validateNameForCli(string $name): string|bool
    {
        if (strlen($name) < 2) {
            return 'Name must be at least 2 characters.';
        }
        if (strlen($name) > 255) {
            return 'Name must not exceed 255 characters.';
        }

        return true;
    }

    /**
     * Validate the username field for CLI arguments
     */
    private function validateUsernameForCli(string $username): string|bool
    {
        if (strlen($username) < 3) {
            return 'Username must be at least 3 characters.';
        }
        if (strlen($username) > 50) {
            return 'Username must not exceed 50 characters.';
        }
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            return 'Username can only contain letters, numbers, underscores, and hyphens.';
        }
        if (User::where('username', $username)->exists()) {
            return 'Username already exists.';
        }

        return true;
    }

    /**
     * Validate the username field for prompts
     */
    private function validateUsernameForPrompt(string $username): ?string
    {
        if (strlen($username) < 3) {
            return 'Username must be at least 3 characters.';
        }
        if (strlen($username) > 24) {
            return 'Username must not exceed 24 characters.';
        }
        if (! preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
            return 'Username can only contain letters, numbers, underscores, and hyphens.';
        }
        if (User::where('username', $username)->exists()) {
            return 'Username already exists.';
        }

        return null;
    }

    /**
     * Validate the email field for CLI arguments
     */
    private function validateEmailForCli(string $email): string|bool
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return 'Please enter a valid email address.';
        }

        if (User::where('email', $email)->exists()) {
            return 'Email address already exists.';
        }

        return true;
    }

    /**
     * Validate the email field for prompts
     */
    private function validateEmailForPrompt(string $email): ?string
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return 'Please enter a valid email address.';
        }

        if (User::where('email', $email)->exists()) {
            return 'Email address already exists.';
        }

        return null;
    }

    /**
     * Validate the password field for CLI arguments
     */
    private function validatePasswordForCli(string $password): string|bool
    {
        if (strlen($password) < 8) {
            return 'Password must be at least 8 characters.';
        }

        return true;
    }
}
