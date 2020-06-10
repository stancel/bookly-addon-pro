<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="form-group">
    <label for="bookly_settings_final_step_url_mode"><?php esc_html_e( 'Final step URL', 'bookly' ) ?></label>
    <select class="form-control custom-select" id="bookly_settings_final_step_url_mode">
        <?php foreach ( array( __( 'Disabled', 'bookly' ) => 0, __( 'Enabled', 'bookly' ) => 1 ) as $text => $mode ) : ?>
            <option value="<?php echo esc_attr( $mode ) ?>" <?php selected( get_option( 'bookly_url_final_step_url' ), $mode ) ?> ><?php echo $text ?></option>
        <?php endforeach ?>
    </select>
    <input class="form-control mt-2"
           <?php if ( get_option( 'bookly_url_final_step_url' ) == '' ): ?>style="display: none"<?php endif ?>
           type="text" name="bookly_url_final_step_url"
           value="<?php form_option( 'bookly_url_final_step_url' ) ?>"
           placeholder="<?php esc_attr_e( 'Enter a URL', 'bookly' ) ?>"
    />
    <small class="text-muted form-text"><?php esc_html_e( 'Set the URL of a page that the user will be forwarded to after successful booking. If disabled then the default Done step is displayed.', 'bookly' ) ?></small>
</div>