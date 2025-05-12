<?php
function redeem_loyalty_points($email, $points)
{
    $response = wp_remote_post(LOYALTY_API_BASE . '/points/redeem', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode(['email' => $email, 'points' => $points]),
    ]);

    if (is_wp_error($response)) {
        return ['error' => true, 'message' => 'Request failed'];
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}
