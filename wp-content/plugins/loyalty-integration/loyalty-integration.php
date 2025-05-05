<?php
/**
 * Plugin Name: Loyalty Integration
 * Description: Displays client points from Laravel API.
 * Version: 1.0
 * Author: Ricardo
 */

// Shortcode: [my_points]
add_shortcode('my_points', 'show_user_points');

function show_user_points() {
    // We simulate that the user is logged in and this is his email
    $email = 'cliente@email.com';
    $url = "http://127.0.0.1:8000/api/loyalty/points?email=$email";
    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return 'Error connecting to the points server.';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data['points'])) {
        return 'The points information could not be obtained.'.$url;
    }

    return "<p>You have accumulated <strong>{$data['points']}</strong> points.</p>";
}
