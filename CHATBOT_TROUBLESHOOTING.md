# AI Chatbot Troubleshooting Guide

## Issue: Chatbot Not Responding to Trigger Words

If your chatbot is still giving generic responses instead of specific FAQ answers when using trigger words, follow these troubleshooting steps:

### 1. ✅ Verify Configuration Upload

**Problem**: The updated configuration may not have been properly uploaded to Elfsight.

**Solution**:
1. Run `php setup_ai_chatbot.php` to get the latest configuration
2. Copy the **TRAINING INSTRUCTIONS** section completely
3. Copy all **Q&A pairs** with their expanded keywords
4. In your Elfsight dashboard, **completely replace** the old training data
5. Save and republish your chatbot

### 2. 🔄 Clear Chatbot Cache

**Problem**: Elfsight may be using cached training data.

**Solution**:
1. In Elfsight dashboard, go to your chatbot settings
2. Make a small change (like adding a space to any field)
3. Save the changes
4. Wait 5-10 minutes for the cache to clear
5. Test again

### 3. 📝 Training Data Format

**Problem**: Elfsight may require specific formatting for keywords.

**Solution**:
Ensure your training data includes:
- **Training Instructions** (copy exactly from setup script)
- **Keywords** for each Q&A pair (now includes all trigger words)
- **Complete answers** for each question

### 4. 🎯 Test Specific Keywords

Try these exact phrases to test each FAQ:

**For Rates FAQ**:
- "How much do you charge?"
- "What are your fees?"
- "Is it expensive?"
- "What does it cost?"

**For Services FAQ**:
- "What do you offer?"
- "What services are available?"
- "What can you provide?"

**For Equipment FAQ**:
- "What gear do you have?"
- "What tools do you use?"
- "Do you have good microphones?"

**For Booking FAQ**:
- "How do I schedule?"
- "When can I book?"
- "How do I contact you?"

**For Rescheduling FAQ**:
- "Can I change my appointment?"
- "How do I modify my booking?"
- "Can I cancel?"

### 5. 🔧 Advanced Configuration

**If the issue persists**:

1. **Add Training Instructions to Elfsight**:
   Copy this exact text into your chatbot's "Additional Instructions" field:
   ```
   When users ask about rates, pricing, costs, fees, or money, always provide the specific rates FAQ answer. When users ask about services, what you offer, or what you do, provide the services FAQ answer. When users ask about equipment, gear, instruments, or tools, provide the equipment FAQ answer. When users ask about booking, scheduling, or appointments, provide the booking FAQ answer. When users ask about rescheduling, changing, or modifying appointments, provide the rescheduling FAQ answer.
   ```

2. **Enable Strict Mode** (if available in Elfsight):
   - Look for "Strict FAQ Mode" or similar setting
   - Enable it to force the chatbot to use only your provided answers

3. **Increase Keyword Matching**:
   - In Elfsight settings, look for "Keyword Sensitivity" or "Matching Threshold"
   - Set it to "High" or "Strict"

### 6. 🚀 Final Steps

1. **Test in Incognito Mode**: Use a private/incognito browser window to test
2. **Wait for Propagation**: Changes may take 10-15 minutes to take effect
3. **Check Widget Integration**: Ensure the widget ID is correctly placed in your website
4. **Contact Elfsight Support**: If issues persist, contact Elfsight support with your configuration

### 7. 📊 Expected Behavior

After proper configuration, your chatbot should:
- Recognize keywords like "price", "cost", "rates" → Provide rates FAQ
- Recognize keywords like "services", "offer", "provide" → Provide services FAQ
- Recognize keywords like "equipment", "gear", "tools" → Provide equipment FAQ
- Recognize keywords like "book", "schedule", "appointment" → Provide booking FAQ
- Recognize keywords like "reschedule", "change", "cancel" → Provide rescheduling FAQ

### 8. 🆘 Still Not Working?

If the chatbot still gives generic responses:

1. **Check Elfsight Plan**: Some features may require a paid plan
2. **Verify Widget Status**: Ensure the widget is "Published" not "Draft"
3. **Test Different Browsers**: Try Chrome, Firefox, Safari
4. **Check Console Errors**: Open browser developer tools for JavaScript errors

---

**Need Help?** Run `php setup_ai_chatbot.php` to get the latest configuration data to copy into Elfsight.