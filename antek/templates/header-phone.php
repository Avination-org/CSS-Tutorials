<?php if ( trim( ideapark_mod( 'header_phone' ) ) ) { ?>
	<li class="c-header__top-row-item c-header__top-row-item--phone">
		<i class="ip-phone c-header__top-row-icon c-header__top-row-icon--phone"></i>
		<?php echo ideapark_phone_wrap( esc_html( trim( ideapark_mod( 'header_phone' ) ) ) ); ?>
	</li>
<?php } ?>