<aside id="sidebar"
	   class="c-catalog-sidebar js-popup-sidebar <?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar' ); ?>">
	<div class="c-catalog-sidebar__shadow"></div>
	<div class="c-catalog-sidebar__wrap">
		<div class="c-catalog-sidebar__buttons">
			<button type="button" class="h-cb c-catalog-sidebar__close"
					id="ideapark-shop-sidebar-close"><i class="ip-close"><!-- --></i></button>
		</div>
		<div class="c-sidebar c-catalog-sidebar__content">
			<div class="c-sidebar-wrap">
				<?php dynamic_sidebar( 'catalog-sidebar' ); ?>
				<div class="c-catalog-sidebar__sticky" id="filter-sticky">
					<button class="c-button c-button--default c-button--full c-catalog-sidebar__apply js-sidebar-filter-btn"
							type="button"><?php esc_html_e( 'Apply Filters', 'antek' ) ?></button>
				</div>
				<div id="filter-bottom"></div>
				<input type="hidden"
					   class="js-catalog-url"
					   value="<?php echo esc_attr( get_post_type_archive_link( 'catalog' ) ); ?>"/>
			</div>
		</div>
	</div>
</aside>