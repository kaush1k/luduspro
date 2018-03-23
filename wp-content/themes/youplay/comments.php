<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package Youplay
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area comments-block">

	<h2>
		<?php
			printf( // WPCS: XSS OK
				esc_html__('Comments', 'youplay') . (yp_opts('single_post_comments_count') && have_comments() ? ' <small>(%s)</small>' : ''),
				number_format_i18n( get_comments_number() )
			);
		?>
	</h2>

	<?php if ( have_comments() ) : ?>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'youplay' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'youplay' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'youplay' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<?php
			wp_list_comments(array(
				'walker'      => new youplay_walker_comment,
				'short_ping'  => true,
				'avatar_size' => 90
			));
		?>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'youplay' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'youplay' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'youplay' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php else: // have_comments() ?>
		<?php esc_html_e( 'No Comments', 'youplay' ); ?>
	<?php endif; // have_comments() ?>

	<?php
		youplay_comment_form(array(
			'class_submit'         => 'btn btn-default pull-right',
			'comment_field'        =>  '<div class="youplay-textarea"><textarea id="comment" placeholder="' . esc_attr__( 'Your Comment...', 'youplay') . '" name="comment" rows="5" aria-required="true"></textarea></div>',
			'logged_in_as'         => '',
			'comment_notes_before' => '',
			'comment_notes_after'  => ''
		));
	?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'youplay' ); ?></p>
	<?php endif; ?>

</div><!-- #comments -->
