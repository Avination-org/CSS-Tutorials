<?php if ( trim( ideapark_mod( 'header_address' ) ) ) { ?>
	<li class="c-header__top-row-item c-header__top-row-item--address">
		<i class="ip-map-pin c-header__top-row-icon c-header__top-row-icon--address"></i>
		<?php echo esc_html( trim( ideapark_mod( 'header_address' ) ) ); ?>
	</li>
<?php } ?>