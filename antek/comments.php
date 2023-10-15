<?php
/*
* If the current post is protected by a password and the visitor has not yet
* entered the password we will return early without loading the comments.
*/
if ( post_password_required() ) {
	return;
}
?>

<?php if ( comments_open() || have_comments() ) { ?>
	<div class="c-post__decor"></div>
	<div id="comments" class="c-post__comments">

		<?php if ( have_comments() ) : ?>

			<h2 class="comments-title">
				<?php
				printf( esc_html( _n( '1 Comment', '%1$s Comments', get_comments_number(), 'antek' ) ),
					number_format_i18n( get_comments_number() ), get_the_title() );
				?>
			</h2>
			<div class="c-post__divider"><i class="ip-decor"></i></div>

			<ol class="commentlist">
				<?php
				wp_list_comments( [
					'avatar_size' => 90,
					'max_depth'   => 3,
					'callback'    => 'ideapark_html5_comment',
					'type'        => 'all',
					'style'       => 'ol',
					'short_ping'  => true,
					'format'      => 'html5',
				] );
				?>
			</ol><!-- .comments-list -->

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
				<nav id="comments-nav-below" class="comments-navigation" role="navigation">
					<div
						class="nav-previous"><?php previous_comments_link( '<span class="meta-nav"><i class="ip-double-arrow"></i></span>' . esc_html__( 'Older Comments', 'antek' ) ); ?></div>
					<div
						class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'antek' ) . '<span class="meta-nav"><i class="ip-double-arrow"></i></span>' ); ?></div>
				</nav><!-- #comments-nav-below -->
			<?php endif; // Check for comment navigation. ?>

		<?php endif; // have_comments() ?>

		<?php comment_form([
			'title_reply_after' => '</h3>',
			'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s c-button c-button--default">%4$s<i class="ip-double-arrow c-button__arrow"></i></button>'
		]); ?>

	</div><!-- #comments -->
<?php } ?>
