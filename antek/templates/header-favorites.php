<li class="c-header__top-row-item c-header__top-row-item--favorites<?php if ( ! empty( $_COOKIE['ip-favorites'] ) && json_decode( stripslashes( $_COOKIE['ip-favorites'] ), true ) ) { ?> active<?php } ?>">
	<?php
	echo ideapark_wrap( esc_html__( 'Favorites', 'antek' ), '<a href="' . esc_url( add_query_arg( 'favorites', '', get_post_type_archive_link( 'catalog' ) ) ) . '" rel="nofollow"><i class="ip-star-outline c-header__top-row-icon c-header__top-row-icon--favorites"></i>', '</a>' );
	?>
</li>