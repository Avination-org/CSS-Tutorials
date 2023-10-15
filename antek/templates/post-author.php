<?php
global $ideapark_customize;

$description = get_the_author_meta( 'description' );
$author_soc  = "";

if ( ! empty( $ideapark_customize ) ) {
	ob_start();
	foreach ( $ideapark_customize as $section ) {
		if ( ! empty( $section['controls'] ) && array_key_exists( 'facebook', $section['controls'] ) ) {
			foreach ( $section['controls'] as $control_name => $control ) { ?>
				<?php if ( strpos( $control_name, 'soc_' ) === false && get_the_author_meta( $control_name ) ) { ?>
					<a href="<?php echo esc_url( get_the_author_meta( $control_name ) ); ?>" class="c-soc__link" target="_blank">
						<i class="ip-<?php echo esc_attr( $control_name ) ?> c-soc__icon c-soc__icon--<?php echo esc_attr( $control_name ) ?>">
							<!-- --></i>
					</a>
				<?php } ?>
			<?php }
		}
	}
	$author_soc = trim( ob_get_clean() );
}
if ( $description || $author_soc ) { ?>
	<div class="c-post__decor"></div>
	<div class="c-post__author">
		<div class="c-post__author-thumb">
			<?php echo get_avatar( get_the_author_meta( 'email' ), 155 ); ?>
		</div>
		<div class="c-post__author-content">
			<div class="c-post__author-title"><?php the_author_posts_link(); ?></div>
			<?php echo ideapark_wrap( $description, '<div class="c-post__author-desc">', '</div>' ); ?>
			<?php echo ideapark_wrap( $author_soc, '<div class="c-soc c-post__author-soc">', '</div>' ); ?>
		</div>
	</div>
<?php }