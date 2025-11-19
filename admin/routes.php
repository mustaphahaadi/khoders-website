<?php
/**
 * KHODERS WORLD Admin Routes
 * Define routes for the admin panel
 */

// Include router
require_once __DIR__ . '/includes/router.php';

// Set base URL
Router::setBaseUrl('');

// Dashboard
Router::register('index', 'pages/dashboard.php', [
    'name' => 'dashboard',
    'title' => 'Dashboard - KHODERS WORLD Admin'
]);

// Content Management
Router::register('events', 'pages/events.php', [
    'name' => 'events',
    'title' => 'Events - KHODERS WORLD Admin'
]);

Router::register('event-editor', 'pages/event-editor.php', [
    'name' => 'event-editor',
    'title' => 'Event Editor - KHODERS WORLD Admin'
]);

Router::register('team', 'pages/team.php', [
    'name' => 'team',
    'title' => 'Team Members - KHODERS WORLD Admin'
]);

Router::register('team-editor', 'pages/team-editor.php', [
    'name' => 'team-editor',
    'title' => 'Team Member Editor - KHODERS WORLD Admin'
]);

Router::register('projects', 'pages/projects.php', [
    'name' => 'projects',
    'title' => 'Projects - KHODERS WORLD Admin'
]);

Router::register('project-editor', 'pages/project-editor.php', [
    'name' => 'project-editor',
    'title' => 'Project Editor - KHODERS WORLD Admin'
]);

Router::register('courses', 'pages/courses.php', [
    'name' => 'courses',
    'title' => 'Programs/Courses - KHODERS WORLD Admin'
]);

Router::register('course-editor', 'pages/course-editor.php', [
    'name' => 'course-editor',
    'title' => 'Course Editor - KHODERS WORLD Admin'
]);

Router::register('programs', 'pages/programs.php', [
    'name' => 'programs',
    'title' => 'Programs - KHODERS WORLD Admin'
]);

Router::register('program-editor', 'pages/program-editor.php', [
    'name' => 'program-editor',
    'title' => 'Program Editor - KHODERS WORLD Admin'
]);

Router::register('blog', 'pages/blog.php', [
    'name' => 'blog',
    'title' => 'Blog Posts - KHODERS WORLD Admin'
]);

Router::register('blog-editor', 'pages/blog-editor.php', [
    'name' => 'blog-editor',
    'title' => 'Blog Editor - KHODERS WORLD Admin'
]);

Router::register('skills', 'pages/skills.php', [
    'name' => 'skills',
    'title' => 'Skills Management - KHODERS WORLD Admin'
]);

Router::register('resources', 'pages/resources.php', [
    'name' => 'resources',
    'title' => 'Learning Resources - KHODERS WORLD Admin'
]);

Router::register('resource-editor', 'pages/resource-editor.php', [
    'name' => 'resource-editor',
    'title' => 'Resource Editor - KHODERS WORLD Admin'
]);

// Member Management
Router::register('members', 'pages/members.php', [
    'name' => 'members',
    'title' => 'Members - KHODERS WORLD Admin'
]);

Router::register('contacts', 'pages/contacts.php', [
    'name' => 'contacts',
    'title' => 'Contact Messages - KHODERS WORLD Admin'
]);

Router::register('newsletter', 'pages/newsletter.php', [
    'name' => 'newsletter',
    'title' => 'Newsletter - KHODERS WORLD Admin'
]);

Router::register('ratings', 'pages/ratings.php', [
    'name' => 'ratings',
    'title' => 'Ratings & Reviews - KHODERS WORLD Admin'
]);

Router::register('enrollments', 'pages/enrollments.php', [
    'name' => 'enrollments',
    'title' => 'Enrollments - KHODERS WORLD Admin'
]);

// System
Router::register('form-logs', 'pages/form-logs.php', [
    'name' => 'form-logs',
    'title' => 'Form Logs - KHODERS WORLD Admin',
    'requiredRole' => 'admin'
]);

Router::register('settings', 'pages/settings.php', [
    'name' => 'settings',
    'title' => 'Settings - KHODERS WORLD Admin',
    'requiredRole' => 'admin'
]);

Router::register('site-settings', 'pages/site-settings.php', [
    'name' => 'site-settings',
    'title' => 'Site Settings - KHODERS WORLD Admin',
    'requiredRole' => 'admin'
]);

Router::register('profile', 'pages/profile.php', [
    'name' => 'profile',
    'title' => 'My Profile - KHODERS WORLD Admin'
]);

Router::register('admin-users', 'pages/admin-users.php', [
    'name' => 'admin-users',
    'title' => 'Admin Users - KHODERS WORLD Admin',
    'requiredRole' => 'admin'
]);

// Define 404 handler
Router::notFound(function() {
    header('HTTP/1.0 404 Not Found');
    include 'pages/404.php';
});
