<div class="c-header__main-row-item c-header__main-row-item--menu">
	<?php echo str_replace( '<nav', '<nav itemscope itemtype="http://schema.org/SiteNavigationElement"', wp_nav_menu( [
		'container'       => 'nav',
		'container_class' => 'c-top-menu js-top-menu',
		'echo'            => false,
		'menu_id'         => 'top-menu',
		'menu_class'      => 'c-top-menu__list' . ( ideapark_mod( 'top_menu_third' ) ? ' c-top-menu__list--third' : '' ),
		'theme_location'  => 'primary',
		'fallback_cb'     => '',
		'depth'           => ideapark_mod( 'top_menu_third' ) ? 3 : 2
	] ) ); ?>
</div>