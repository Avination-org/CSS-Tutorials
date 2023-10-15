<?php if ( trim( ideapark_mod( 'header_email' ) ) ) { ?>
	<li class="c-header__top-row-item c-header__top-row-item--email">
		<i class="ip-email c-header__top-row-icon c-header__top-row-icon--email"></i>
		<?php echo make_clickable( esc_html( trim( ideapark_mod( 'header_email' ) ) ) ); ?>
	</li>
<?php } ?>