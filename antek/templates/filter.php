<?php

$col = (int) ( ideapark_woocommerce_on() && ! ideapark_mod( 'disable_booking' ) );
if ( ideapark_mod( 'filter_location' ) && taxonomy_exists( 'location' ) ) {
	$locations = get_terms( [
		'taxonomy'   => 'location',
		'hide_empty' => false,
	] );
	if ( ! empty( $locations ) && sizeof( $locations ) > 1 ) {
		$col ++;
	}
}

$category_list = ideapark_mod( 'filter_type' ) && function_exists( 'ideapark_get_categories' ) ? ideapark_get_categories( false, ideapark_mod( 'filter_type_only_top_level' ) ) : [];

if ( ! empty( $category_list ) && sizeof( $category_list ) > 1 ) {
	$col ++;
}

if ( ! $col ) {
	return;
}

if ( ( $queried_object = get_queried_object() ) && isset( $queried_object->taxonomy ) && ( $queried_object->taxonomy == 'vehicle_type' ) ) {
	$current_vehicle_type_id = $queried_object->term_id;
} else {
	$current_vehicle_type_id = false;
}

if ( ! empty( $ideapark_var['layout'] ) ) {
	$filter_layout = $ideapark_var['layout'];
} else {
	$filter_layout = 'widget';
}

/*
 * @var $term WP_Term;
 * */
$form_action   = $current_vehicle_type_id ? get_term_link( $current_vehicle_type_id, 'vehicle_type' ) : get_post_type_archive_link( 'catalog' );
$u             = wp_parse_url( $form_action );
$hidden_params = [];
if ( ! empty( $u['query'] ) ) {
	parse_str( $u['query'], $hidden_params );
}

$dates              = function_exists( 'ideapark_get_filter_dates_range' ) ? ideapark_get_filter_dates_range( false, true, true ) : [];
$default_date_start = '';
$default_date_end   = '';
if ( ! empty( $dates['start'] ) && ! empty( $dates['end'] ) ) {
	$default_date_start = $dates['start']->format( ideapark_date_format() );
	$default_date_end   = $dates['end']->format( ideapark_date_format() );
}

$field_location   = '';
$field_date_range = '';
$field_type       = '';
$field_delivery   = '';

if ( ! empty( $locations ) && sizeof( $locations ) > 1 ) {
	ob_start();
	?>
	<div class="c-filter__field c-filter__field--<?php echo esc_attr( $filter_layout ); ?>">
		<span class="c-filter__icon"><?php if ( ideapark_mod( 'filter_location_icon' ) ) { ?><img
				src="<?php echo esc_url( ideapark_mod( 'filter_location_icon' ) ); ?>"
				alt="<?php esc_attr_e( 'Location', 'antek' ); ?>" class="c-filter__icon-custom"/><?php } else { ?><i
				class="ip-location c-filter__icon-location"></i><?php } ?></span>
		<select name="pickup"
				class="c-filter__select c-filter__select--<?php echo esc_attr( $filter_layout ); ?> <?php ideapark_class( ! empty( $_REQUEST['pickup'] ), 'c-filter__select--active js-filter-field' ); ?> js-filter-pickup">
			<option value=""><?php esc_html_e( 'Select Location', 'antek' ); ?></option>
			<?php foreach ( $locations as $term ) { ?>
				<option value="<?php echo esc_attr( $term->term_id ); ?>"
				        <?php if ( isset( $_REQUEST['pickup'] ) && $_REQUEST['pickup'] == $term->term_id ) { ?>selected<?php } ?>><?php echo esc_html( $term->name ); ?></option>
			<?php } ?>
		</select>
	</div>
	<?php
	$field_location = ob_get_clean();
}

if ( ideapark_woocommerce_on() && ! ideapark_mod( 'disable_booking' ) ) {
	ob_start();
	?>
	<div class="c-filter__field c-filter__field--<?php echo esc_attr( $filter_layout ); ?>">
		<span class="c-filter__icon"><?php if ( ideapark_mod( 'filter_range_icon' ) ) { ?><img
				src="<?php echo esc_url( ideapark_mod( 'filter_range_icon' ) ); ?>"
				alt="<?php esc_attr_e( 'Date range', 'antek' ); ?>" class="c-filter__icon-custom"/><?php } else { ?><i
				class="ip-calendar c-filter__icon-calendar"></i><?php } ?></span>
		<input type="hidden"
			   class="js-filter-date-start <?php ideapark_class( ! empty( $_REQUEST['start'] ), 'js-filter-field' ); ?>"
			   name="start" value="" data-value="<?php echo esc_attr( $default_date_start ) ?>"/>
		<input type="hidden"
			   class="js-filter-date-end <?php ideapark_class( ! empty( $_REQUEST['end'] ), 'js-filter-field' ); ?>"
			   name="end" value="" data-value="<?php echo esc_attr( $default_date_end ) ?>"/>
		<input type="text"
			   class="c-filter__date c-filter__date--<?php echo esc_attr( $filter_layout ); ?> js-filter-date-range <?php ideapark_class( ! empty( $_REQUEST['start'] ) || ! empty( $_REQUEST['end'] ), 'c-filter__date--active' ); ?>"
			   value="<?php if ( $default_date_start && $default_date_end ) {
			       echo ideapark_wrap( '  —  ', $default_date_start, $default_date_end );
		       } ?>" readonly/>
	</div>
	<?php
	$field_date_range = ob_get_clean();
}

if ( ! empty( $category_list ) && sizeof( $category_list ) > 1 ) {
	ob_start();
	?>
	<div class="c-filter__field c-filter__field--<?php echo esc_attr( $filter_layout ); ?>">
		<span class="c-filter__icon"><?php if ( ideapark_mod( 'filter_type_icon' ) ) { ?><img
				src="<?php echo esc_url( ideapark_mod( 'filter_type_icon' ) ); ?>"
				alt="<?php esc_attr_e( 'Category', 'antek' ); ?>" class="c-filter__icon-custom"/><?php } else { ?><i
				class="ip-truck c-filter__icon-truck"></i><?php } ?></span>
		<select
			class="c-filter__select c-filter__select--<?php echo esc_attr( $filter_layout ); ?> <?php ideapark_class( ! empty( $current_vehicle_type_id ), 'c-filter__select--active' ); ?> js-filter-type">
			<option
				data-id="0"
				value="<?php echo esc_attr( get_post_type_archive_link( 'catalog' ) ); ?>"><?php esc_html_e( 'Select Category', 'antek' ); ?></option>
			<?php foreach ( $category_list as $term_id => $term_name ) { ?>
				<option
					data-id="<?php echo esc_attr( $term_id ); ?>"
					value="<?php echo esc_attr( get_term_link( $term_id, 'vehicle_type' ) ); ?>"
					<?php if ( $current_vehicle_type_id == $term_id ) { ?>selected<?php } ?>><?php echo esc_html( $term_name ); ?></option>
			<?php } ?>
		</select>
	</div>
	<?php
	$field_type = ob_get_clean();
}

if ( ideapark_mod( '_delivery_on' ) && ideapark_mod( 'filter_delivery' ) && ! ideapark_mod( 'disable_self_pickup' ) ) {
	ob_start(); ?>
	<div class="c-filter__delivery-wrap c-filter__delivery-wrap--<?php echo esc_attr( $filter_layout ); ?>">
		<span class="c-filter__delivery-col">
			<label>
				<input type="radio" class="c-filter__radio js-filter-field" name="delivery"
				       <?php if ( empty( $_REQUEST['delivery'] ) ) { ?>checked<?php } ?> value="0"/><?php esc_html_e( 'Self-Pickup', 'antek' ) ?>
			</label>
		</span>
		<span class="c-filter__delivery-col">
			<label>
				<input type="radio" class="c-filter__radio js-filter-field" name="delivery"
				       <?php if ( ! empty( $_REQUEST['delivery'] ) ) { ?>checked<?php } ?> value="1"/><?php esc_html_e( 'Owner Delivery', 'antek' ) ?>
			</label>
		</span>
	</div>
	<?php

	$field_delivery = ob_get_clean();
}

ob_start(); ?>
<button type="submit"
		class="h-cb c-filter__button c-filter__button--<?php echo esc_attr( $filter_layout ); ?> js-filter-button"><?php echo esc_html( ideapark_mod( 'filter_button_text' ) ) ?>
	<i class="ip-double-arrow c-filter__button-arrow"></i></button>
<?php
$field_button = ob_get_clean();

?>
<form method="GET"
	  action="<?php echo esc_url( $form_action ); ?>"
	  class="c-filter c-filter--col-<?php echo esc_attr( $col ); ?> c-filter--<?php echo esc_attr( $filter_layout ); ?> js-filter-form <?php ideapark_class( ! empty( $_REQUEST['pickup'] ) || ! empty( $_REQUEST['start'] ) || ! empty( $_REQUEST['end'] ), 'js-filter-form--active' ); ?>">
	<?php foreach ( $hidden_params as $param_name => $param_value ) { ?>
		<input class="js-filter-permalink" type="hidden" name="<?php echo esc_attr( $param_name ); ?>"
			   value="<?php echo esc_attr( $param_value ); ?>"/>
	<?php } ?>

	<?php if ( $filter_layout == 'header' ) { ?>
		<div class="c-filter__row-bar">
			<div class="c-filter__stretch-bar c-filter__stretch-bar--<?php echo esc_attr( $filter_layout ); ?>"></div>
		</div>
	<?php } ?>
	<div class="c-filter__fields c-filter__fields--<?php echo esc_attr( $filter_layout ); ?>">
		<?php echo ideapark_wrap( $field_type ); ?>
		<?php echo ideapark_wrap( $field_location ); ?>
		<?php echo ideapark_wrap( $field_date_range ); ?>
		<?php echo ideapark_wrap( $field_button ); ?>
		<?php echo ideapark_wrap( $field_delivery ); ?>
	</div>
</form>
<?php if ( $filter_layout == 'widget' ) { ?>
	<div class="c-filter__stretch-bar c-filter__stretch-bar--<?php echo esc_attr( $filter_layout ); ?>"></div>
<?php } ?>
