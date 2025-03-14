<?php
/**
 * Plugin Name: WP Request
 * Plugin URI: 
 * Description: A plugin to handle user requests through a form and display them in the admin panel.
 * Version: 1.0.2
 * Author: 
 * Author URI: 
 * Text Domain: wp-request
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WP_REQUEST_VERSION', '1.0.2');
define('WP_REQUEST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_REQUEST_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_wp_request() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_requests';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        description text NOT NULL,
        note varchar(255) DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'activate_wp_request');

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wp_request() {
    // Nothing to do here for now
}
register_deactivation_hook(__FILE__, 'deactivate_wp_request');

/**
 * Enqueue plugin styles
 */
function wp_request_enqueue_styles() {
    wp_enqueue_style('wp-request-style', WP_REQUEST_PLUGIN_URL . 'assets/css/wp-request.css', array(), WP_REQUEST_VERSION);
}
add_action('wp_enqueue_scripts', 'wp_request_enqueue_styles');

/**
 * Register the shortcode for the request form
 */
function wp_request_form_shortcode() {
    ob_start();
    include(WP_REQUEST_PLUGIN_DIR . 'templates/request-form.php');
    return ob_get_clean();
}
add_shortcode('wp_request_form', 'wp_request_form_shortcode');

/**
 * Handle form submission
 */
function wp_request_handle_form_submission() {
    if (isset($_POST['wp_request_submit']) && wp_verify_nonce($_POST['wp_request_nonce'], 'wp_request_form')) {
        $name = sanitize_text_field($_POST['wp_request_name']);
        $email = sanitize_email($_POST['wp_request_email']);
        $description = sanitize_textarea_field($_POST['wp_request_description']);
        
        // Validate form data
        $errors = array();
        
        if (empty($name)) {
            $errors[] = __('Name is required.', 'wp-request');
        }
        
        if (empty($email)) {
            $errors[] = __('Email is required.', 'wp-request');
        } elseif (!is_email($email)) {
            $errors[] = __('Email is not valid.', 'wp-request');
        }
        
        if (empty($description)) {
            $errors[] = __('Description is required.', 'wp-request');
        }
        
        if (empty($errors)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'wp_requests';
            
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $name,
                    'email' => $email,
                    'description' => $description,
                )
            );
            
            // Set success message
            set_transient('wp_request_form_success', true, 60);
            
            // Redirect to the same page to prevent form resubmission
            wp_redirect(remove_query_arg('wp_request_error'));
            exit;
        } else {
            // Set error message
            set_transient('wp_request_form_errors', $errors, 60);
            
            // Redirect to the same page with error parameter
            wp_redirect(add_query_arg('wp_request_error', '1'));
            exit;
        }
    }
}
add_action('init', 'wp_request_handle_form_submission');

/**
 * Add admin menu page
 */
function wp_request_add_admin_menu() {
    add_menu_page(
        __('WP Requests', 'wp-request'),
        __('WP Requests', 'wp-request'),
        'manage_options',
        'wp-requests',
        'wp_request_admin_page',
        'dashicons-feedback',
        30
    );
}
add_action('admin_menu', 'wp_request_add_admin_menu');

/**
 * Display admin page
 */
function wp_request_admin_page() {
    global $wpdb; // Ensure $wpdb is accessible
    $table_name = $wpdb->prefix . 'wp_requests'; // Define $table_name here
    if (isset($_POST['update_note'])) {
        $request_id = intval($_POST['request_id']);
        $request_note = sanitize_text_field($_POST['request_note']);
        
        // Debugging output for the request ID and note
        echo "Updating request ID: $request_id with note: $request_note<br>";
        
        $result = $wpdb->update(
            $table_name,
            array('note' => $request_note),
            array('id' => $request_id)
        );
        
        // Debugging output
        echo "Update result: " . ($result ? "Success" : "Failed") . "<br>";
        
        // Redirect to the same page to prevent form resubmission
        wp_redirect(add_query_arg('updated', '1'));
        exit;
    }

    if (isset($_POST['delete_request'])) {
        $request_id = intval($_POST['request_id']);
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $request_id)
        );
        
        // Debugging output
        echo "Delete result: " . ($result ? "Success" : "Failed") . "<br>";
        
        // Redirect to the same page to prevent form resubmission
        wp_redirect(add_query_arg('deleted', '1'));
        exit;
    }

    include(WP_REQUEST_PLUGIN_DIR . 'templates/admin-page.php');
}
