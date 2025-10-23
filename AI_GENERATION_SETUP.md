# 🤖 AI Event Generation - Setup Guide

## ✨ Features Implemented

### 1. **Automatic Event Description Generation**
- Click "Generate with AI" button next to the description field
- AI creates engaging, professional event descriptions based on:
  - Event title
  - Event type (workshop, conference, cleanup, etc.)
  - Location (optional)
- Descriptions are tailored for environmental/sustainability themes
- 150-200 words, 2-3 paragraphs
- Action-oriented and inspiring tone

### 2. **Automatic FAQ Generation**
- Automatically triggered after accepting a generated description
- Creates 5 relevant Q&A pairs covering:
  - Timing and duration
  - Materials needed
  - Prerequisites
  - Registration process
  - Location details
- Displayed in a beautiful modal for review

## 🛠️ Setup Instructions

### Step 1: Get Your Gemini API Key

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the generated key

### Step 2: Configure the API Key

Open your `.env` file and replace the placeholder:

```env
GEMINI_API_KEY=your_actual_api_key_here
```

**Example:**
```env
GEMINI_API_KEY=AIzaSyC1234567890abcdefghijklmnopqrstuvwxyz
```

### Step 3: Test the Feature

1. Navigate to Create Event page: `http://127.0.0.1:8000/events/create`
2. Fill in:
   - Event Title (e.g., "Community Recycling Workshop")
   - Event Type (e.g., "Workshop")
   - Location (optional, e.g., "Central Park")
3. Click "**✨ Generate with AI**" button
4. Wait 2-3 seconds for AI to generate
5. Review the description in the modal
6. Click "**Use This Description**" to insert it
7. View the auto-generated FAQ

## 📁 Files Created/Modified

### New Files:
- `app/Services/GeminiService.php` - AI service class
- `AI_GENERATION_SETUP.md` - This documentation

### Modified Files:
- `.env` - Added GEMINI_API_KEY
- `config/services.php` - Added Gemini configuration
- `app/Http/Controllers/CommunityEventController.php` - Added AI methods
- `routes/web.php` - Added AI generation routes
- `resources/views/events/create.blade.php` - Added UI and JavaScript

## 🔧 Technical Details

### API Endpoints Created:

```php
POST /events/ai/generate-description
POST /events/ai/generate-faq
```

### Request Format (Description):

```json
{
  "title": "Community Recycling Workshop",
  "type": "workshop",
  "location": "Central Park" // optional
}
```

### Response Format (Description):

```json
{
  "success": true,
  "description": "Join us for an engaging Community Recycling Workshop..."
}
```

### Request Format (FAQ):

```json
{
  "title": "Community Recycling Workshop",
  "type": "workshop",
  "description": "Full event description here..."
}
```

### Response Format (FAQ):

```json
{
  "success": true,
  "faqs": [
    {
      "question": "What should I bring to the workshop?",
      "answer": "Please bring your own reusable water bottle..."
    },
    // ... 4 more Q&A pairs
  ]
}
```

## 🎨 UI Components

### "Generate with AI" Button
- Located next to Description label
- Purple outline style matching the app theme
- Shows spinner during generation
- Disabled state while loading

### AI Modal
- Large, scrollable modal (modal-lg)
- Purple header with magic icon
- Formatted content display
- Action buttons: Close / Use This Description

### FAQ Preview
- Card-style design
- Left border accent (purple)
- Q&A pairs numbered
- White background with spacing

## 🚀 How It Works

1. **User clicks "Generate with AI"**
   ```javascript
   // Validates title and type are filled
   // Makes AJAX POST request to Laravel backend
   ```

2. **Laravel receives request**
   ```php
   // CommunityEventController@generateDescription
   // Calls GeminiService->generateEventDescription()
   ```

3. **GeminiService calls API**
   ```php
   // Builds specialized prompt
   // Sends HTTP request to Google Gemini API
   // Parses and returns response
   ```

4. **Frontend displays result**
   ```javascript
   // Shows modal with generated content
   // On accept: inserts into textarea
   // Auto-triggers FAQ generation
   ```

5. **FAQ generation (automatic)**
   ```javascript
   // Uses description + title + type
   // Generates 5 Q&A pairs
   // Displays in modal (informational only)
   ```

## 🔐 Security

- API key stored in `.env` (not in git)
- CSRF token validation on all AJAX requests
- Input validation in controllers
- Error handling with try-catch blocks
- Sanitized output before display

## 🐛 Troubleshooting

### Issue: "Failed to generate description"

**Possible causes:**
1. Invalid or missing API key
2. API quota exceeded
3. Network connectivity issues

**Solutions:**
1. Check `.env` has correct `GEMINI_API_KEY`
2. Verify key is valid at Google AI Studio
3. Check Laravel logs: `storage/logs/laravel.log`

### Issue: Button doesn't work

**Check:**
1. Title and Type fields are filled
2. Browser console for JavaScript errors (F12)
3. Network tab shows POST request is sent

### Issue: API returns error

**Debug:**
```bash
# View Laravel logs
php artisan tail

# Or check file directly
cat storage/logs/laravel.log
```

## 📊 Prompt Engineering

The AI prompts are carefully crafted to:

### Description Prompt:
- Specifies 150-200 words
- Emphasizes environmental/sustainability focus
- Requests professional yet friendly tone
- Includes concrete examples
- Highlights community benefits

### FAQ Prompt:
- Requests exactly 5 Q&A pairs
- Focuses on practical concerns
- Keeps answers brief (2-3 sentences)
- Uses consistent Q:/A: format
- Addresses common participant questions

## 🎯 Future Enhancements (Optional)

- [ ] Save generated FAQs to database
- [ ] Display FAQ on event detail page
- [ ] Add "Regenerate" button for different variations
- [ ] Support for multiple languages
- [ ] AI-powered event name suggestions
- [ ] Smart date/time recommendations
- [ ] Participant limit suggestions
- [ ] Related events recommendations

## 📝 Notes

- Free tier API key has rate limits
- Each generation costs ~0.001 tokens
- Average response time: 2-4 seconds
- Descriptions are unique each time (temperature: 0.7)
- Works best with descriptive event titles

## 🎉 Success Indicators

You'll know it's working when:
- ✅ Button shows "Generating..." with spinner
- ✅ Modal appears within 2-4 seconds
- ✅ Description is relevant and well-formatted
- ✅ FAQ appears automatically after accepting
- ✅ Description inserts into textarea correctly

---

**Created by:** GitHub Copilot AI Assistant  
**Date:** October 21, 2025  
**Version:** 1.0
