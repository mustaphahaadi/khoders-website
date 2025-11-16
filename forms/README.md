# Form Handlers

This directory contains form submission handlers for the KHODERS website.

## Files

### contact.php
- **Purpose:** Handle contact form submissions
- **Method:** POST
- **Fields:** name, email, subject, message
- **Saves to:** contacts table
- **Response:** JSON with success/error message
- **Security:** Prepared statements, input validation, CSRF protection

### register.php
- **Purpose:** Handle member registration
- **Method:** POST
- **Fields:** name, email, phone, program, experience_level
- **Saves to:** registrations table
- **Response:** JSON with success/error message
- **Security:** Prepared statements, input validation, CSRF protection

### newsletter.php
- **Purpose:** Handle newsletter subscriptions
- **Method:** POST
- **Fields:** email
- **Saves to:** newsletter_subscriptions table
- **Response:** JSON with success/error message
- **Security:** Prepared statements, input validation, CSRF protection

## Security

All forms:
- Use prepared statements to prevent SQL injection
- Validate input before saving
- Sanitize output
- Include CSRF protection
- Have honeypot spam detection

## Testing

To test a form:

```bash
curl -X POST http://localhost/khoders-website/forms/contact.php \
  -d "name=Test&email=test@example.com&subject=Test&message=Test message"
```

## Troubleshooting

If forms don't work:
1. Check database connection in `config/database.php`
2. Verify tables exist (contacts, registrations, newsletter_subscriptions)
3. Check file permissions
4. Review error logs in `logs/` directory
