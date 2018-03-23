<?php
/**
 * @package Youplay
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class("news-one"); ?>>
    <div class="row vertical-gutter">
        <div class="col-md-4">
            <?php
            $additional_info = '';

            if ( 'post' == get_post_type() ) :
                $additional_info = '<div class="over-info bottom h4">' . youplay_posted_on( true, true, false ) . '</div>';
            endif;

            youplay_post_thumbnail( false, $additional_info );
            ?>
        </div>
        <div class="col-md-8">
            <div class="entry-header clearfix">
                <?php the_title( sprintf( '<h2 class="entry-title pull-left m-0"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
            </div>
            <div class="tags">
                <?php youplay_post_tags(); ?>
            </div>
            <div class="entry-content description">
                <?php
                the_excerpt();
                ?>

                <?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'youplay' ),
                    'after'  => '</div>',
                    ) );
                    ?>
                </div>
                <?php youplay_read_more(); ?>

                <?php youplay_entry_footer(); ?>
            </div>
        </div>
</div>
