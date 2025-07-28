<?php

// Check what database phpMyAdmin might be connecting to
try {
    // Connect using the same credentials as Laravel
    $host = '127.0.0.1';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Available Databases ===\n";
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($databases as $db) {
        echo "- $db\n";
        
        // Check if this database has our tables
        try {
            $pdo->exec("USE `$db`");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            $hasBookings = in_array('bookings', $tables);
            $hasFeedback = in_array('feedback', $tables);
            
            if ($hasBookings || $hasFeedback) {
                echo "  → Contains our tables: ";
                if ($hasBookings) echo "bookings ";
                if ($hasFeedback) echo "feedback ";
                echo "\n";
                
                if ($hasBookings) {
                    $count = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
                    echo "    Bookings count: $count\n";
                }
                
                if ($hasFeedback) {
                    $count = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
                    echo "    Feedback count: $count\n";
                }
            }
        } catch (Exception $e) {
            // Skip databases we can't access
        }
    }
    
    echo "\n=== Checking music_studio_new specifically ===\n";
    $pdo->exec("USE music_studio_new");
    
    $bookingCount = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    $feedbackCount = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
    
    echo "Bookings in music_studio_new: $bookingCount\n";
    echo "Feedback in music_studio_new: $feedbackCount\n";
    
    if ($bookingCount > 0) {
        echo "\nRecent bookings:\n";
        $stmt = $pdo->query("SELECT id, reference, date, time_slot, status FROM bookings ORDER BY created_at DESC LIMIT 3");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- ID: {$row['id']}, Ref: {$row['reference']}, Date: {$row['date']}, Status: {$row['status']}\n";
        }
    }
    
    if ($feedbackCount > 0) {
        echo "\nRecent feedback:\n";
        $stmt = $pdo->query("SELECT id, name, rating, comment FROM feedback ORDER BY created_at DESC LIMIT 3");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- ID: {$row['id']}, Name: {$row['name']}, Rating: {$row['rating']}\n";
        }
    }
    
    echo "\n=== phpMyAdmin Troubleshooting ===\n";
    echo "1. Make sure phpMyAdmin is using the same connection:\n";
    echo "   - Host: 127.0.0.1 (or localhost)\n";
    echo "   - Username: root\n";
    echo "   - Password: (empty)\n";
    echo "   - Database: music_studio_new\n";
    echo "\n2. Try refreshing phpMyAdmin or clearing browser cache\n";
    echo "3. Check if phpMyAdmin is connecting to a different MySQL instance\n";
    echo "4. Verify the URL: http://localhost/phpmyadmin\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}