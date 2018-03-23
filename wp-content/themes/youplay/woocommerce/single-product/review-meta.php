<?php
/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly.
}

global $comment;
$verified = wc_review_is_from_verified_owner( $comment->comment_ID );

if ( $comment->comment_approved === '0' ) : ?>

    <p class="meta"><em><?php _e( 'Your review is awaiting approval', 'youplay' ); ?></em></p>

<?php else : ?>
    <span class="review-author h4"><?php comment_author(); ?></span>
     <?php

         if ( get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' && $verified )
            echo '<em class="verified">(' . __( 'verified owner', 'youplay' ) . ')</em> ';
    ?>

    <div class="date" itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' ); ?>"><i class="fa fa-calendar"></i> <?php echo get_comment_date( wc_date_format() ); ?></div>

<?php endif; ?>
