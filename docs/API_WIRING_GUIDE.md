# API Wiring Guide - KHODERS WORLD

## Overview

This document describes how the admin dashboard, public APIs, and frontend templates are wired together to create a complete content management system.

---

## Architecture Flow

```
Admin Dashboard
    ↓
Admin Editor Pages (course-editor.php, event-editor.php, etc.)
    ↓
Database Tables (courses, events, projects, team_members, blog_posts)
    ↓
Public API Endpoints (courses-list.php, events-list.php, etc.)
    ↓
Frontend Templates (events-template.php, projects-template.php, etc.)
    ↓
Public Website Pages
```

---

## Content Type: Courses

### Admin Editor

- **File**: `admin/pages/course-editor.php`
- **Route**: `?route=course-editor&action=add` or `?route=course-editor&action=edit&id=1`
- **Form Fields**:
  - Title (required)
  - Description
  - Duration
  - Level (Beginner, Intermediate, Advanced)
  - Instructor
  - **Image File Upload** (5MB max, JPG/PNG/WebP/GIF)
  - Price
  - Status (Draft, Active, Inactive)

### File Upload Processing

```php
// Handled by FileUploader class
$uploader = new FileUploader('courses', 5 * 1024 * 1024);
$uploadResult = $uploader->upload($_FILES['image_file']);
// Returns: ['success' => bool, 'path' => '/public/uploads/courses/filename...', 'error' => string]
```

### Database Table

```sql
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description LONGTEXT,
    duration VARCHAR(100),
    level VARCHAR(50),
    instructor VARCHAR(255),
    image_url VARCHAR(500),  -- Stores path: /public/uploads/courses/...
    price DECIMAL(10, 2),
    status VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Public API Endpoint

- **URL**: `/api/courses-list.php`
- **Method**: GET
- **Returns**: JSON array of courses

```json
[
  {
    "id": 1,
    "title": "Web Development Basics",
    "description": "Learn HTML, CSS, and JavaScript",
    "duration": "4 weeks",
    "level": "Beginner",
    "instructor": "John Doe",
    "image_url": "/public/uploads/courses/course-1-timestamp-random.jpg",
    "price": "49.99",
    "status": "active"
  }
]
```

### Frontend Integration

- **Template**: `pages/courses.html`
- **API Call**: Fetch from `/api/courses-list.php`
- **Display**: Show course cards with image, title, instructor, level, and price

---

## Content Type: Events

### Admin Editor

- **File**: `admin/pages/event-editor.php`
- **Route**: `?route=event-editor&action=add` or `?route=event-editor&action=edit&id=1`
- **Form Fields**:
  - Title (required)
  - Description
  - Event Date & Time
  - Location
  - **Image File Upload** (5MB max)
  - Registration URL
  - Is Featured (checkbox)
  - Status (Upcoming, Ongoing, Completed, Cancelled)

### File Upload Processing

```php
$uploader = new FileUploader('events', 5 * 1024 * 1024);
$uploadResult = $uploader->upload($_FILES['image_file']);
// Saves to: /public/uploads/events/filename...
```

### Database Table

```sql
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description LONGTEXT,
    event_date DATETIME,
    location VARCHAR(255),
    image_url VARCHAR(500),  -- Stores path: /public/uploads/events/...
    registration_url VARCHAR(500),
    is_featured BOOLEAN,
    status VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Public API Endpoint

- **URL**: `/api/events-list.php`
- **Method**: GET
- **Query Parameters**:
  - `status` - Filter by status (upcoming, ongoing, completed, cancelled)
- **Returns**: JSON array of events

```json
[
  {
    "id": 1,
    "title": "Web Development Workshop",
    "description": "Hands-on workshop for beginners",
    "event_date": "2024-02-15 10:00:00",
    "location": "KHODERS Center",
    "image_url": "/public/uploads/events/event-1-timestamp-random.jpg",
    "registration_url": "https://eventbrite.com/...",
    "is_featured": 1,
    "status": "upcoming"
  }
]
```

### Frontend Integration

- **Template**: `pages/events-template.php`
- **API Call**: Fetch from `/api/events-list.php?status=upcoming`
- **Display**: Show event cards with date, location, image, and registration button

---

## Content Type: Projects

### Admin Editor

- **File**: `admin/pages/project-editor.php`
- **Route**: `?route=project-editor&action=add` or `?route=project-editor&action=edit&id=1`
- **Form Fields**:
  - Title (required)
  - Description
  - Technologies Used (comma-separated, stored as JSON)
  - **Image File Upload** (5MB max)
  - GitHub URL
  - Demo URL
  - Is Featured (checkbox)
  - Status (Active, Completed, In-Progress, Archived)

### File Upload Processing

```php
$uploader = new FileUploader('projects', 5 * 1024 * 1024);
$uploadResult = $uploader->upload($_FILES['image_file']);
// Saves to: /public/uploads/projects/filename...
```

### Database Table

```sql
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description LONGTEXT,
    image_url VARCHAR(500),  -- Stores path: /public/uploads/projects/...
    tech_stack JSON,         -- Stores: ["React", "Node.js", "MongoDB"]
    github_url VARCHAR(500),
    demo_url VARCHAR(500),
    is_featured BOOLEAN,
    status VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Public API Endpoint

- **URL**: `/api/projects-list.php`
- **Method**: GET
- **Returns**: JSON array of projects

```json
[
  {
    "id": 1,
    "title": "E-Commerce Platform",
    "description": "Full-stack e-commerce solution",
    "image_url": "/public/uploads/projects/project-1-timestamp-random.jpg",
    "tech_stack": ["React", "Node.js", "MongoDB"],
    "github_url": "https://github.com/khoders/ecommerce",
    "demo_url": "https://ecommerce-demo.khoders.com",
    "is_featured": 1,
    "status": "completed"
  }
]
```

### Frontend Integration

- **Template**: `pages/projects-template.php`
- **API Call**: Fetch from `/api/projects-list.php`
- **Display**: Show project cards with image, tech stack, links to GitHub and demo

---

## Content Type: Team Members

### Admin Editor

- **File**: `admin/pages/team-editor.php`
- **Route**: `?route=team-editor&action=add` or `?route=team-editor&action=edit&id=1`
- **Form Fields**:
  - Name (required)
  - Position/Role
  - Biography
  - **Photo File Upload** (5MB max)
  - Email
  - LinkedIn URL
  - GitHub URL
  - Twitter URL
  - Personal Website
  - Display Order (lower numbers first)
  - Is Featured (checkbox)
  - Status (Active, Inactive, Alumni)

### File Upload Processing

```php
$uploader = new FileUploader('team', 5 * 1024 * 1024);
$uploadResult = $uploader->upload($_FILES['photo_file']);
// Saves to: /public/uploads/team/filename...
```

### Database Table

```sql
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    position VARCHAR(255),
    bio LONGTEXT,
    photo_url VARCHAR(500),  -- Stores path: /public/uploads/team/...
    email VARCHAR(255),
    linkedin_url VARCHAR(500),
    github_url VARCHAR(500),
    twitter_url VARCHAR(500),
    personal_website VARCHAR(500),
    is_featured BOOLEAN,
    status VARCHAR(50),
    order_index INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Public API Endpoint

- **URL**: `/api/team-list.php`
- **Method**: GET
- **Returns**: JSON array of team members, ordered by order_index and name

```json
[
  {
    "id": 1,
    "name": "Jane Smith",
    "position": "Founder & CEO",
    "bio": "Passionate about education and technology...",
    "photo_url": "/public/uploads/team/jane-smith-timestamp-random.jpg",
    "email": "jane@khoders.com",
    "linkedin_url": "https://linkedin.com/in/janesmith",
    "github_url": "https://github.com/janesmith",
    "twitter_url": "https://twitter.com/janesmith",
    "personal_website": "https://janesmith.com",
    "is_featured": 1,
    "status": "active",
    "order_index": 1
  }
]
```

### Frontend Integration

- **Template**: `pages/team-template.php`
- **API Call**: Fetch from `/api/team-list.php`
- **Display**: Show team member cards with photo, title, bio, and social media links

---

## Content Type: Blog Posts

### Admin Editor

- **File**: `admin/pages/blog-editor.php`
- **Route**: `?route=blog-editor&action=add` or `?route=blog-editor&action=edit&id=1`
- **Form Fields**:
  - Title (required)
  - Slug (auto-generated from title)
  - Content (required, supports HTML)
  - Excerpt
  - Author
  - Category
  - Tags (comma-separated)
  - **Featured Image File Upload** (5MB max)
  - Featured Image Alt Text
  - Status (Draft, Published, Archived)

### File Upload Processing

```php
$uploader = new FileUploader('blog', 5 * 1024 * 1024);
$uploadResult = $uploader->upload($_FILES['featured_image_file']);
// Saves to: /public/uploads/blog/filename...
```

### Database Table

```sql
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    content LONGTEXT,
    excerpt VARCHAR(500),
    author VARCHAR(255),
    featured_image VARCHAR(500),  -- Stores path: /public/uploads/blog/...
    featured_image_alt VARCHAR(255),
    category VARCHAR(100),
    tags VARCHAR(500),
    status VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Public API Endpoint

- **URL**: `/api/blog-list.php`
- **Method**: GET
- **Returns**: JSON array of published blog posts

```json
[
  {
    "id": 1,
    "title": "Getting Started with Web Development",
    "slug": "getting-started-with-web-development",
    "content": "<p>HTML, CSS, and JavaScript are...</p>",
    "excerpt": "A beginner's guide to web development",
    "author": "John Doe",
    "featured_image": "/public/uploads/blog/blog-1-timestamp-random.jpg",
    "featured_image_alt": "Web development tools",
    "category": "Tutorial",
    "tags": "web,development,beginner",
    "status": "published",
    "created_at": "2024-01-15 10:00:00"
  }
]
```

### Frontend Integration

- **Display**: Blog list on homepage or dedicated blog page
- **API Call**: Fetch from `/api/blog-list.php`
- **Features**: Display posts with featured image, excerpt, and link to full post

---

## File Upload System

### FileUploader Class

**Location**: `config/file-upload.php`

### Features

- **Secure filename generation**: `filename-[timestamp]-[random].ext`
- **MIME type validation**: Allows JPG, PNG, WebP, GIF only
- **File size validation**: 5MB default limit (configurable)
- **Extension whitelist**: jpg, jpeg, png, webp, gif
- **Auto-directory creation**: Creates upload folders with proper permissions (0755)
- **Old file cleanup**: Automatically removes old image when editing

### Usage Pattern

```php
// Initialize uploader for specific content type
$uploader = new FileUploader('courses', 5 * 1024 * 1024); // 5MB limit

// Upload file
$uploadResult = $uploader->upload($_FILES['image_file']);

// Check result
if ($uploadResult['success']) {
    $filePath = $uploadResult['path']; // e.g., /public/uploads/courses/file-timestamp-random.jpg
    // Save $filePath to database
} else {
    $error = $uploadResult['error']; // Display error to user
}

// Delete old file (when editing)
if ($action === 'edit' && !empty($oldPath)) {
    $uploader->delete($oldPath);
}
```

### Upload Directory Structure

```
public/uploads/
├── courses/       # Course images
├── events/        # Event images
├── projects/      # Project images
├── team/          # Team member photos
└── blog/          # Blog featured images
```

---

## Form Processing Pattern

### In Admin Editor Pages

1. **Include FileUploader class**

   ```php
   require_once __DIR__ . '/../../config/file-upload.php';
   ```

2. **Set form enctype**

   ```html
   <form method="post" enctype="multipart/form-data"></form>
   ```

3. **Add file input**

   ```html
   <input
     type="file"
     name="image_file"
     accept="image/*"
     onchange="previewImage(this, 'preview_id')"
   />
   ```

4. **Handle upload in POST processing**

   ```php
   if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
       $uploader = new FileUploader('courses', 5 * 1024 * 1024);
       $uploadResult = $uploader->upload($_FILES['image_file']);

       if ($uploadResult['success']) {
           // Delete old image if editing
           if ($action === 'edit' && !empty($old_path)) {
               $uploader->delete($old_path);
           }
           $field_value = $uploadResult['path'];
       } else {
           $error = 'Image upload failed: ' . $uploadResult['error'];
       }
   }
   ```

5. **Add JavaScript preview function**
   ```javascript
   function previewImage(input, previewId) {
     if (input.files && input.files[0]) {
       const reader = new FileReader();
       reader.onload = function (e) {
         const preview = document.getElementById(previewId);
         preview.src = e.target.result;
         preview.style.display = "block";
       };
       reader.readAsDataURL(input.files[0]);
     }
   }
   ```

---

## Complete Data Flow Example: Adding a Course

### Step 1: Admin User Action

- Admin visits: `admin/?route=course-editor&action=add`
- Fills in form:
  - Title: "React Fundamentals"
  - Description: "Learn React basics..."
  - Instructor: "Jane Doe"
  - **Selects image file** (automatically previewed via JavaScript)

### Step 2: Form Submission

- Form POSTs to same URL with `enctype="multipart/form-data"`
- Browser sends:
  - Form data (title, description, instructor, etc.)
  - **Binary image file** in `$_FILES['image_file']`

### Step 3: Server-Side Processing

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form fields
    $course['title'] = $_POST['title'];
    $course['description'] = $_POST['description'];

    // Handle image upload
    $uploader = new FileUploader('courses', 5 * 1024 * 1024);
    $uploadResult = $uploader->upload($_FILES['image_file']);

    if ($uploadResult['success']) {
        // Save path to database
        $course['image_url'] = $uploadResult['path'];
        // e.g., /public/uploads/courses/course-1612345678-abc123.jpg
    }

    // Insert into database
    $db->insert('courses', $course);
}
```

### Step 4: File Storage

- File saved to: `/public/uploads/courses/course-1612345678-abc123.jpg`
- Database record contains: `image_url = "/public/uploads/courses/course-1612345678-abc123.jpg"`

### Step 5: API Retrieval

- Frontend requests: `GET /api/courses-list.php`
- API returns JSON with image path:

```json
{
  "id": 1,
  "title": "React Fundamentals",
  "description": "Learn React basics...",
  "image_url": "/public/uploads/courses/course-1612345678-abc123.jpg"
}
```

### Step 6: Frontend Display

- Template receives API data
- Displays image: `<img src="/public/uploads/courses/course-1612345678-abc123.jpg">`
- User sees course card with image on website

---

## Image Display on Frontend

### With Image File

```html
<img src="/public/uploads/courses/course-1-timestamp.jpg" alt="Course title" />
```

### With Missing Image (Fallback)

Templates include image validation:

```html
<?php if (!empty($course['image_url'])): ?>
<img
  src="<?php echo htmlspecialchars($course['image_url']); ?>"
  alt="<?php echo htmlspecialchars($course['title']); ?>"
/>
<?php else: ?>
<img src="/assets/img/placeholder.png" alt="No image available" />
<?php endif; ?>
```

---

## Security Considerations

### File Upload Validation

1. **MIME type check**: Validates actual file content, not just extension
2. **File extension whitelist**: Only allows image/\*
3. **Filename sanitization**: Generates secure random names
4. **Size limits**: Enforced server-side (5MB default)

### Database Security

1. **Prepared statements**: All queries use parameterized statements
2. **Input sanitization**: Data escaped on output
3. **CSRF protection**: All forms include CSRF tokens

### File System Security

1. **Upload directory permissions**: 0755 (readable by web server)
2. **Outside web root** consideration: Images stored in public directory for access
3. **Old file cleanup**: Orphaned files deleted on update

---

## Troubleshooting

### Issue: Image upload fails

**Causes**:

- File too large (> 5MB)
- Invalid MIME type
- Upload directory not writable
- Missing directory permissions

**Solutions**:

1. Check file size in browser console
2. Ensure file is actual image (JPG/PNG/WebP/GIF)
3. Verify `/public/uploads/` directory exists and is writable
4. Check PHP error logs

### Issue: Image doesn't display on frontend

**Causes**:

- Incorrect file path in database
- File deleted from server
- Missing file

**Solutions**:

1. Verify path in database: `SELECT image_url FROM courses WHERE id = 1;`
2. Check file exists: `ls -la /public/uploads/courses/`
3. Verify correct permissions: File should be readable

### Issue: "Image upload failed" error

**Check the following**:

1. Is file a valid image? Test with different file
2. Is file size under 5MB?
3. Are upload directories created?
4. Check PHP error logs for permissions issues

---

## Summary

This complete system ensures:

- ✅ Secure file uploads with validation
- ✅ Clean database design with file paths
- ✅ Seamless admin → API → frontend integration
- ✅ Automatic old file cleanup on edits
- ✅ Image preview before upload
- ✅ Responsive image display on frontend
- ✅ Fallback placeholders for missing images

All 4 admin editors (courses, events, projects, team) and blog editor now use file uploads instead of URL text fields, providing a better user experience and more secure file management.
