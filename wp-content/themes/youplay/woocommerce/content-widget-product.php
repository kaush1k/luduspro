<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;
?>

<div class="row youplay-side-news">
  <div class="col-xs-3 col-md-4">
    <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="angled-img">
      <div class="img">
        <?php echo $product->get_image(); ?>
      </div>
    </a>
  </div>
  <div class="col-xs-9 col-md-8">
    <h4 class="ellipsis"><a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr( $product->get_name() ); ?>"><?php echo $product->get_name(); ?></a></h4>

    <?php if ( ! empty( $show_rating ) ) : ?>
        <?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
    <?php endif; ?>

    <div class="price"><?php echo $product->get_price_html(); ?></div>

    <?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
  </div>
</div>
