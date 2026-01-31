<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class PromoteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('PROMOTE_SUPER_ADMIN_EMAIL');

        if (!$email) {
            $this->command->info('Please set PROMOTE_SUPER_ADMIN_EMAIL in your .env (example: PROMOTE_SUPER_ADMIN_EMAIL=you@example.com) and re-run this seeder.');
            return;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->command->info("User with email {$email} not found.");
            return;
        }

        $user->role = 'super_admin';
        $user->save();

        $this->command->info("User {$user->email} promoted to super_admin.");
    }
}
