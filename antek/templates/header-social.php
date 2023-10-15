<?php ob_start();
get_template_part( 'templates/soc' );
$content = trim( ob_get_clean() ); ?>
<?php if ( $content ) { ?>
	<li class="c-header__top-row-item c-header__top-row-item--social">
		<?php echo ideapark_wrap( $content ); ?>
	</li>
<?php } ?>