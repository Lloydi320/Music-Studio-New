<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->info('No users found in the system.');
            return 0;
        }
        
        $this->info('Users in the system:');
        $this->line('');
        
        foreach ($users as $user) {
            $adminStatus = $user->is_admin ? 'Admin' : 'User';
            $this->line("- {$user->name} ({$user->email}) - {$adminStatus}");
        }
        
        $this->line('');
        $this->info('Total users: ' . $users->count());
        $this->info('Admin users: ' . $users->where('is_admin', true)->count());
        
        return 0;
    }
}