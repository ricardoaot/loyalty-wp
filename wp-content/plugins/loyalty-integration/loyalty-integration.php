<?php

define('LOYALTY_API_HOST', '127.0.0.1');
define('LOYALTY_API_PORT', '8000');
define('LOYALTY_API_BASE', 'http://' . LOYALTY_API_HOST . ':' . LOYALTY_API_PORT . '/api/loyalty');

/**
 * Plugin Name: Loyalty Integration
 * Description: Displays client points from Laravel API.
 * Version: 1.0
 * Author: Ricardo Olivari
 */

// Shortcode: [my_points]
add_shortcode('my_points', 'show_user_points');

function show_user_points()
{
    // Check if the user is logged in
    if (!is_user_logged_in()) {
        return '<p>Please log in to see your points.</p>';
    }

    // Get the current user's email
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;

    // We simulate that the user is logged in and this is his email
    //$email = 'cliente@email.com';

    $url = "http://127.0.0.1:8000/api/loyalty/points?email=$email";
    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return 'Error connecting to the points server.';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data['points'])) {
        return 'The points information could not be obtained.' . $url;
    }

    return "<p>You have accumulated <strong>{$data['points']}</strong> points.</p>";
}

add_shortcode('points_history', 'render_points_history');

function render_points_history()
{
    if (!is_user_logged_in()) {
        return '<p>Please log in to view your points history.</p>';
    }

    $current_user = wp_get_current_user();
    $email = $current_user->user_email;

    $url = LOYALTY_API_BASE . "/points/transactions/" . urlencode($email);
    $response = wp_remote_get($url);

    //$response = wp_remote_get("http://localhost:8000/api/points/transactions/$email");

    if (is_wp_error($response)) {
        return 'Failed to connect to the transactions service.';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!is_array($data)) {
        return 'Invalid data format received from API.';
    }

    if (empty($data)) {
        return '<p>You have no transactions yet.</p>';
    }

    $html = '<table style="width:100%; border-collapse: collapse;" border="1">';
    $html .= '<thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Points</th>
        </tr>
        </thead><tbody>';

    $timezone = get_option('timezone_string') ?: 'UTC';
    
    foreach ($data as $transaction) {
        $date = new DateTime($transaction['created_at'], new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));
    
        $formatted_date = $date->format('d M Y, h:i A (T)');
        $html .= '<tr>';
            $html .= '<td>' . esc_html($formatted_date) . '</td>';
            $html .= '<td>' . esc_html(ucfirst($transaction['type'])) . '</td>';
            $html .= '<td>' . esc_html($transaction['points']) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    return $html;
}
