<div class="c-header__mobile_blocks">
	<div class="c-header__top js-mobile-blocks">
		<?php
		ob_start();
		$show_social = false;
		$header_blocks = ideapark_parse_checklist( ideapark_mod( 'header_blocks' ) );
		foreach ( $header_blocks as $block_index => $enabled ) {
			if ( $enabled ) {
				if ( $block_index == 'menu' || $block_index == 'auth' ) {
					continue;
				} elseif ( $block_index == 'social' ) {
					$show_social = true;
				} else {
					get_template_part( 'templates/header-' . $block_index );
				}
			}
		}
		$content = trim( ob_get_clean() );
		?>
		<?php get_template_part( 'templates/header-support' ); ?>
		<?php if ( $content ) { ?>
			<ul class="c-header__top-row-list c-header__top-row-list--<?php echo esc_attr( ideapark_mod( 'header_blocks_layout' ) ); ?>">
				<?php
				echo ideapark_wrap( $content );
				?>
			</ul>
		<?php } ?>
		<?php if ( $show_social ) { ?>
			<?php ideapark_get_template_part( 'templates/soc' ); ?>
		<?php } ?>
	</div>
</div>