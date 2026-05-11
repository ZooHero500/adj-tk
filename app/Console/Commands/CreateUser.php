<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating a new user for PornTk...');
        $this->newLine();

        try {
            $username = text(
                label: 'Username',
                placeholder: 'Enter a unique username',
                required: true,
                validate: function (string $value) {
                    if (strlen($value) < 3) {
                        return 'Username must be at least 3 characters.';
                    }
                    if (strlen($value) > 50) {
                        return 'Username must not exceed 50 characters.';
                    }
                    if (! preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
                        return 'Username can only contain letters, numbers, underscores, and hyphens.';
                    }
                    if (User::where('username', $value)->exists()) {
                        return 'Username already exists.';
                    }

                }
            );

            $email = text(
                label: 'Email Address',
                placeholder: 'user@example.com',
                required: true,
                validate: function (string $value) {
                    $validator = Validator::make(['email' => $value], [
                        'email' => 'required|email|max:255',
                    ]);

                    if ($validator->fails()) {
                        return 'Please enter a valid email address.';
                    }

                    if (User::where('email', $value)->exists()) {
                        return 'Email address already exists.';
                    }

                }
            );

            $password = Str::random(30);

            $this->newLine();
            $this->info('User Summary:');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $username],
                    ['Username', $username],
                    ['Email', $email],
                    ['Password', str_repeat('*', strlen($password))],
                ]
            );

            $confirmed = confirm(
                label: 'Create this user?',
                default: true
            );

            if (! $confirmed) {
                $this->warn('User creation cancelled.');

                return Command::FAILURE;
            }

            DB::transaction(function () use ($username, $email, $password) {
                $user = new User;
                $user->name = $username;
                $user->username = $username;
                $user->email = $email;
                $user->password = Hash::make($password);
                $user->email_verified_at = now();
                $user->is_admin = false;
                $user->save();

                $this->info('✅ User created successfully!');
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
            $this->error('Failed to create user: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
