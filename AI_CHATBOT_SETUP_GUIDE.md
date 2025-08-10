# AI Chatbot Setup Guide - Lemon Hub Studio

## Overview
This guide will help you set up the Elfsight AI Chatbot widget for your music studio website. The chatbot will provide 24/7 customer support, answer common questions, and help with booking inquiries.

## Step 1: Create Your Elfsight Account

1. Visit [https://elfsight.com/ai-chatbot-widget/html/](https://elfsight.com/ai-chatbot-widget/html/)
2. Click "Create Widget" or "Get Started"
3. Sign up for a free account or choose a paid plan
4. Select the AI Chatbot widget

## Step 2: Configure Your Chatbot

### Basic Settings
1. **Widget Name**: "Lemon Hub Studio Assistant"
2. **Greeting Message**: "Hi! I'm your Lemon Hub Studio assistant. How can I help you today? 🎵"
3. **Placeholder Text**: "Ask about our services, booking, or anything else..."

### Training Your Chatbot
In the "Training" tab, add the following information:

#### Studio Services Q&A
```
Q: What services do you offer?
A: We offer professional music recording, mixing and mastering, music lessons, band rehearsal space, and instrument rentals.

Q: How do I book a session?
A: You can book a session by logging in with your Google account and selecting your preferred date and time from our booking calendar.

Q: What are your rates?
A: Please contact us directly for current pricing information. Rates vary depending on the service and session length.

Q: What instruments do you have available?
A: We have a full range of professional recording equipment, guitars, bass, drums, keyboards, and various other instruments available for use and rental.

Q: Do you offer music lessons?
A: Yes! We offer individual and group music lessons for various instruments and skill levels.

Q: What are your studio hours?
A: Our studio operates by appointment. Please check our booking calendar for available time slots.

Q: Can I cancel or reschedule my booking?
A: Yes, you can manage your bookings through your account. Please give us advance notice when possible.

Q: Do you provide mixing and mastering services?
A: Absolutely! We offer professional mixing and mastering services for all genres of music.
```

#### Contact Information
```
Q: How can I contact you?
A: You can reach us through:
- Facebook: Lemon Hub Studio (https://www.facebook.com/lemonhubstudio)
- TikTok: @lemonhubstudio
- Email: Contact us through our website
- Or use this chat for immediate assistance!
```

### Appearance Customization
1. **Primary Color**: #ff6b35 (matches your website theme)
2. **Position**: Bottom right corner
3. **Size**: Medium
4. **Animation**: Slide up

## Step 3: Get Your Widget ID

1. After configuring your chatbot, click "Save" or "Publish"
2. Copy the widget ID from the embed code
3. The embed code will look like:
   ```html
   <div class="elfsight-app-ai-chatbot" data-elfsight-app-ai-chatbot-id="YOUR_WIDGET_ID"></div>
   ```

## Step 4: Update Your Website

**The widget code has already been added to your website files:**
- `resources/views/home.blade.php` (main website)
- `resources/views/layouts/app.blade.php` (admin panel)

**To activate the chatbot:**
1. Replace `YOUR_WIDGET_ID_HERE` with your actual widget ID in both files
2. The widget will appear on all pages of your website

## Step 5: Advanced Training (Optional)

For better performance, you can:

1. **Upload Documents**: Add your service brochures, FAQ documents, or pricing sheets
2. **Website Content**: Let the AI learn from your website content
3. **Chat History**: Review and improve responses based on actual customer interactions

## Step 6: Monitor and Improve

1. **Analytics**: Check your Elfsight dashboard for chat statistics
2. **Email Reports**: Enable email notifications for chat transcripts
3. **Regular Updates**: Update the training data based on new services or common questions

## Pricing Information

- **Free Plan**: Limited features, Elfsight branding
- **Paid Plans**: Start from $5/month, remove branding, unlimited chats
- **14-day money-back guarantee**

## Benefits for Your Music Studio

✅ **24/7 Customer Support**: Answer questions even when you're not available
✅ **Lead Generation**: Capture potential customer information
✅ **Booking Assistance**: Help customers navigate the booking process
✅ **Reduced Workload**: Automate common inquiries
✅ **Professional Image**: Show customers you're tech-savvy and accessible

## Troubleshooting

If the chatbot doesn't appear:
1. Check that you've replaced `YOUR_WIDGET_ID_HERE` with your actual widget ID
2. Ensure your Elfsight account is active
3. Clear your browser cache
4. Check browser console for any JavaScript errors

## Support

For technical issues:
- Elfsight Support: Available through your dashboard
- Website Issues: Check the Laravel logs in `storage/logs/`

---

**Note**: The AI chatbot will enhance your customer service but should complement, not replace, personal interaction with your clients. Regular monitoring and updates will ensure the best customer experience.