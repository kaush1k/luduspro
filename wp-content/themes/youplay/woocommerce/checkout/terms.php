<?php
/**
 * Checkout terms and conditions checkbox
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.1.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$terms_page_id = wc_get_page_id( 'terms' );

if ( $terms_page_id > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) :
    $terms         = get_post( $terms_page_id );
    $terms_content = has_shortcode( $terms->post_content, 'woocommerce_checkout' ) ? '' : wc_format_content( $terms->post_content );

    if ( $terms_content ) {
        do_action( 'woocommerce_checkout_before_terms_and_conditions' );
        echo '<div class="woocommerce-terms-and-conditions" style="display: none; max-height: 200px; overflow: auto;">' . $terms_content . '</div>';
    }
    ?>
    <div class="terms wc-terms-and-conditions">
        <div class="youplay-checkbox pull-left pl-20 mb-0">
            <input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
            <label for="terms" style="line-height: normal;">
                <?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'youplay' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?> <span class="required">*</span>
            </label>
        </div>
        <input type="hidden" name="terms-field" value="1" />
    </div>
    <?php do_action( 'woocommerce_checkout_after_terms_and_conditions' ); ?>
<?php endif; ?>
