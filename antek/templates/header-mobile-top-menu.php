<?php

$locs     = get_nav_menu_locations();
$location = ! empty( $locs['mobile'] ) ? 'mobile' : 'primary';

echo str_replace( '<nav', '<nav itemscope itemtype="http://schema.org/SiteNavigationElement"', wp_nav_menu( [
	'container'       => 'nav',
	'container_class' => 'c-mobile-menu c-mobile-menu--top-menu js-mobile-top-menu',
	'echo'            => false,
	'menu_id'         => 'mobile-top-menu',
	'menu_class'      => 'c-mobile-menu__list',
	'fallback_cb'     => '',
	'theme_location'  => $location,
] ) );
