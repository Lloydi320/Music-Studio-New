<?php
/**
 * Elfsight Training Data Upload Helper
 * Automatically formats your JSON config for easy Elfsight dashboard upload
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

echo "\n🚀 ELFSIGHT TRAINING DATA UPLOAD HELPER\n";
echo "==========================================\n\n";

echo "📋 STEP 1: COPY TRAINING INSTRUCTIONS\n";
echo "------------------------------------\n";
echo $config['training_data']['instructions'] . "\n\n";

echo "📝 STEP 2: COPY ALL Q&A PAIRS\n";
echo "----------------------------\n";

$qaCount = 1;
foreach ($config['training_data']['qa_pairs'] as $qa) {
    echo "Q{$qaCount}: {$qa['question']}\n";
    echo "A{$qaCount}: {$qa['answer']}\n\n";
    $qaCount++;
}

echo "⚙️ STEP 3: BUSINESS INFORMATION\n";
echo "------------------------------\n";
echo "Name: {$config['business_info']['name']}\n";
echo "Type: {$config['business_info']['type']}\n";
echo "Description: {$config['business_info']['description']}\n\n";

echo "💬 STEP 4: GREETING MESSAGES\n";
echo "---------------------------\n";
echo "Welcome: {$config['greeting_messages']['welcome']}\n";
echo "Fallback: {$config['greeting_messages']['fallback']}\n\n";

echo "⚡ STEP 5: QUICK REPLIES\n";
echo "----------------------\n";
foreach ($config['quick_replies'] as $reply) {
    echo "- $reply\n";
}

echo "\n✅ UPLOAD CHECKLIST:\n";
echo "===================\n";
echo "□ 1. Go to elfsight.com and login\n";
echo "□ 2. Find your AI Chatbot widget (ID: 8126d948-90e0-4f43-b92d-523c11148e8a)\n";
echo "□ 3. Delete any existing training data\n";
echo "□ 4. Copy training instructions from STEP 1\n";
echo "□ 5. Add all " . count($config['training_data']['qa_pairs']) . " Q&A pairs from STEP 2\n";
echo "□ 6. Set business info from STEP 3\n";
echo "□ 7. Set greeting messages from STEP 4\n";
echo "□ 8. Add quick replies from STEP 5\n";
echo "□ 9. Save and publish changes\n";
echo "□ 10. Wait 15 minutes for changes to take effect\n";
echo "□ 11. Test with 'rates' to verify it works\n\n";

echo "🎯 EXPECTED RESULT:\n";
echo "When you type 'rates', chatbot should respond:\n";
echo "Our rates depend on the service:\n";
echo "- Recording: P1,000/hour\n";
echo "- Studio rental: P250/hour\n\n";

echo "📞 NEED HELP? Check these files:\n";
echo "- ELFSIGHT_TRAINING_DATA_UPLOAD.md\n";
echo "- ELFSIGHT_SINGLE_WORD_FIX.md\n";
echo "- CHATBOT_TROUBLESHOOTING.md\n\n";

echo "✨ Your training data is ready for upload!\n";
?>