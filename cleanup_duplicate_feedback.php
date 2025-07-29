<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Feedback;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧹 Cleaning up duplicate feedback entries...\n";

try {
    // Get all feedback entries
    $feedbacks = Feedback::orderBy('created_at', 'desc')->get();
    
    echo "📊 Found " . $feedbacks->count() . " total feedback entries\n";
    
    // Group by name, rating, comment, and created_at (within 1 minute)
    $duplicates = [];
    $toDelete = [];
    
    foreach ($feedbacks as $feedback) {
        $key = $feedback->name . '|' . $feedback->rating . '|' . $feedback->comment . '|' . $feedback->user_id;
        
        if (!isset($duplicates[$key])) {
            $duplicates[$key] = [];
        }
        
        $duplicates[$key][] = $feedback;
    }
    
    // Find duplicates (keep the first one, delete the rest)
    foreach ($duplicates as $key => $group) {
        if (count($group) > 1) {
            echo "🔍 Found " . count($group) . " duplicates for: " . substr($key, 0, 50) . "...\n";
            
            // Keep the first one (most recent), delete the rest
            $first = array_shift($group);
            foreach ($group as $duplicate) {
                $toDelete[] = $duplicate->id;
                echo "   🗑️  Will delete ID: " . $duplicate->id . " (created: " . $duplicate->created_at . ")\n";
            }
        }
    }
    
    if (empty($toDelete)) {
        echo "✅ No duplicate feedback entries found!\n";
    } else {
        echo "🗑️  Deleting " . count($toDelete) . " duplicate entries...\n";
        
        // Delete the duplicates
        $deleted = Feedback::whereIn('id', $toDelete)->delete();
        
        echo "✅ Successfully deleted " . $deleted . " duplicate feedback entries\n";
        echo "📊 Remaining feedback entries: " . Feedback::count() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "🎉 Cleanup complete!\n"; 