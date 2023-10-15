<?php
if ( ! $content = ideapark_mod( '_soc_cache' ) ) {

	global $ideapark_customize;
	$soc_list  = [];
	$soc_count = 0;

	if ( ! empty( $ideapark_customize ) ) {
		foreach ( $ideapark_customize AS $section ) {
			if ( ! empty( $section['controls'] ) && array_key_exists( 'facebook', $section['controls'] ) ) {
				foreach ( $section['controls'] AS $key => $control ) {
					if ( ! empty( $control['type'] ) && $control['type'] == 'text' ) {
						$soc_list[] = $key;
					}
				}
				break;
			}
		}
	}
	ob_start();
	foreach ( $soc_list AS $soc_name ) {
		if ( ideapark_mod( $soc_name ) ): $soc_count ++; ?>
			<a href="<?php echo esc_url( ideapark_mod( $soc_name ) ); ?>" class="c-soc__link" target="_blank"><i
					class="ip-<?php echo esc_attr( $soc_name ) ?> c-soc__icon c-soc__icon--<?php echo esc_attr( $soc_name ) ?>">
					<!-- --></i></a>
		<?php endif;
	}
	$content = ob_get_contents();
	ob_end_clean();
	ideapark_mod_set_temp( '_soc_cache', $content );
}
?>
<?php echo ideapark_wrap( $content, '<div class="c-soc' . ( ! empty( $ideapark_var['class'] ) ? ' ' . esc_attr( $ideapark_var['class'] ) : '' ) . '">', '</div>' ) ?>