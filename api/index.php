<?php
require_once 'ApiResponse.php';

$endpoints = [
    [
        'endpoint' => '/api/blog-list.php',
        'method' => 'GET',
        'description' => 'List blog posts'
    ],
    [
        'endpoint' => '/api/contact.php',
        'method' => 'POST',
        'description' => 'Submit contact form'
    ],
    [
        'endpoint' => '/api/courses-list.php',
        'method' => 'GET',
        'description' => 'List available courses'
    ],
    [
        'endpoint' => '/api/enroll-course.php',
        'method' => 'POST',
        'description' => 'Enroll in a specific course'
    ],
    [
        'endpoint' => '/api/enroll-program.php',
        'method' => 'POST',
        'description' => 'Enroll in a program'
    ],
    [
        'endpoint' => '/api/events-list.php',
        'method' => 'GET',
        'description' => 'List upcoming events'
    ],
    [
        'endpoint' => '/api/event-details.php',
        'method' => 'GET',
        'description' => 'Get details for a specific event'
    ],
    [
        'endpoint' => '/api/newsletter.php',
        'method' => 'POST',
        'description' => 'Subscribe to newsletter'
    ],
    [
        'endpoint' => '/api/projects-list.php',
        'method' => 'GET',
        'description' => 'List student projects'
    ],
    [
        'endpoint' => '/api/register-event.php',
        'method' => 'POST',
        'description' => 'Register for an event'
    ],
    [
        'endpoint' => '/api/register.php',
        'method' => 'POST',
        'description' => 'Register as a new member'
    ],
    [
        'endpoint' => '/api/search.php',
        'method' => 'GET',
        'description' => 'Search site content'
    ],
    [
        'endpoint' => '/api/team-list.php',
        'method' => 'GET',
        'description' => 'List team members'
    ],
    [
        'endpoint' => '/api/submit-rating.php',
        'method' => 'POST',
        'description' => 'Submit a rating and review'
    ],
    [
        'endpoint' => '/api/get-ratings.php',
        'method' => 'GET',
        'description' => 'Get ratings for an item'
    ],
    [
        'endpoint' => '/api/update-rating.php',
        'method' => 'POST',
        'description' => 'Update an existing rating'
    ],
    [
        'endpoint' => '/api/delete-rating.php',
        'method' => 'POST',
        'description' => 'Delete a rating'
    ]
];

ApiResponse::success($endpoints, 'Khoders World API - Available Endpoints');
?>
