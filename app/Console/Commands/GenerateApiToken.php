<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-api-token {email} {password} {--create=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API Token for a user with email, if create option is passed, create the user if it does not exist.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userEmail = $this->argument('email');

        $user = User::where('email', $userEmail)->first();

        $userName = $this->option('create');

        if ((null === $user) && ! $userName) {
            $this->error('User does not exist, use --create option to create the user.');

            return 1;
        }

        if ($user && ! Hash::check($this->argument('password'), $user->password)) {
            $this->error('Password does not match.');

            return 1;
        }


        if (null === $user) {
            $user = User::create([
                'name' => $userName,
                'email' => $userEmail,
                'password' => Hash::make($this->argument('password')),
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        $this->info("API Token for user {$userEmail} is {$token} User ID is {$user->id}");

        return 0;
    }
}
