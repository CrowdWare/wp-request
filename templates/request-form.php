<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wp-request-form-container">
    <?php
    // Display success message if form was submitted successfully
    if (get_transient('wp_request_form_success')) {
        delete_transient('wp_request_form_success');
        ?>
        <div class="wp-request-success-message">
            <p><?php _e('Your request has been submitted successfully.', 'wp-request'); ?></p>
        </div>
        <?php
    }
    
    // Display error messages if there are any
    if (isset($_GET['wp_request_error']) && $_GET['wp_request_error'] == '1') {
        $errors = get_transient('wp_request_form_errors');
        if ($errors && is_array($errors)) {
            delete_transient('wp_request_form_errors');
            ?>
            <div class="wp-request-error-message">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo esc_html($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        }
    }
    ?>
    
    <form method="post" action="" class="wp-request-form">
        <?php wp_nonce_field('wp_request_form', 'wp_request_nonce'); ?>
        <h2>Project Request</h2>
        <p>Please fill out the form, tell us about your vision and let us know how we can help you.
            Additionally please send us an <a href="mailto:art@crowdware.info?subject=Project%20Request">email</a>, we might not look into this website every day.
        </p>
        <div class="wp-request-form-field">
            <label for="wp_request_name"><?php _e('Name', 'wp-request'); ?> <span class="required">*</span></label>
            <input type="text" name="wp_request_name" id="wp_request_name" value="<?php echo isset($_POST['wp_request_name']) ? esc_attr($_POST['wp_request_name']) : ''; ?>" required>
        </div>
        
        <div class="wp-request-form-field">
            <label for="wp_request_email"><?php _e('Email', 'wp-request'); ?> <span class="required">*</span></label>
            <input type="email" name="wp_request_email" id="wp_request_email" value="<?php echo isset($_POST['wp_request_email']) ? esc_attr($_POST['wp_request_email']) : ''; ?>" required>
        </div>
        
        <div class="wp-request-form-field">
            <label for="wp_request_description"><?php _e('Description', 'wp-request'); ?> <span class="required">*</span></label>
            <textarea name="wp_request_description" id="wp_request_description" rows="5" required><?php echo isset($_POST['wp_request_description']) ? esc_textarea($_POST['wp_request_description']) : ''; ?></textarea>
        </div>
        
        <div class="wp-request-form-submit">
            <button type="submit" name="wp_request_submit" class="wp-request-submit-button"><?php _e('Submit Request', 'wp-request'); ?></button>
        </div>
    </form>
</div>
