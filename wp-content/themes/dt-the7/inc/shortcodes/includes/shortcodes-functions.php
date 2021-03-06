<?php

// File Security Check.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert dimensions string like '1x1' to array [1, 1].
 * Return [1, 1] if $dimension_string is invalid.
 *
 * @since 6.7.0
 *
 * @param string $dimension_string
 *
 * @return array
 */
function the7_shortcode_decode_image_dimension( $dimension_string ) {
	$dimension_string = strtolower( $dimension_string );
	if ( strpos( $dimension_string, 'x' ) === false ) {
		return array( 1, 1 );
	}

	$dimension_array = array();
	foreach ( array_slice( explode( 'x', $dimension_string ), 0, 2 ) as $dimension ) {
		$dimension_array[] = max( (int) $dimension, 1 );
	}

	return $dimension_array;
}

/**
 * Decode typical shortcode responsive columns attribute and add it to $data_atts_array.
 * By default attribute value is empty string.
 * Added attributes:
 * [
 *  'desktop-columns-num' => '',
 *  'v-tablet-columns-num'' => '',
 *  'h-tablet-columns-num'' => '',
 *  'phone-columns-num'' => '',
 * ]
 *
 * @since 6.7.0
 * @uses  DT_VCResponsiveColumnsParam::decode_columns()
 * @uses  absint()
 *
 * @param array  $data_atts_array
 * @param string $encoded_columns
 *
 * @return array
 */
function the7_shortcode_add_responsive_columns_data_attributes( $data_atts_array, $encoded_columns ) {
	$columns     = DT_VCResponsiveColumnsParam::decode_columns( $encoded_columns );
	$columns_map = array(
		'desktop'  => 'desktop',
		'v_tablet' => 'v-tablet',
		'h_tablet' => 'h-tablet',
		'phone'    => 'phone',
	);

	foreach ( $columns_map as $column_name => $data_att_name ) {
		$data_atts_array["{$data_att_name}-columns-num"] = isset( $columns[ $column_name ] ) ? absint( $columns[ $column_name ] ) : '';
	}

	return $data_atts_array;
}

function the7_get_custom_icons_stylesheets( $icons_stylesheets = array() ) {
	$custom_icons = get_option( 'smile_fonts' );
	$upload_dir = wp_get_upload_dir();

	foreach( $custom_icons as $icon ) {
		$icons_stylesheets[] = $upload_dir['baseurl'] . '/smile_fonts/' . $icon['style'];
	}

	return $icons_stylesheets;
}