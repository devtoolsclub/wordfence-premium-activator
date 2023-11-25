<?php
/**
 * Plugin Name: Wordfence Premium Security Activator
 * Description: Automatically activates and configures Wordfence Security plugin.
 * Version: 1.0
 * Author: GPL Community
 * Author URI: https://devtools.club/gpl/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7.4
 * Update URI: https://github.com/devtoolsclub/wordfence-premium-activator
 * Tags: security, firewall, malware scanner, real-time protection, activation, license, register, form, key
 * WC requires at least: 8.0
 * WC tested up to: 8.2
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/class-wordfence-activator.php';
require_once __DIR__ . '/includes/functions.php';

add_action('admin_init', function() {
    activator_admin_notice_plugin_install('wordfence/wordfence.php', 'wordfence', 'Wordfence Security');
    activator_admin_notice_plugin_activate('wordfence/wordfence.php');
});

function run_wordfence_activator() {
    $activator = new Wordfence_Activator();
    $activator->run();
}

run_wordfence_activator();