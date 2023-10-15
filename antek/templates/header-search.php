<div id="ideapark-ajax-search" class="c-header-search c-header-search--disabled">
	<div class="c-header-search__shadow"></div>
	<div class="c-header-search__wrap" id="ideapark-ajax-search-wrap">
		<div class="c-header-search__form">
			<div class="c-header-search__tip"><?php esc_html_e( 'What are you looking for?', 'antek' ); ?></div>
			<?php ideapark_af( 'get_search_form', 'ideapark_search_form_ajax', 100 ); ?>
			<?php get_search_form(); ?>
			<?php ideapark_rf( 'get_search_form', 'ideapark_search_form_ajax', 100 ); ?>
		</div>
		<div class="l-section l-section--container c-header-search__result" id="ideapark-ajax-search-result">

		</div>
		<button type="button" class="h-cb h-cb--svg c-header-search__close" id="ideapark-ajax-search-close"><i
				class="ip-close"></i></button>
	</div>
</div>
