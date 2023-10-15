<?php if ( trim( ideapark_mod( 'header_other' ) ) ) { ?>
	<li class="c-header__top-row-item c-header__top-row-item--other">
		<i class="ip-hand c-header__top-row-icon c-header__top-row-icon--other"></i>
		<?php echo do_shortcode( esc_html( trim( ideapark_mod( 'header_other' ) ) ) ); ?>
	</li>
<?php } ?>