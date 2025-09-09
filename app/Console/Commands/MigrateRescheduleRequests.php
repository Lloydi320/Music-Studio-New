<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;
use App\Models\RescheduleRequest;
use App\Models\Booking;
use App\Models\InstrumentRental;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateRescheduleRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:reschedule-requests {--dry-run : Show what would be migrated without actually migrating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing reschedule requests from activity_logs to reschedule_requests table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no data will be migrated');
        }
        
        $this->info('Starting migration of reschedule requests...');
        
        // Find all activity logs that represent reschedule requests
        $rescheduleActivityLogs = ActivityLog::where('description', 'LIKE', 'Reschedule Request Submitted:%')
            ->orWhere('description', 'LIKE', '%reschedule%')
            ->orderBy('created_at', 'asc')
            ->get();
            
        $this->info("Found {$rescheduleActivityLogs->count()} potential reschedule request activity logs");
        
        if ($rescheduleActivityLogs->isEmpty()) {
            $this->info('No reschedule requests found to migrate.');
            return 0;
        }
        
        $migratedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        foreach ($rescheduleActivityLogs as $activityLog) {
            try {
                // Skip if this activity log has already been migrated
                $existingRequest = RescheduleRequest::where('original_activity_log_id', $activityLog->id)->first();
                if ($existingRequest) {
                    $this->line("Skipping activity log {$activityLog->id} - already migrated");
                    $skippedCount++;
                    continue;
                }
                
                // Only process actual reschedule request submissions
                if (!str_contains($activityLog->description, 'Reschedule Request Submitted:')) {
                    $this->line("Skipping activity log {$activityLog->id} - not a reschedule request submission");
                    $skippedCount++;
                    continue;
                }
                
                if ($dryRun) {
                    $this->line("Would migrate activity log {$activityLog->id}: {$activityLog->description}");
                    $migratedCount++;
                    continue;
                }
                
                $rescheduleRequest = $this->migrateActivityLog($activityLog);
                
                if ($rescheduleRequest) {
                    $this->line("✓ Migrated activity log {$activityLog->id} to reschedule request {$rescheduleRequest->id}");
                    $migratedCount++;
                } else {
                    $this->error("✗ Failed to migrate activity log {$activityLog->id}");
                    $errorCount++;
                }
                
            } catch (\Exception $e) {
                $this->error("✗ Error migrating activity log {$activityLog->id}: {$e->getMessage()}");
                Log::error('Reschedule request migration error', [
                    'activity_log_id' => $activityLog->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $errorCount++;
            }
        }
        
        $this->info("\nMigration Summary:");
        $this->info("Migrated: {$migratedCount}");
        $this->info("Skipped: {$skippedCount}");
        $this->info("Errors: {$errorCount}");
        
        if ($dryRun) {
            $this->info('\nThis was a dry run. Run without --dry-run to perform the actual migration.');
        }
        
        return 0;
    }
    
    /**
     * Migrate a single activity log to a reschedule request
     */
    private function migrateActivityLog(ActivityLog $activityLog): ?RescheduleRequest
    {
        DB::beginTransaction();
        
        try {
            $rescheduleData = $activityLog->new_values ?? [];
            $originalData = $activityLog->old_values ?? [];
            
            // Determine resource type and get the resource
            $resourceType = $activityLog->resource_type;
            $resourceId = $activityLog->resource_id;
            
            // Normalize resource type
            if ($resourceType === 'App\\Models\\Booking' || $resourceType === 'Booking') {
                $resourceType = RescheduleRequest::RESOURCE_BOOKING;
                $resource = Booking::find($resourceId);
            } elseif ($resourceType === 'App\\Models\\InstrumentRental' || $resourceType === 'InstrumentRental') {
                $resourceType = RescheduleRequest::RESOURCE_INSTRUMENT_RENTAL;
                $resource = InstrumentRental::find($resourceId);
            } else {
                throw new \Exception("Unknown resource type: {$resourceType}");
            }
            
            if (!$resource) {
                throw new \Exception("Resource not found: {$resourceType} #{$resourceId}");
            }
            
            // Create the reschedule request
            $rescheduleRequest = new RescheduleRequest();
            $rescheduleRequest->resource_type = $resourceType;
            $rescheduleRequest->resource_id = $resourceId;
            $rescheduleRequest->user_id = $activityLog->user_id ?? $resource->user_id;
            $rescheduleRequest->original_activity_log_id = $activityLog->id;
            
            // Set customer information
            if ($resourceType === RescheduleRequest::RESOURCE_BOOKING) {
                $rescheduleRequest->customer_name = $resource->customer_name ?? $resource->band_name;
                $rescheduleRequest->customer_email = $resource->email ?? $resource->user->email ?? null;
                $rescheduleRequest->original_date = $resource->date;
                $rescheduleRequest->original_time_slot = $resource->time_slot;
                $rescheduleRequest->original_duration = $resource->duration;
                
                // Extract requested changes from reschedule data
                $rescheduleRequest->requested_date = $rescheduleData['new_date'] ?? $rescheduleData['date'] ?? null;
                $rescheduleRequest->requested_time_slot = $rescheduleData['new_time_slot'] ?? $rescheduleData['time_slot'] ?? null;
                $rescheduleRequest->requested_duration = $rescheduleData['new_duration'] ?? $rescheduleData['duration'] ?? null;
                
            } elseif ($resourceType === RescheduleRequest::RESOURCE_INSTRUMENT_RENTAL) {
                $rescheduleRequest->customer_name = $resource->customer_name;
                $rescheduleRequest->customer_email = $resource->customer_email ?? $resource->user->email ?? null;
                $rescheduleRequest->original_start_date = $resource->rental_start_date;
                $rescheduleRequest->original_end_date = $resource->rental_end_date;
                
                // Extract requested changes from reschedule data
                $rescheduleRequest->requested_start_date = $rescheduleData['new_start_date'] ?? $rescheduleData['rental_start_date'] ?? null;
                $rescheduleRequest->requested_end_date = $rescheduleData['new_end_date'] ?? $rescheduleData['rental_end_date'] ?? null;
                $rescheduleRequest->requested_duration = $rescheduleData['new_duration'] ?? $rescheduleData['rental_duration_days'] ?? null;
            }
            
            // Set additional fields
            $rescheduleRequest->reason = $rescheduleData['reason'] ?? 'Migrated from activity log';
            $rescheduleRequest->status = RescheduleRequest::STATUS_PENDING; // Default to pending
            $rescheduleRequest->priority = RescheduleRequest::PRIORITY_MEDIUM;
            $rescheduleRequest->original_data = $originalData;
            $rescheduleRequest->requested_data = $rescheduleData;
            
            // Set timestamps to match the original activity log
            $rescheduleRequest->created_at = $activityLog->created_at;
            $rescheduleRequest->updated_at = $activityLog->updated_at;
            
            $rescheduleRequest->save();
            
            DB::commit();
            
            return $rescheduleRequest;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}