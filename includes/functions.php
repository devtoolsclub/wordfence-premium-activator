<?php

defined('ABSPATH') || exit;

/**
 * Checks if specific admin actions related to plugin management are being executed.
 *
 * @return bool True if an install or upload action is happening, false otherwise.
 */
function activator_admin_notice_ignored() {
    global $pagenow;
    $action = $_REQUEST['action'] ?? '';

    return $pagenow == 'update.php' && in_array($action, ['install-plugin', 'upload-plugin'], true);
}

/**
 * Checks if a plugin is installed.
 *
 * @param string $plugin Plugin file path to check.
 * @return bool True if the plugin is installed, false otherwise.
 */
function is_plugin_installed($plugin) {
    $installed_plugins = get_plugins();
    return isset($installed_plugins[$plugin]);
}

/**
 * Displays an admin notice if a required plugin is not installed.
 *
 * @param string $plugin Plugin file path.
 * @param string $wp_plugin_id WordPress plugin ID.
 * @param string $plugin_name Name of the plugin.
 * @return bool True if notice is added, false otherwise.
 */
function activator_admin_notice_plugin_install($plugin, $wp_plugin_id, $plugin_name) {
    if (!is_plugin_installed($plugin)) {
        if (!current_user_can('install_plugins')) {
            return true;
        }
        $install_url = 'https://devtools.club/product/wordfence/'; // Direct link to product page
        $message = '<h3>' . esc_html__("{$plugin_name} plugin is required", 'wordfence-activator') . '</h3>';
        $message .= '<p>' . esc_html__("Install and activate the \"{$plugin_name}\" plugin to access all features.", 'wordfence-activator') . '</p>';
        $message .= '<p>' . sprintf('<a href="%s" class="button-primary">%s</a>', esc_url($install_url), esc_html__('Install Now', 'wordfence-activator')) . '</p>';

        add_action('admin_notices', function () use ($message) {
            ?><div class="notice notice-error"><p><?= $message ?></p></div><?php
        });

        return true;
    }

    return false;
}

/**
 * Displays an admin notice if a required plugin is not active.
 *
 * @param string $plugin Plugin file path.
 * @return bool True if notice is added, false otherwise.
 */
function activator_admin_notice_plugin_activate($plugin) {
    if (!is_plugin_active($plugin)) {
        if (!current_user_can('activate_plugins')) {
            return true;
        }
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
        $plugin_id = substr($plugin, 0, strpos($plugin, '/'));
        $activate_action = sprintf(
            '<a href="%s" id="activate-%s" class="button-primary" aria-label="%s">%s</a>',
            wp_nonce_url('plugins.php?action=activate&amp;plugin=' . urlencode($plugin) . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $plugin),
            esc_attr($plugin_id),
            esc_attr(sprintf(_x('Activate %s', 'plugin'), $plugin_data['Name'])),
            __('Activate Now')
        );
        $message = '<h3>' . esc_html__("Activate Wordfence Security!", 'wordfence-activator') . '</h3>';
        $message .= '<p>' . esc_html__("Activate the plugin to use all features.", 'wordfence-activator') . '</p>';
        $message .= '<p>' . $activate_action . '</p>';

        add_action('admin_notices', function () use ($message) {
            ?><div class="notice notice-warning"><p><?= $message ?></p></div><?php
        });

        return true;
    }

    return false;
}

// Additional utility functions can be added here.
