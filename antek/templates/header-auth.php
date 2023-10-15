<?php if ( ideapark_woocommerce_on() ) { ?>
	<li class="c-header__top-row-item c-header__top-row-item--auth">
		<?php if ( is_user_logged_in() ) { ?>
			<?php
			echo ideapark_wrap( esc_html__( 'Account', 'antek' ), '<a href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" rel="nofollow"><i class="ip-user c-header__top-row-icon c-header__top-row-icon--auth-account"></i>', '</a>' );
		} else { ?>
			<?php
			echo ideapark_wrap( esc_html__( 'Login or Register', 'antek' ), '<a href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" rel="nofollow"><i class="ip-login c-header__top-row-icon c-header__top-row-icon--auth-login"></i>', '</a>' );
		} ?>
	</li>
<?php } ?>


