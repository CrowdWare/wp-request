<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
    // Display success messages
    if (isset($_GET['updated'])) {
        echo '<div class="updated"><p>' . __('Note updated successfully.', 'wp-request') . '</p></div>';
    }
    if (isset($_GET['deleted'])) {
        echo '<div class="updated"><p>' . __('Request deleted successfully.', 'wp-request') . '</p></div>';
    }
    
    // Get the requests from the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'wp_requests';
    
    // Pagination settings
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    
    // Get total count
    $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
    $total_pages = ceil($total_items / $per_page);
    
    // Get requests for current page
    $requests = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ),
        ARRAY_A
    );
    
    if ($requests) {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'wp-request'); ?></th>
                    <th><?php _e('Name', 'wp-request'); ?></th>
                    <th><?php _e('Email', 'wp-request'); ?></th>
                    <th><?php _e('Description', 'wp-request'); ?></th>
                    <th><?php _e('Date', 'wp-request'); ?></th>
                    <th><?php _e('Note', 'wp-request'); ?></th>
                    <th><?php _e('Actions', 'wp-request'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request) : ?>
                    <tr>
                        <td><?php echo esc_html($request['id']); ?></td>
                        <td><?php echo esc_html($request['name']); ?></td>
                        <td><?php echo esc_html($request['email']); ?></td>
                        <td><?php echo esc_html($request['description']); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($request['created_at']))); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="request_id" value="<?php echo esc_attr($request['id']); ?>">
                                <input type="text" name="request_note" value="<?php echo esc_attr($request['note']); ?>">
                                <button type="submit" name="update_note" class="button"><?php _e('Update', 'wp-request'); ?></button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="request_id" value="<?php echo esc_attr($request['id']); ?>">
                                <button type="submit" name="delete_request" class="button"><?php _e('Delete', 'wp-request'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php
        // Pagination
        if ($total_pages > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'total' => $total_pages,
                'current' => $current_page,
            ));
            echo '</div></div>';
        }
    } else {
        ?>
        <p><?php _e('No requests found.', 'wp-request'); ?></p>
    <?php
    }
    ?>
</div>
