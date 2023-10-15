<div class="c-header__main-row-item">
	<div
		class="c-header__logo <?php if ( ideapark_mod( 'header_type' ) == 'header-type-4' ) { ?> c-header__logo--row-1<?php } ?>">
		<?php if ( ! is_front_page() ): ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php endif ?>

			<?php if ( ideapark_mod( 'logo__width' ) && ideapark_mod( 'logo__height' ) ) {
				$dimension = ' width="' . ideapark_mod( 'logo__width' ) . '" height="' . ideapark_mod( 'logo__height' ) . '" ';
			} else {
				$dimension = '';
			} ?>
			<?php if ( ideapark_mod( 'logo' ) ) { ?>
				<img <?php echo ideapark_wrap( $dimension ); ?>
					src="<?php echo esc_url( ideapark_mod( 'logo' ) ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					class="c-header__logo-img <?php ideapark_class( ideapark_mod( 'logo_mobile' ), 'c-header__logo-img--desktop', 'c-header__logo-img--all' ); ?>"/>
			<?php } else { ?>
				<span
					class="c-header__logo-empty"><?php echo esc_html( trim( ideapark_truncate( get_bloginfo( 'name' ), 10, '' ), " -/.,\r\n\t" ) ); ?></span>
			<?php } ?>

			<?php if ( ! is_front_page() ): ?></a><?php endif ?>
	</div>
</div>
