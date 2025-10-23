<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            if ($existingUser->is_admin) {
                $this->info("User '{$existingUser->name}' ({$email}) is already an admin.");
                return 0;
            } else {
                // Make existing user admin
                $existingUser->update(['is_admin' => true]);
                $this->info("Existing user '{$existingUser->name}' ({$email}) has been made an admin!");
                return 0;
            }
        }
        
        try {
            // Create new admin user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now()
            ]);
            
            $this->info("✅ Admin user created successfully!");
            $this->info("👤 Name: {$user->name}");
            $this->info("📧 Email: {$user->email}");
            $this->info("🔑 Admin: Yes");
            $this->info("\n🎯 You can now login with these credentials and access the admin panel at /admin/dashboard");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create admin user: " . $e->getMessage());
            return 1;
        }
    }
}