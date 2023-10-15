<?php if ( trim( ideapark_mod( 'header_callback' ) ) ) { ?>
	<li class="c-header__top-row-item c-header__top-row-item--callback">
		<i class="ip-hands-free c-header__top-row-icon c-header__top-row-icon--callback"></i>
		<a href="" onclick="return false;"
		   class="c-header__callback-btn js-callback">
			<?php echo esc_html( ideapark_mod( 'header_callback' ) ); ?>
		</a>
	</li>
<?php } ?>
