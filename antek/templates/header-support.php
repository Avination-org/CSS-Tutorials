<?php if ( ideapark_mod( 'header_support' ) ) { ?>
	<div class="c-header__main-row-item c-header__main-row-item--support">
		<div class="c-header__support">
			<i class="ip-hands-free c-header__support-icon"></i>
			<div class="c-header__support-content">
				<?php echo ideapark_wrap( esc_html(ideapark_mod( 'header_support_title' )), '<div class="c-header__support-title">' ,'</div>' ) ?>
				<?php echo ideapark_phone_wrap( esc_html( ideapark_mod( 'header_support_phone' ) ), '<div class="c-header__support-phone">', '</div>' ); ?>
			</div>
		</div>
	</div>
<?php } ?>
