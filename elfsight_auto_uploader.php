<?php
/**
 * Elfsight Auto Uploader - Complete Training Data Setup
 * This script provides everything you need to upload to Elfsight dashboard
 */

// Read the chatbot configuration
$configFile = 'elfsight_chatbot_config.json';
if (!file_exists($configFile)) {
    die("❌ Config file not found: $configFile\n");
}

$config = json_decode(file_get_contents($configFile), true);
if (!$config) {
    die("❌ Invalid JSON in config file\n");
}

echo "\n🤖 ELFSIGHT AUTO UPLOADER - COMPLETE SETUP\n";
echo "============================================\n\n";

echo "🎯 WIDGET INFO:\n";
echo "Widget ID: 8126d948-90e0-4f43-b92d-523c11148e8a\n";
echo "Dashboard: https://elfsight.com/dashboard\n\n";

echo "🔥 QUICK ACCESS LINKS:\n";
echo "1. Login: https://elfsight.com/login\n";
echo "2. Dashboard: https://elfsight.com/dashboard\n";
echo "3. Find your AI Chatbot widget\n";
echo "4. Click 'Edit' or 'Configure'\n\n";

echo "⚠️  CRITICAL FIRST STEP: DELETE ALL EXISTING DATA!\n";
echo "================================================\n";
echo "Before adding new data, you MUST:\n";
echo "- Delete all existing Q&A pairs\n";
echo "- Clear all training instructions\n";
echo "- Remove old business info\n";
echo "- Start completely fresh\n\n";

echo "📋 SECTION 1: TRAINING INSTRUCTIONS\n";
echo "==================================\n";
echo "Copy this EXACT text into 'Instructions' field:\n\n";
echo "```\n";
echo $config['training_data']['instructions'];
echo "\n```\n\n";

echo "📝 SECTION 2: Q&A PAIRS (ALL 10 PAIRS)\n";
echo "====================================\n";
echo "Add these Q&A pairs ONE BY ONE:\n\n";

$qaCount = 1;
foreach ($config['training_data']['qa_pairs'] as $qa) {
    echo "--- Q&A PAIR #{$qaCount} ---\n";
    echo "QUESTION: {$qa['question']}\n";
    echo "ANSWER:\n{$qa['answer']}\n\n";
    $qaCount++;
}

echo "⚙️  SECTION 3: BUSINESS INFORMATION\n";
echo "==================================\n";
echo "Business Name: {$config['business_info']['name']}\n";
echo "Business Type: {$config['business_info']['type']}\n";
echo "Description: {$config['business_info']['description']}\n\n";

echo "💬 SECTION 4: GREETING MESSAGES\n";
echo "==============================\n";
echo "Welcome Message:\n{$config['greeting_messages']['welcome']}\n\n";
echo "Fallback Message:\n{$config['greeting_messages']['fallback']}\n\n";

echo "⚡ SECTION 5: QUICK REPLIES\n";
echo "=========================\n";
echo "Add these quick reply buttons:\n";
foreach ($config['quick_replies'] as $reply) {
    echo "- $reply\n";
}

echo "\n✅ UPLOAD CHECKLIST - FOLLOW EXACTLY:\n";
echo "====================================\n";
echo "□ 1. Go to https://elfsight.com and login\n";
echo "□ 2. Find AI Chatbot widget (ID: 8126d948-90e0-4f43-b92d-523c11148e8a)\n";
echo "□ 3. Click 'Edit' or 'Configure'\n";
echo "□ 4. DELETE ALL EXISTING DATA FIRST!\n";
echo "□ 5. Copy training instructions from SECTION 1\n";
echo "□ 6. Add all 10 Q&A pairs from SECTION 2 (one by one)\n";
echo "□ 7. Set business info from SECTION 3\n";
echo "□ 8. Set greeting messages from SECTION 4\n";
echo "□ 9. Add quick replies from SECTION 5\n";
echo "□ 10. SAVE all changes\n";
echo "□ 11. PUBLISH the widget\n";
echo "□ 12. Wait exactly 15 minutes\n";
echo "□ 13. Test with single word 'rates'\n\n";

echo "🧪 TESTING PROTOCOL:\n";
echo "===================\n";
echo "After 15 minutes, test these single words:\n";
echo "- Type: 'rates' → Should show pricing info\n";
echo "- Type: 'services' → Should show services list\n";
echo "- Type: 'book' → Should show booking info\n";
echo "- Type: 'price' → Should show pricing info\n";
echo "- Type: 'cost' → Should show pricing info\n\n";

echo "🎯 EXPECTED SUCCESS RESULT:\n";
echo "==========================\n";
echo "When you type 'rates', chatbot should respond:\n";
echo "\"Our rates depend on the service:\n";
echo "- Recording: P1,000/hour\n";
echo "- Studio rental: P250/hour\"\n\n";

echo "❌ FAILURE INDICATORS:\n";
echo "=====================\n";
echo "If chatbot still shows:\n";
echo "- Generic responses\n";
echo "- Echoes the word back\n";
echo "- Says 'I don't understand'\n";
echo "→ Double-check all Q&A pairs were added correctly\n\n";

echo "🚨 TROUBLESHOOTING:\n";
echo "==================\n";
echo "If it's still not working:\n";
echo "1. Verify ALL 10 Q&A pairs are added\n";
echo "2. Check for typos in questions/answers\n";
echo "3. Ensure training instructions are copied exactly\n";
echo "4. Wait the full 15 minutes after publishing\n";
echo "5. Clear browser cache and try again\n\n";

echo "📞 SUPPORT FILES:\n";
echo "================\n";
echo "- ELFSIGHT_MANUAL_UPLOAD_GUIDE.md (detailed steps)\n";
echo "- ELFSIGHT_SINGLE_WORD_FIX.md (technical explanation)\n";
echo "- CHATBOT_TROUBLESHOOTING.md (common issues)\n\n";

echo "🎵 SUCCESS MESSAGE:\n";
echo "==================\n";
echo "Once working, your Lemon Hub Studio AI assistant will:\n";
echo "✅ Respond to single words like 'rates', 'services', 'book'\n";
echo "✅ Provide 24/7 professional customer support\n";
echo "✅ Help customers with bookings and inquiries\n";
echo "✅ Enhance your studio's customer experience\n\n";

echo "🚀 YOU'RE ALL SET! Go upload this data to Elfsight now!\n";
echo "Happy music making! ✨\n";
?>