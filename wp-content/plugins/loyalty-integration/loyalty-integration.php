<?php
/**
 * Plugin Name: Loyalty Program Integration
 */

define('LOYALTY_API_HOST', '127.0.0.1');
define('LOYALTY_API_PORT', '8000');
define('LOYALTY_API_BASE', 'http://' . LOYALTY_API_HOST . ':' . LOYALTY_API_PORT . '/api/loyalty');

require_once plugin_dir_path(__FILE__) . 'includes/api-client.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';