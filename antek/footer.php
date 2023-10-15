<?php
$page_id = apply_filters( 'wpml_object_id', ideapark_mod( 'footer_page' ), 'any' );
if ( 'publish' != ideapark_post_status( $page_id ) ) {
	$page_id = 0;
}
?>
</div><!-- /.l-inner -->
<footer
	class="l-section c-footer<?php ideapark_class( ! $page_id && ideapark_mod( 'footer_copyright' ), 'c-footer--simple' ); ?>">
	<?php if ( $page_id ) {
		if (  ideapark_is_elementor_page( $page_id ) ) {
			$page_content = Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $page_id );
			$is_elementor = true;
		} elseif ( $post = get_post( $page_id ) ) {
			$page_content = apply_filters( 'the_content', $post->post_content );
			$page_content = str_replace( ']]>', ']]&gt;', $page_content );
			$page_content = ideapark_wrap( $page_content, '<div class="entry-content">', '</div>' );
			wp_reset_postdata();
		} else {
			$page_content = '';
		}
		echo ideapark_wrap( $page_content, '<div class="l-section">', '</div>' );
	} else { ?>
		<div class="l-section__container">
			<?php if ( ideapark_mod( 'footer_copyright' ) ) { ?>
				<?php get_template_part( 'templates/footer-copyright' ); ?>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if ( ideapark_is_elementor_preview_mode() && ideapark_mod( 'footer_page' ) ) { ?>
		<a onclick="window.open('<?php echo esc_url( esc_url( admin_url( 'post.php?post=' . ideapark_mod( 'footer_page' ) . '&action=' . ( ! empty( $is_elementor ) ? 'elementor' : 'edit' ) ) ) ); ?>', '_blank').focus();"
		   href=""
		   class="h-footer-edit">
			<i>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 862 1000">
					<path
						d="M725 324L529 127l67-67c55-53 109-83 155-38l79 80c53 52 15 101-38 155zM469 187l196 196-459 459H13V646zM35 1000a35 35 0 0 0 0-70h792a35 35 0 0 0 0 70z"
						fill="white"/>
				</svg>
			</i>
		</a>
	<?php } ?>
</footer>
</div><!-- /.l-wrap -->
<?php get_template_part( 'templates/pswp' ); ?>
<?php if ( ideapark_mod( 'to_top_button' ) ) { ?>
	<button class="c-to-top-button js-to-top-button c-to-top-button--without-menu" type="button">
		<i class="ip-down_arrow c-to-top-button__svg"></i>
	</button>
<?php } ?>
<?php wp_footer(); ?>
</body>
</html>
