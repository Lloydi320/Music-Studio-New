<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DatabaseShowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:show-fixed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show database information without performance_schema dependency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = DB::connection();
        $config = Config::get('database.connections.' . Config::get('database.default'));
        
        $this->info('Database Information:');
        $this->line('');
        
        // Basic connection info
        $this->line('Connection: ' . Config::get('database.default'));
        $this->line('Driver: ' . $config['driver']);
        $this->line('Host: ' . $config['host']);
        $this->line('Port: ' . $config['port']);
        $this->line('Database: ' . $config['database']);
        $this->line('Username: ' . $config['username']);
        $this->line('');
        
        try {
            // Get MySQL version without using performance_schema
            $version = DB::select('SELECT VERSION() as version')[0]->version;
            $this->line('MySQL Version: ' . $version);
            
            // Get basic database info
            $tables = DB::select('SHOW TABLES');
            $this->line('Total Tables: ' . count($tables));
            
            // Get connection status using SHOW STATUS instead of performance_schema
            try {
                $status = DB::select("SHOW STATUS LIKE 'Threads_connected'");
                if (!empty($status)) {
                    $this->line('Active Connections: ' . $status[0]->Value);
                }
            } catch (\Exception $e) {
                $this->line('Active Connections: Unable to retrieve (non-critical)');
            }
            
            $this->line('');
            $this->info('✅ Database connection is working properly!');
            $this->line('Note: This command bypasses performance_schema compatibility issues.');
            
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}