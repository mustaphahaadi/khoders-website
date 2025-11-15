# Forms Module Documentation

This directory contains all form handlers for the KHODERS website. Each form processes submissions, logs them, and integrates with the database.

---

## Overview

| Form | Purpose | Endpoint | Database Table | Logs |
|------|---------|----------|-----------------|------|
| **Contact Form** | Site visitor inquiries | `forms/contact.php` | `contacts` | `logs/contacts.log` |
| **Registration Form** | Membership applications | `forms/register.php` | `members` | `logs/registrations.log` |
| **Newsletter Form** | Email subscriptions | `forms/newsletter.php` | `newsletter_subscribers` | `logs/subscriptions.log` |

---

## 1. Contact Form (`contact.php`)

### Purpose
Allows site visitors to submit contact inquiries which are logged and emailed to the admin.

### Form Fields
- **Name** (required) - Visitor's name
- **Email** (required) - Visitor's email address
- **Phone** (optional) - Contact phone number
- **Subject** (required) - Inquiry subject line
- **Message** (required) - Main inquiry message (up to 2000 characters)

### Data Flow
```
HTML Form (pages/contact.html)
    ↓
forms/contact.php (validates and processes)
    ↓
├─→ Saves to: contacts table in database
├─→ Logs to: logs/contacts.log
└─→ Sends email to: info@khodersclub.com (configurable)
    ↓
Admin receives email notification
```

### Security Features
- **Honeypot field** (`website`) - Catches bot submissions
- **CSRF token validation** - Prevents cross-site attacks
- **IP address logging** - Tracks submission source for auditing
- **Input validation** - Fields are checked before processing

### Configuration
Email recipient can be changed in `contact.php` line 12:
```php
$receiving_email_address = 'info@khodersclub.com';
```

To enable SMTP for sending emails, uncomment and configure lines 62-68:
```php
$contact->smtp = array(
  'host' => 'example.com',
  'username' => 'example',
  'password' => 'pass',
  'port' => '587'
);
```

### Response
- **Success**: "Your message has been sent"
- **Failure**: Error message indicating what went wrong
- **Always**: Email is sent regardless of database success (logged if database fails)

### Admin Access
View all contact submissions in admin panel:
- Path: `admin/index.php?route=contacts`
- Shows: All contact messages with timestamps and IP addresses

---

## 2. Registration Form (`register.php`)

### Purpose
Collects membership applications from students and interested participants.

### Form Fields
- **First Name** (required) - Applicant's first name
- **Last Name** (required) - Applicant's last name
- **Email** (required) - Applicant's email address
- **Phone** (optional) - Contact number
- **Student ID** (optional) - Educational institution ID
- **Program of Study** (required) - Selected program/course
- **Year of Study** (required) - Current academic year
- **Areas of Interest** (optional) - Multiple checkbox selection
- **Experience Level** (required) - Previous experience with field
- **Additional Information** (optional) - Free text (up to 2000 characters)

### Data Flow
```
HTML Form (pages/register.html)
    ↓
forms/register.php (validates and processes)
    ↓
├─→ Saves to: members table in database
├─→ Logs to: logs/registrations.log
└─→ Sends email to: info@khodersclub.com (configurable)
    ↓
Admin receives registration notification
↓
Member can be viewed/managed in admin panel
```

### Security Features
- **Honeypot field** (`username`) - Catches bot registrations
- **CSRF token validation** - Prevents cross-site attacks
- **IP address logging** - Tracks registration source
- **Input validation** - All required fields validated
- **Array handling** - Properly validates checkbox arrays for interests

### Configuration
Email recipient can be changed in `register.php` line 12:
```php
$receiving_email_address = 'info@khodersclub.com';
```

To enable SMTP for sending emails, uncomment and configure lines 58-64:
```php
$register->smtp = array(
  'host' => 'example.com',
  'username' => 'example',
  'password' => 'pass',
  'port' => '587'
);
```

### Response
- **Success**: "Your registration has been received"
- **Failure**: Error message indicating what went wrong
- **Always**: Email is sent regardless of database success (logged if database fails)

### Admin Access
View all registrations in admin panel:
- Path: `admin/index.php?route=members`
- Shows: All members with registration details and timestamps
- Actions: View, edit, or delete member records

---

## 3. Newsletter Form (`newsletter.php`)

### Purpose
Manages email newsletter subscriptions from visitors.

### Form Fields
- **Email** (required) - Subscriber's email address

### Data Flow
```
HTML Form (newsletter signup blocks on various pages)
    ↓
forms/newsletter.php (validates and processes)
    ↓
├─→ Saves to: newsletter_subscribers table in database
├─→ Logs to: logs/subscriptions.log
└─→ Sends email to: newsletter@khodersclub.com (configurable)
    ↓
Admin receives subscription notification
↓
Subscriber is added to mailing list
```

### Security Features
- **Honeypot field** (`website`) - Catches bot subscriptions
- **CSRF token validation** - Prevents cross-site attacks
- **Email validation** - Valid email format required
- **IP address logging** - Tracks subscription source
- **Referrer tracking** - Records where subscription came from

### Configuration
Email recipient can be changed in `newsletter.php` line 12:
```php
$receiving_email_address = 'newsletter@khodersclub.com';
```

To enable SMTP for sending emails, uncomment and configure lines 56-62:
```php
$newsletter->smtp = array(
  'host' => 'example.com',
  'username' => 'example',
  'password' => 'pass',
  'port' => '587'
);
```

### Response
- **Success**: "Thank you for subscribing"
- **Failure**: Error message indicating what went wrong
- **Always**: Email is sent regardless of database success (logged if database fails)

### Admin Access
View all newsletter subscriptions in admin panel:
- Path: `admin/index.php?route=newsletter`
- Shows: All subscribers with subscription date and source
- Actions: Manage or export subscriber list

---

## Database Integration

All forms use the `database/db_functions.php` module which provides three functions:

### saveContactForm($data)
- **Table**: `contacts`
- **Fields**: `name`, `email`, `phone`, `subject`, `message`, `created_at`, `ip_address`
- **Returns**: `true` if saved successfully, `false` on error

### saveRegistration($data)
- **Table**: `members`
- **Fields**: `first_name`, `last_name`, `email`, `phone`, `student_id`, `program`, `year`, `interests`, `experience`, `additional_info`, `created_at`, `ip_address`
- **Returns**: `true` if saved successfully, `false` on error

### saveNewsletter($data)
- **Table**: `newsletter_subscribers`
- **Fields**: `email`, `subscribed_at`, `ip_address`, `referrer`
- **Returns**: `true` if saved successfully, `false` on error

---

## Logging

Each form maintains a log file in the `logs/` directory for debugging and audit purposes.

### Log Format
```
YYYY-MM-DD HH:MM:SS | IP: 192.168.1.1 | [form-specific details]
```

### Example Entries

**Contact Log** (`logs/contacts.log`):
```
2025-11-15 14:23:45 | IP: 192.168.1.100 | New Contact: John Doe (john@example.com) | Subject: Program Inquiry
```

**Registration Log** (`logs/registrations.log`):
```
2025-11-15 14:45:22 | IP: 192.168.1.101 | New Registration: Jane Smith (jane@example.com) | Program: Web Development | Year: 2025
```

**Subscription Log** (`logs/subscriptions.log`):
```
2025-11-15 15:10:08 | IP: 192.168.1.102 | New Subscription: jane@example.com | User Agent: Mozilla/5.0...
```

### Log Directory
Logs are stored in `logs/` directory. This directory is created automatically if it doesn't exist.

> **Note**: Keep logs private and implement log rotation for production systems.

---

## Email Configuration

### Default Behavior
Forms send emails via PHP's default `mail()` function. This works for local development but may not be reliable for production.

### Configuring SMTP (Recommended for Production)
Each form handler has commented SMTP configuration. To enable:

1. **Get SMTP credentials** from your email provider:
   - **Gmail**: Use app-specific password with `smtp.gmail.com:587`
   - **Office 365**: Use `smtp.office365.com:587`
   - **SendGrid**: Use `smtp.sendgrid.net:587`
   - **Custom server**: Contact your hosting provider

2. **Uncomment the SMTP block** in the desired form file:
   ```php
   $contact->smtp = array(
     'host' => 'smtp.gmail.com',
     'username' => 'your-email@gmail.com',
     'password' => 'your-app-specific-password',
     'port' => '587'
   );
   ```

3. **Test** by submitting a form and confirming receipt at the configured email address.

---

## Troubleshooting

| Issue | Cause | Solution |
|-------|-------|----------|
| Forms don't save to database | Database connection failing | Check `.env` file credentials and MySQL is running |
| Emails not received | Mail function disabled or SMTP not configured | Use SMTP configuration (see above) |
| "Bot submission" always sent | Honeypot field filled | Check form HTML - honeypot must be hidden with CSS |
| Database saves but no email | SMTP not configured | Set up SMTP configuration (see above) |
| 403 error when submitting | CSRF token missing | Ensure form includes `<input type="hidden" name="csrf_token" value="..." />` |

---

## Security Best Practices

1. **Keep forms private** - Only accessible from your website forms
2. **Validate all inputs** - Current forms validate, but custom forms should too
3. **Use HTTPS** - Always submit forms over encrypted connection
4. **Monitor logs** - Review logs regularly for suspicious patterns
5. **Rate limit** - Consider adding rate limiting to prevent spam
6. **Update regularly** - Keep PHP Email Form library updated
7. **Email verification** - Consider requiring email confirmation for registrations
8. **GDPR Compliance** - Ensure compliance with data protection regulations

---

## Development Notes

### Adding a New Form
To add a new form handler:

1. Create `forms/yourform.php`
2. Include database functions: `require_once '../database/db_functions.php'`
3. Initialize PHP_Email_Form: `$form = new PHP_Email_Form;`
4. Add form fields and validation
5. Save to database: `saveYourForm($data)`
6. Send email: `$form->send()`
7. Document in this README

### Testing Forms
Local testing works with the default `mail()` function if configured. For reliable testing, use SMTP configuration.

### Form HTML
Form HTML files should be in `/pages/` directory and POST to the appropriate `forms/*.php` handler.

Example:
```html
<form action="forms/contact.php" method="POST" id="contactForm">
  <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>" />
  <input type="text" name="name" placeholder="Your Name" required />
  <input type="email" name="email" placeholder="Your Email" required />
  <!-- honeypot -->
  <input type="hidden" name="website" />
  <!-- form fields -->
  <button type="submit">Send</button>
</form>
```

---

## Version History

- **v1.0** (Nov 15, 2025) - Initial documentation created
  - Documented all three form handlers
  - Added security features overview
  - Included email configuration guide
  - Added troubleshooting section

---

## Support

For issues or questions about forms:
1. Check the **Troubleshooting** section above
2. Review **Security Best Practices**
3. Check **Logging** for error details
4. Contact the development team

---

*Last Updated: November 15, 2025*
