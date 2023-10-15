<div
	class="c-header__logo">
	<?php if ( ! is_front_page() ): ?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php endif ?>

		<?php if ( ideapark_mod( 'logo__width' ) && ideapark_mod( 'logo__height' ) ) {
			$dimension = ' width="' . ideapark_mod( 'logo__width' ) . '" height="' . ideapark_mod( 'logo__height' ) . '" ';
		} else {
			$dimension = '';
		}

		?>
		<?php if ( ideapark_mod( 'logo_mobile' ) ) { ?>
			<img <?php echo ideapark_wrap( $dimension ); ?>
				src="<?php echo esc_url( ideapark_mod( 'logo_mobile' ) ); ?>"
				alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				class="c-header__logo-img c-header__logo-img--mobile"/>
		<?php } elseif ( ideapark_mod( 'logo' ) ) { ?>
			<img <?php echo ideapark_wrap( $dimension ); ?>
				src="<?php echo esc_url( ideapark_mod( 'logo' ) ); ?>"
				alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				class="c-header__logo-img c-header__logo-img--all"/>
		<?php } else { ?>
			<span
				class="c-header__logo-empty"><?php echo esc_html( trim( ideapark_truncate( get_bloginfo( 'name', 'display' ), 10, '' ), " -/.,\r\n\t" ) ); ?></span>
		<?php } ?>

		<?php if ( ! is_front_page() ): ?></a><?php endif ?>
</div>
