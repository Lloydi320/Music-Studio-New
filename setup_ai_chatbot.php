<?php

/**
 * Lemon Hub Studio - AI Chatbot Setup Script
 * This script helps you set up the Elfsight AI Chatbot with pre-configured settings
 */

echo "\n🎵 LEMON HUB STUDIO - AI CHATBOT SETUP\n";
echo "=====================================\n\n";

// Load the chatbot configuration
$configFile = __DIR__ . '/elfsight_chatbot_config.json';
if (!file_exists($configFile)) {
    echo "❌ Configuration file not found: $configFile\n";
    exit(1);
}

$config = json_decode(file_get_contents($configFile), true);
if (!$config) {
    echo "❌ Invalid configuration file\n";
    exit(1);
}

echo "✅ Configuration loaded successfully\n\n";

// Display setup instructions
echo "📋 SETUP INSTRUCTIONS:\n";
echo "=====================\n\n";

echo "1. 🌐 CREATE ELFSIGHT ACCOUNT:\n";
echo "   - Visit: https://elfsight.com/ai-chatbot-widget/\n";
echo "   - Click 'Create Widget' or 'Get Started'\n";
echo "   - Sign up for free account\n\n";

echo "2. 🤖 CONFIGURE YOUR CHATBOT:\n";
echo "   Business Name: {$config['business_info']['name']}\n";
echo "   Business Type: {$config['business_info']['type']}\n";
echo "   Description: {$config['business_info']['description']}\n";
echo "   Welcome Message: {$config['greeting_messages']['welcome']}\n";
echo "   Fallback Message: {$config['greeting_messages']['fallback']}\n\n";

echo "3. 📚 TRAINING DATA (Copy & Paste into Elfsight):\n";
echo "   ================================================\n\n";

// Display training instructions
if (isset($config['training_data']['instructions'])) {
    echo "📋 TRAINING INSTRUCTIONS:\n";
    echo "   " . $config['training_data']['instructions'] . "\n\n";
}

foreach ($config['training_data']['qa_pairs'] as $index => $qa) {
    echo "   Q" . ($index + 1) . ": {$qa['question']}\n";
    echo "   A" . ($index + 1) . ": {$qa['answer']}\n";
    if (isset($qa['keywords'])) {
        echo "   Keywords: " . implode(', ', $qa['keywords']) . "\n";
        echo "   (Keywords help the AI recognize when users are asking about this topic)\n";
    }
    echo "\n";
}

echo "4. 🎨 APPEARANCE SETTINGS (Configure in Elfsight Dashboard):\n";
echo "   - Choose your preferred primary color\n";
echo "   - Set chatbot position (bottom-right recommended)\n";
echo "   - Select appropriate size (medium recommended)\n";
echo "   - Enable smooth animations\n\n";

echo "5. ⚙️ QUICK REPLIES (Add these to your chatbot):\n";
foreach ($config['quick_replies'] as $index => $reply) {
    echo "   - $reply\n";
}
echo "\n";

echo "6. 🔧 ADDITIONAL INSTRUCTIONS:\n";
echo "   - Always be friendly and professional\n";
echo "   - Provide accurate information about studio services\n";
echo "   - Direct complex queries to human staff\n";
echo "   - Encourage bookings through available channels\n";
echo "\n";

echo "7. 📧 RECOMMENDED SETTINGS:\n";
echo "   - Enable Email Transcripts: Yes\n";
echo "   - Language: English\n";
echo "   - Timezone: Your local timezone\n";
echo "   - Enable offline messages\n\n";

echo "8. 🚀 FINAL STEPS:\n";
echo "   - After configuring in Elfsight, copy your Widget ID\n";
echo "   - Replace 'YOUR_WIDGET_ID_HERE' in these files:\n";
echo "     * resources/views/home.blade.php\n";
echo "     * resources/views/layouts/app.blade.php\n";
echo "   - Test the chatbot on your website\n\n";

// Check if widget code is already in place
$homeFile = __DIR__ . '/resources/views/home.blade.php';
$layoutFile = __DIR__ . '/resources/views/layouts/app.blade.php';

echo "📁 FILE STATUS CHECK:\n";
echo "====================\n";

if (file_exists($homeFile)) {
    $homeContent = file_get_contents($homeFile);
    if (strpos($homeContent, 'elfsight-app-8126d948-90e0-4f43-b92d-523c11148e8a') !== false) {
        echo "✅ Home page: Widget code found (ID: 8126d948-90e0-4f43-b92d-523c11148e8a)\n";
    } elseif (strpos($homeContent, 'elfsight-app-ai-chatbot') !== false) {
        echo "✅ Home page: Widget code found\n";
        if (strpos($homeContent, 'YOUR_WIDGET_ID_HERE') !== false) {
            echo "⚠️  Home page: Widget ID needs to be replaced\n";
        } else {
            echo "✅ Home page: Widget ID appears to be set\n";
        }
    } else {
        echo "❌ Home page: Widget code not found\n";
    }
} else {
    echo "❌ Home page file not found\n";
}

if (file_exists($layoutFile)) {
    $layoutContent = file_get_contents($layoutFile);
    if (strpos($layoutContent, 'elfsight-app-8126d948-90e0-4f43-b92d-523c11148e8a') !== false) {
        echo "✅ Layout file: Widget code found (ID: 8126d948-90e0-4f43-b92d-523c11148e8a)\n";
    } elseif (strpos($layoutContent, 'elfsight-app-ai-chatbot') !== false) {
        echo "✅ Layout file: Widget code found\n";
        if (strpos($layoutContent, 'YOUR_WIDGET_ID_HERE') !== false) {
            echo "⚠️  Layout file: Widget ID needs to be replaced\n";
        } else {
            echo "✅ Layout file: Widget ID appears to be set\n";
        }
    } else {
        echo "❌ Layout file: Widget code not found\n";
    }
} else {
    echo "❌ Layout file not found\n";
}

echo "\n";

// Offer to update widget ID if provided
echo "💡 WIDGET ID UPDATE:\n";
echo "===================\n";
echo "If you have your Elfsight Widget ID, you can update it now.\n";
echo "Enter your Widget ID (or press Enter to skip): ";

$widgetId = trim(fgets(STDIN));

if (!empty($widgetId)) {
    $updated = false;
    
    // Update home.blade.php
    if (file_exists($homeFile)) {
        $homeContent = file_get_contents($homeFile);
        $newHomeContent = str_replace('YOUR_WIDGET_ID_HERE', $widgetId, $homeContent);
        if ($newHomeContent !== $homeContent) {
            file_put_contents($homeFile, $newHomeContent);
            echo "✅ Updated Widget ID in home.blade.php\n";
            $updated = true;
        }
    }
    
    // Update app.blade.php
    if (file_exists($layoutFile)) {
        $layoutContent = file_get_contents($layoutFile);
        $newLayoutContent = str_replace('YOUR_WIDGET_ID_HERE', $widgetId, $layoutContent);
        if ($newLayoutContent !== $layoutContent) {
            file_put_contents($layoutFile, $newLayoutContent);
            echo "✅ Updated Widget ID in app.blade.php\n";
            $updated = true;
        }
    }
    
    if ($updated) {
        echo "\n🎉 SUCCESS! Your AI Chatbot is now configured and ready to use!\n";
        echo "Visit your website to see the chatbot in action.\n\n";
    } else {
        echo "⚠️  No files were updated. Please check if the widget code is properly installed.\n\n";
    }
} else {
    echo "⏭️  Skipped Widget ID update. You can manually replace 'YOUR_WIDGET_ID_HERE' later.\n\n";
}

echo "📖 HELPFUL RESOURCES:\n";
echo "=====================\n";
echo "- Elfsight AI Chatbot: https://elfsight.com/ai-chatbot-widget/\n";
echo "- Setup Guide: AI_CHATBOT_SETUP_GUIDE.md\n";
echo "- Configuration: elfsight_chatbot_config.json\n\n";

echo "🎵 Your Lemon Hub Studio AI Assistant is ready to help your customers 24/7!\n";
echo "The chatbot will assist with bookings, answer questions about your services,\n";
echo "and provide professional support to enhance your customer experience.\n\n";

echo "✨ Happy music making! ✨\n\n";

?>