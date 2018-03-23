<?php
/**
 * Show messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>

<?php foreach ( $messages as $message ) :
  $msg = str_replace('button wc-forward', 'btn btn-sm', wp_kses_post( $message ));
  ?>
  <div class="alert"><?php echo wp_kses_post($msg); ?></div>
<?php endforeach; ?>